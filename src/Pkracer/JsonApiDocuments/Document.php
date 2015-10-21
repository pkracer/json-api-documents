<?php

namespace Pkracer\JsonApiDocuments;

use Pkracer\JsonApiDocuments\Exceptions\InvalidDocumentFormatException;
use Pkracer\JsonApiDocuments\Exceptions\InvalidDocumentResourceException;
use Pkracer\JsonApiDocuments\Exceptions\InvalidErrorFormatException;
use Pkracer\JsonApiDocuments\Exceptions\InvalidLinkException;
use Pkracer\JsonApiDocuments\Exceptions\MissingFormatException;
use Pkracer\JsonApiDocuments\Exceptions\MissingIdException;
use Pkracer\JsonApiDocuments\Exceptions\MissingTypeException;
use Pkracer\JsonApiDocuments\Interfaces\DocumentFormatterInterface;
use Pkracer\JsonApiDocuments\Interfaces\DocumentInterface;
use Pkracer\JsonApiDocuments\Interfaces\ErrorInterface;
use Pkracer\JsonApiDocuments\Interfaces\ResourceInterface;

class Document implements DocumentInterface
{
    protected $data;

    protected $errors = [];

    protected $meta = [];

    protected $links = [];

    protected $description = [];

    protected $included = [];

    /**
     * @var DocumentFormatterInterface
     */
    protected $format;

    public function __construct($format = null)
    {
        if ($format !== null) {
            $this->format($format);
        }
    }

    public function data($data)
    {
        // clear any errors if data is being set
        $this->errors = [];

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

    public function format($format)
    {
        if (is_string($format) && class_exists($format)) {
            $format = new $format;
        }

        if ( ! $format instanceof DocumentFormatterInterface) {
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
