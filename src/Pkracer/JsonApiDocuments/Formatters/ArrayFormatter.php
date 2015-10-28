<?php

namespace Pkracer\JsonApiDocuments\Formatters;

use Pkracer\JsonApiDocuments\Exceptions\MissingIdException;
use Pkracer\JsonApiDocuments\Exceptions\MissingTypeException;
use Pkracer\JsonApiDocuments\Relationship;
use Pkracer\JsonApiDocuments\Resource;

class ArrayFormatter extends FormatterAbstract
{
    public function format($array)
    {
        if ( ! is_array($array)) {
            throw new \InvalidArgumentException('The resource must be an array.');
        }

        if (is_null($this->type)) {
            throw new \InvalidArgumentException('The type must be set on the formatter.');
        }

        $resource = new Resource($this->type, $this->formatId($array));

        return $this->setOptionalTopLevelMembers($resource, $array);
    }

    public function setOptionalTopLevelMembers(Resource $resource, array $array)
    {
        if (method_exists($this, 'attributes')) {
            $resource->attributes($this->attributes($array));
        }

        if (method_exists($this, 'links')) {
            $resource->links($this->links($array));
        }

        if (method_exists($this, 'meta')) {
            $resource->meta($this->meta($array));
        }

        if ( ! empty($this->relationships)) {
            $relationships = $this->buildRelationships($array);
            $resource->relationships($relationships);

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

    protected function buildRelationships(array $item)
    {
        $relationships = [];

        foreach ($this->relationships as $relationship) {

            if ($this->relationshipCanBeLoaded($relationship, $item)) {
                $relationships[] = $this->buildRelationship($relationship, $item);
            }
        }

        return $relationships;
    }

    protected function canBeIncluded($resource)
    {
        if (in_array($resource, $this->includes) || $this->includes == '*') {
            return true;
        }

        return false;
    }

    protected function relationshipCanBeLoaded($relationship, array $resource)
    {
        return method_exists($this, $relationship) && isset($resource[$relationship]);
    }

    protected function buildRelationship($name, array $item)
    {
        $formattedRelationshipResources = $this->formatRelationshipItems($name, $item);

        $relation = new Relationship($name, $formattedRelationshipResources);

        $linksMethod = $this->relationshipLinksMethod($name);

        if (method_exists($this, $linksMethod)) {
            $relation->links($this->$linksMethod($item));
        }

        $metaMethod = $this->relationshipMetaMethod($name);
        if (method_exists($this, $metaMethod)) {
            $relation->meta($this->$metaMethod($item));
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

    public function relationLinks(array $item)
    {
        return [
            'self' => $this->baseUrl . '/' . $this->type . '/' . $item['id'] . '/relationships/relation',
            'related' => $this->baseUrl . '/' . $this->type . '/' . $item['id'] . '/relation',
        ];
    }

    public function hasRelationsToFormat(array $item)
    {
        if (empty($this->relations)) {
            return false;
        }

        foreach ($this->relations as $relation) {
            if (isset($item[$relation])) {
                return true;
            }
        }

        return false;
    }



    public function loadRelatedItem($relation, array $item = null)
    {
        if ( ! isset($item['type']) || is_null($item['type'])) {
            throw new MissingTypeException;
        }

        if ( ! isset($item['id']) || is_null($item['id'])) {
            throw new MissingIdException;
        }

        $item['id'] = (string) $item['id'];

        $relationship = [
            'type' => $item['type'],
            'id' => $item['id']
        ];

        $relationshipMethodName = $relation . 'Links';
        if (method_exists($this, $relationshipMethodName)) {
            $links = $this->$relationshipMethodName($item);
            if ( ! empty($links)) {
                $relationship['links'] = $links;
            }
        }

        if ($this->shouldInclude === ['*'] || in_array($relation, $this->shouldInclude)) {
            $this->includes[] = $item;
        }

        if ( ! isset($this->relations[$relation])) {
            $this->relations[$relation] = null;
        }

        $this->relations[$relation] = $relationship;
        return $this;

    }

    public function loadRelatedCollection($relation, array $data = [])
    {
        $relationships = [];
        foreach ($data as $index => $item) {
            if ( ! isset($item['type']) || is_null($item['type'])) {
                throw new MissingTypeException;
            }

            if ( ! isset($item['id']) || is_null($item['id'])) {
                throw new MissingIdException;
            }

            $data[$index]['id'] = (string) $item['id'];
            $relationships[] = [
                'type' => $item['type'],
                'id' => $data[$index]['id']
            ];

            if ($this->shouldInclude === ['*'] || in_array($relation, $this->shouldInclude)) {
                $this->includes[] = $data[$index];
            }
        }

        if ( ! isset($this->relations[$relation])) {
            $this->relations[$relation] = [];
        }

        $this->relations[$relation] = $relationships;
        return $this;
    }


}