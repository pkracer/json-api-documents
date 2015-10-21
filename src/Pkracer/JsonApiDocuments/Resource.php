<?php

namespace Pkracer\JsonApiDocuments;

use Pkracer\JsonApiDocuments\Exceptions\InvalidRelationshipException;
use Pkracer\JsonApiDocuments\Interfaces\RelationshipInterface;
use Pkracer\JsonApiDocuments\Interfaces\ResourceInterface;

class Resource implements ResourceInterface
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

    public function includes(Resource $resource)
    {
        $this->included[] = $resource;
        return $this;
    }

    public function relationship(RelationshipInterface $relationship)
    {
        $relationshipName = $relationship->getName();

        if ( ! isset($this->relationships[$relationshipName])) {
            $this->relationships[$relationshipName] = null;
        }

        $this->relationships[$relationshipName] = $relationship;

        return $this;
    }

    public function getRelationship($name)
    {
        if (array_key_exists($name, $this->relationships)) {
            return $this->relationships[$name];
        }

        return null;
    }

    public function relationships(array $relationships)
    {
        foreach ($relationships as $relationship) {
            if ( ! $relationship instanceof RelationshipInterface) {
                throw new InvalidRelationshipException;
            }

            $relationshipName = $relationship->getName();

            if ( ! isset($this->relationships[$relationshipName])) {
                $this->relationships[$relationshipName] = null;
            }

            $this->relationships[$relationshipName] = $relationship;
        }

        return $this;
    }

    public function getRelationships()
    {
        return $this->relationships;
    }


    public function toArray()
    {
        $resource = [
            'type' => $this->type,
            'id' => $this->id
        ];

        if ( ! empty($this->attributes)) {
            $resource['attributes'] = $this->attributes;
        }

        if ( ! empty($this->relationships)) {
            $resource['relationships'] = [];
            foreach ($this->relationships as $relationship) {
                $resource['relationships'] = array_merge($resource['relationships'], $relationship->toArray());
            }
        }

        if ( ! empty($this->links)) {
            $resource['links'] = $this->links;
        }

        if ( ! empty($this->meta)) {
            $resource['meta'] = $this->meta;
        }

        return $resource;
    }
}
