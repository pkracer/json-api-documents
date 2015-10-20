<?php

namespace Pkracer\JsonApiDocuments;

use Pkracer\JsonApiDocuments\Exceptions\MissingIdException;
use Pkracer\JsonApiDocuments\Exceptions\MissingTypeException;

class JsonApiResourceFormat implements JsonApiDocumentFormatInterface
{
    protected $type = 'defaults';

    protected $id = 'id';

    protected $baseUrl;

    protected $relations = [];

    protected $includes = [];

    protected $shouldInclude = ['*'];

    public function type($type)
    {
        $this->type = (string) $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function attributes(array $data)
    {
        return $data;
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

    public function format(array $item)
    {
        $formatted =  [
            'type' => $this->type,
            'id' => (string) $this->formatId($item),
        ];

        $attributes = $this->attributes($item);
        if ( ! empty($attributes)) {
            $formatted['attributes'] = $attributes;
        }

        $links = $this->links($item);
        if ( ! empty($links)) {
            $formatted['links'] = $links;
        }

        return $formatted;
    }

    protected function formatId(array $item)
    {
        return $item[$this->id];
    }

    protected function links(array $item)
    {
        return [
            'self' => $this->baseUrl . '/' . $this->type . '/' . $this->formatId($item)
        ];
    }

    public function baseUrl($url)
    {
        // trim trailing slash
        $this->baseUrl = rtrim($url, '/');
        return $this;
    }

    public function relationLinks(array $item)
    {
        return [
            'self' => $this->baseUrl . '/' . $this->type . '/' . $item['id'] . '/relationships/relation',
            'related' => $this->baseUrl . '/' . $this->type . '/' . $item['id'] . '/relation',
        ];
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function hasRelations(array $item)
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

    public function relations(array $relations)
    {
        $this->relations = $relations;
        return $this;
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

    public function getRelations()
    {
        return $this->relations;
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

    public function getIncludes()
    {
        return $this->includes;
    }
}