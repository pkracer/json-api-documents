<?php

namespace Pkracer\JsonApiDocuments;

use Pkracer\JsonApiDocuments\Exceptions\InvalidDocumentFormatException;
use Pkracer\JsonApiDocuments\Exceptions\InvalidDocumentResourceException;
use Pkracer\JsonApiDocuments\Exceptions\InvalidErrorFormatException;
use Pkracer\JsonApiDocuments\Exceptions\InvalidLinkException;
use Pkracer\JsonApiDocuments\Exceptions\MissingFormatException;
use Pkracer\JsonApiDocuments\Exceptions\MissingIdException;
use Pkracer\JsonApiDocuments\Exceptions\MissingTypeException;

class JsonApiDocument
{
    protected $data;

    protected $errors = [];

    protected $meta = [];

    protected $links = [];

    protected $description = [];

    protected $includes = [];

    /**
     * @var \Pkracer\JsonApiDocuments\JsonApiDocumentFormatInterface
     */
    protected $format;

    public function __construct($format = null)
    {
        if ($format !== null) {
            $this->format($format);
        }
    }

    public function item(JsonApiResource $resource)
    {
        if ( ! $resource instanceof JsonApiResource) {
            throw new InvalidDocumentResourceException;
        }

        $this->data = $resource;

        // clear errors if data is being set
        $this->errors = [];
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function errors(array $errors)
    {
        foreach ($errors as $error) {
            if ( ! $error instanceof JsonApiError) {
                throw new InvalidErrorFormatException;
            }
        }
        $this->errors = $errors;
        $this->data = null;
        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
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

    public function describe(array $description)
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function links(array $links = [])
    {
        $this->links = $links;
        return $this;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function sideload(array $resource)
    {
        $this->includes[] = $resource;
        return $this;
    }

    public function getIncludes()
    {
        return $this->includes;
    }

    public function format($format)
    {
        if (is_string($format) && class_exists($format)) {
            $format = new $format;
        }

        if ( ! $format instanceof JsonApiDocumentFormatInterface) {
            throw new InvalidDocumentFormatException;
        }

        $this->format = $format;
        return $this;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function toArray()
    {
        if ($this->format === null) {
            throw new MissingFormatException;
        }

        if ($this->errors) {
            return $this->errorDocumentToArray();
        }

        return $this->dataDocumentToArray();
    }

    protected function errorDocumentToArray()
    {
        return [
            'errors' => $this->errors
        ];
    }

    protected function dataDocumentToArray()
    {
        $document = [];

        if ( ! empty($this->description)) {
            $document['jsonapi'] = $this->description;
        }

        if ( ! empty($this->links)) {
            $document['links'] = $this->links;
        }

        $document['data'] = $this->data;

        if ( ! empty($this->meta)) {
            $document['meta'] = $this->meta;
        }

        if ( ! empty($this->includes)) {
            $document['included'] = $this->includes;
        }

        return $document;
    }

    public function collection(array $resources)
    {
        foreach ($resources as $resource) {
            if ( ! $resource instanceof JsonApiResource) {
                throw new InvalidDocumentResourceException;
            }
        }

        $this->data = $resources;
        return $this;
    }
}
