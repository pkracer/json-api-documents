<?php

namespace Pkracer\JsonApiDocuments\Formatters;

use Pkracer\JsonApiDocuments\Interfaces\FormatterInterface;

abstract class FormatterAbstract implements FormatterInterface
{
    protected $type;

    protected $id = 'id';

    protected $overrideLinks = false;

    protected $baseUrl;

    protected $relationships = [];

    protected $includes = ['*'];

    abstract public function format($resource);

    public function type($type)
    {
        $this->type = (string) $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getId()
    {
        return $this->id;
    }

    public function id($id)
    {
        $this->id = (string) $id;
        return $this;
    }

    public function baseUrl($url)
    {
        // trim trailing slash
        $this->baseUrl = rtrim($url, '/');
        return $this;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    protected function formatId(array $item)
    {
        return (string) $item[$this->id];
    }

    public function defaultLinks(array $item)
    {
        return [
            'self' => $this->baseUrl . '/' . $this->type . '/' . $this->formatId($item)
        ];
    }

    public function overrideLinks($value)
    {
        $this->overrideLinks = (boolean) $value;
        return $this;
    }

    public function defaultLinksAreOverriden()
    {
        return $this->overrideLinks === true;
    }

    public function relationships(array $relations)
    {
        $this->relationships = $relations;
        return $this;
    }

    public function getRelationships()
    {
        return $this->relationships;
    }

    public function includes(array $includes)
    {
        $this->includes = $includes;
        return $this;
    }

    public function getIncludes()
    {
        return $this->includes;
    }
}
