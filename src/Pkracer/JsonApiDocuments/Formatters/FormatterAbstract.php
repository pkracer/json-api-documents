<?php

namespace Pkracer\JsonApiDocuments\Formatters;

use Pkracer\JsonApiDocuments\Interfaces\FormatterInterface;
use Pkracer\JsonApiDocuments\Relationship;
use Pkracer\JsonApiDocuments\Resource;

abstract class FormatterAbstract implements FormatterInterface
{
    /**
     * @var \Pkracer\JsonApiDocuments\Resource
     */
    protected $resource;

    protected $type;

    protected $id = 'id';

    protected $overrideLinks = false;

    protected $baseUrl;

    protected $relationships = [];

    protected $includes = ['*'];

    public function format($entity)
    {
        if (is_null($this->type)) {
            throw new \InvalidArgumentException('The type must be set on the formatter.');
        }

        $resource = $this->instantiateResource($entity);
        return $this->setOptionalTopLevelMembers($resource, $entity);
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

    protected function formatId($entity)
    {
        if (is_array($entity)) {
            return (string) $entity[$this->id];
        }

        if (is_object($entity)) {
            return (string) $entity->{$this->id};
        }

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

    public function defaultLinksAreOverridden()
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

    public function instantiateResource($entity)
    {
        if ( ! is_array($entity) && ! is_object($entity)) {
            throw new \InvalidArgumentException('The entity being formatted must be an array or an object');
        }

        return new Resource($this->type, $this->formatId($entity));
    }

    public function setOptionalTopLevelMembers(Resource $resource, $entity)
    {
        $resource = $this->setAttributes($resource, $entity);
        $resource = $this->setLinks($resource, $entity);
        $resource = $this->setMeta($resource, $entity);
        return $this->setRelationships($resource, $entity);
    }

    protected function setAttributes(Resource $resource, $entity)
    {
        if (method_exists($this, 'attributes')) {
            $this->resource->attributes($this->attributes($entity));
        }

        return $resource;
    }

    protected function setLinks(Resource $resource, $entity)
    {
        if (method_exists($this, 'links')) {
            $this->resource->links($this->links($entity));
        }

        return $resource;
    }

    protected function setMeta(Resource $resource, $entity)
    {
        if (method_exists($this, 'meta')) {
            $this->resource->meta($this->meta($entity));
        }

        return $resource;
    }

    protected function setRelationships(Resource $resource, $entity)
    {
        if ( ! empty($this->relationships)) {
            $relationships = $this->buildRelationships($entity);
            $this->resource->relationships($relationships);

            // TODO: need to fix this into a loop
            // TODO: add optional second argument to the $resource->relationships() method that determins if incoming sjould be sideloaded
//            if ($this->canBeIncluded($relationships)) {
//                /* @var $relationships Relationship */
//                $nestedResources = $relationships->getResourcesToInclude();
//
//                // TODO:: allow included to be set all at once rather than through iteration
//                foreach ($nestedResources as $nestedResource) {
//                    $resource->includes($nestedResource);
//                }
//            }
        }

        return $resource;
    }

    protected function buildRelationships($entity)
    {
        $relationships = [];

        foreach ($this->relationships as $relationship) {

            if ($this->relationshipCanBeLoaded($relationship, $entity)) {
                $relationships[] = $this->buildRelationship($relationship, $entity);
            }
        }

        return $relationships;
    }

    protected function relationshipCanBeLoaded($relationship, $entity)
    {
        if (is_array($entity)) {
            return method_exists($this, $relationship) && isset($entity[$relationship]);
        }

        return method_exists($this, $relationship) && isset($entity->$relationship);

    }

    protected function buildRelationship($name, $entity)
    {
        $formattedRelationshipResources = $this->formatRelationshipItems($name, $entity);

        $relation = new Relationship($name, $formattedRelationshipResources);

        $linksMethod = $this->relationshipLinksMethod($name);

        if (method_exists($this, $linksMethod)) {
            $relation->links($this->$linksMethod($entity));
        }

        $metaMethod = $this->relationshipMetaMethod($name);
        if (method_exists($this, $metaMethod)) {
            $relation->meta($this->$metaMethod($entity));
        }

        return $relation;
    }

    protected function formatRelationshipItems($relationship, $resource)
    {
        return $this->$relationship($resource);
    }

    protected function relationshipLinksMethod($relationship)
    {
        return $relationship . 'Links';
    }

    protected function relationshipMetaMethod($relationship)
    {
        return $relationship . 'Meta';
    }

    public function relationLinks($entity)
    {
        if (is_array($entity)) {
            return [
                'self' => $this->baseUrl . '/' . $this->type . '/' . $entity['id'] . '/relationships/relation',
                'related' => $this->baseUrl . '/' . $this->type . '/' . $entity['id'] . '/relation',
            ];
        }

        return [
            'self' => $this->baseUrl . '/' . $this->type . '/' . $entity->id . '/relationships/relation',
            'related' => $this->baseUrl . '/' . $this->type . '/' . $entity->id . '/relation',
        ];
    }
}
