<?php

namespace Pkracer\JsonApiDocuments;

use Pkracer\JsonApiDocuments\Exceptions\InvalidDocumentFormatException;
use Pkracer\JsonApiDocuments\Exceptions\InvalidDocumentResourceException;
use Pkracer\JsonApiDocuments\Exceptions\InvalidErrorFormatException;
use Pkracer\JsonApiDocuments\Interfaces\FormatterInterface;
use Pkracer\JsonApiDocuments\Interfaces\DocumentInterface;
use Pkracer\JsonApiDocuments\Interfaces\ErrorInterface;
use Pkracer\JsonApiDocuments\Interfaces\ResourceInterface;

class Document implements DocumentInterface
{
    protected $description = [];

    protected $data;

    protected $errors = [];

    protected $meta = [];

    protected $links = [];

    protected $included = [];

    public function data($data, $formatter = null)
    {
        // if data is being set, clear any previously set errors
        $this->errors = [];

        if ( ! is_null($formatter)) {
            $formatter = $this->initializeFormatter($formatter);
            $data = $formatter->format($data);
        }

        if ($data instanceof ResourceInterface) {
            $this->data = $data;
            return $this;
        }

        if (is_array($data)) {
            foreach ($data as $resource) {
                if ( ! $resource instanceof ResourceInterface) {
                    throw new InvalidDocumentResourceException;
                }
            }
        }

        $this->data = $data;
        return $this;
    }

    /**
     * @param $formatter
     * @return FormatterInterface
     * @throws InvalidDocumentFormatException
     */
    public function initializeFormatter($formatter)
    {
        if (is_string($formatter) && class_exists($formatter)) {
            $formatter = new $formatter;
        }

        if ( ! $formatter instanceof FormatterInterface) {
            throw new InvalidDocumentFormatException;
        }

        return $formatter;
    }

    public function getData()
    {
        return $this->data;
    }

    public function errors(array $errors)
    {
        foreach ($errors as $error) {
            if ( ! $error instanceof ErrorInterface) {
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

    public function links(array $links)
    {
        $this->links = $links;
        return $this;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function includes(ResourceInterface $resource)
    {
        $this->included[] = $resource;
        return $this;
    }

    public function included(array $included)
    {
        $this->included[] = $included;
        return $this;
    }

    public function getIncluded()
    {
        return $this->included;
    }

    public function toArray()
    {
        if ($this->errors) {
            return $this->errorDocumentToArray();
        }

        return $this->dataDocumentToArray();
    }

    protected function errorDocumentToArray()
    {
        $errors = [];

        foreach ($this->errors as $error) {
            $errors[] = $error->toArray();
        }
        return [
            'errors' => $errors
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
}
