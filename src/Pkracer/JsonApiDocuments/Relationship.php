<?php

namespace Pkracer\JsonApiDocuments;

use Pkracer\JsonApiDocuments\Exceptions\InvalidDocumentResourceException;
use Pkracer\JsonApiDocuments\Interfaces\RelationshipInterface;
use Pkracer\JsonApiDocuments\Interfaces\ResourceInterface;

class Relationship implements RelationshipInterface
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

    public function name($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getData()
    {
        return $this->data;
    }

    public function data($data)
    {
        if ($this->dataIsValid($data)) {
            $this->data = $data;
        }

        return $this;
    }

    protected function dataIsValid($data)
    {
        if (is_null($data)) {
            return true;
        }

        if ($data instanceof ResourceInterface) {
            return true;
        }

        if (is_array($data) && empty($data)) {
            return true;
        }

        if (is_array($data)) {
            foreach ($data as $resource) {
                if ( ! $resource instanceof ResourceInterface) {
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
        if ($this->data instanceof Resource && ! empty($this->data->getAttributes())) {
            return true;
        }

        // if it is a has many relation
        if (is_array($this->data)) {

            foreach ($this->data as $resource) {
                /** @var $resource Resource */
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

        if ($this->data instanceof Resource && empty($this->data->getAttributes())) {
            return [];
        }

        if ($this->data instanceof Resource && ! empty($this->data->getAttributes())) {
            return [$this->data];
        }

        $includes = [];
        if (is_array($this->data)) {
            foreach ($this->data as $resource) {
                /** @var $resource Resource */
                if ($resource->getAttributes()) {
                    $includes[] = $resource;
                }
            }
        }

        return $includes;
    }

    public function toArray()
    {
        $relationship = [];

        if ( ! empty($this->data) || ! is_null($this->data)) {
            if ($this->data instanceof ResourceInterface) {
                $relationship['data']['type'] = $this->data->getType();
                $relationship['data']['id'] = $this->data->getId();
            }

            if (is_array($this->data)) {
                $relationship['data'] = [];
                foreach ($this->data as $index => $resource) {
                    $relationship['data'][$index]['type'] = $resource->getType();
                    $relationship['data'][$index]['id'] = $resource->getId();
                }

            }
        }


        if ( ! empty($this->links)) {
            $relationship['links'] = $this->links;
        }

        if ( ! empty($this->meta)) {
            $relationship['meta'] = $this->meta;
        }

        return [$this->name => $relationship];
    }

}
