<?php

namespace Pkracer\JsonApiDocuments;

use Pkracer\JsonApiDocuments\Exceptions\InvalidRelationshipException;
use Pkracer\JsonApiDocuments\Exceptions\MissingIdException;
use Pkracer\JsonApiDocuments\Exceptions\MissingTypeException;

class JsonApiResource
{
    protected $type;

    protected $id;

    protected $attributes = [];

    protected $links = [];

    protected $meta = [];

    protected $relationships = [];

    protected $included = [];

    public function __construct($type, $id)
    {
        $this->type = (string) $type;
        $this->id = (string) $id;
    }

    public function type($type)
    {
        $this->type = (string) $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function id($id)
    {
        $this->id = (string) $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function attributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
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

    public function meta(array $meta)
    {
        $this->meta = $meta;
        return $this;
    }

    public function getMeta()
    {
        return $this->meta;
    }

//    public function itemRelation($relation, JsonApiResource $resource = null, array $relationLinks = [])
//    {
//        if ( ! isset($this->relationships[$relation])) {
//            $this->relationships[$relation]['data'] = null;
//        }
//
//        $this->relationships[$relation]['data'] = $resource;
//
//        if ( ! empty($relationLinks)) {
//            $this->relationships[$relation]['links'] = $relationLinks;
//        }
//
//        return $this;
//
//    }
//
//    public function collectionRelation($relation, array $collection = [], array $relationLinks = [])
//    {
//        foreach ($collection as $item) {
//            if ( ! $item instanceof JsonApiResource) {
//                throw new InvalidRelationshipException;
//            }
//        }
//
//        if ( ! isset($this->relationships[$relation])) {
//            $this->relationships[$relation]['data'] = [];
//        }
//
//        $this->relationships[$relation]['data'] = $collection;
//
//        if ( ! empty($relationLinks)) {
//            $this->relationships[$relation]['links'] = $relationLinks;
//        }
//
//        return $this;
//    }

    public function includes(JsonApiResource $resource)
    {
        $this->included[] = $resource;
        return $this;
    }

    public function getRelationships()
    {
        return $this->relationships;
    }

    public function getIncluded()
    {
        return $this->included;
    }

    public function toArray()
    {
        // TODO: write logic here
    }

    public function relationship(JsonApiRelationship $relationship)
    {
        $this->relationships[] = $relationship;
        return $this;
    }
}
