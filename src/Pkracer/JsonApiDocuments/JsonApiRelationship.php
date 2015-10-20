<?php

namespace Pkracer\JsonApiDocuments;

use Pkracer\JsonApiDocuments\Exceptions\InvalidDocumentResourceException;

class JsonApiRelationship
{
    protected $name;

    protected $data;

    protected $links;

    protected $meta;

    public function __construct($name, $data = null, array $links = null, array $meta = null)
    {
        $this->name = (string) $name;

        if ($this->dataIsValid($data)) {
            $this->data = $data;
        }

        $this->links = $links;
        $this->meta = $meta;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getData()
    {
        return $this->data;
    }

    protected function dataIsValid($data)
    {
        if (is_null($data)) {
            return true;
        }

        if ($data instanceof JsonApiResource) {
            return true;
        }

        if (is_array($data) && empty($data)) {
            return true;
        }

        if (is_array($data)) {
            foreach ($data as $resource) {
                if ( ! $resource instanceof JsonApiResource) {
                    throw new InvalidDocumentResourceException;
                }
            }

            return true;
        }

        return false;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function links(array $links)
    {
        $this->links = $links;
        return $this;
    }

    public function meta(array $meta)
    {
        $this->meta = $meta;
        return $this;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function isCollection()
    {
        if (is_array($this->data)) {
            return true;
        }

        return false;
    }

    public function hasAvailableDataToInclude()
    {
        // if the relationship has no data at all
        if (is_null($this->data) || empty($this->data)) {
            return false;
        }

        // if there is a has one relationship but and attributes are set
        if ($this->data instanceof JsonApiResource && ! empty($this->data->getAttributes())) {
            return true;
        }

        // if it is a has many relation
        if (is_array($this->data)) {

            foreach ($this->data as $resource) {
                /** @var $resource JsonApiResource */
                if (! empty($resource->getAttributes())) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getResourcesToInclude()
    {
        if (is_null($this->data) || empty($this->data)) {
            return [];
        }

        if ($this->data instanceof JsonApiResource && empty($this->data->getAttributes())) {
            return [];
        }

        if ($this->data instanceof JsonApiResource && ! empty($this->data->getAttributes())) {
            return [$this->data];
        }

        $includes = [];
        if (is_array($this->data)) {
            foreach ($this->data as $resource) {
                /** @var $resource JsonApiResource */
                if ($resource->getAttributes()) {
                    $includes[] = $resource;
                }
            }
        }

        return $includes;
    }

}
