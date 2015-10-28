<?php

namespace Pkracer\JsonApiDocuments\Formatters;

use Pkracer\JsonApiDocuments\Exceptions\MissingIdException;
use Pkracer\JsonApiDocuments\Exceptions\MissingTypeException;

class ArrayFormatter extends FormatterAbstract
{
    public function format($array)
    {
        if ( ! is_array($array)) {
            throw new \InvalidArgumentException('The resource must be an array.');
        }

        return parent::format($array);
    }

    protected function canBeIncluded($resource)
    {
        if (in_array($resource, $this->includes) || $this->includes == '*') {
            return true;
        }

        return false;
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