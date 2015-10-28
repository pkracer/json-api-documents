<?php

namespace Pkracer\JsonApiDocuments\Formatters;

use Illuminate\Database\Eloquent\Collection as Collection;
use Illuminate\Database\Eloquent\Model;
use Pkracer\JsonApiDocuments\DocumentFormatter;

class LaravelFormatter extends ArrayFormatter
{
    public function format($resources)
    {
        if ( ! $resources instanceof Model && ! $resources instanceof Collection) {
            throw new \InvalidArgumentException('The resources must be an instance of an Eloquent Model, an Eloquent Collection, or \Illuminate\Support\Collection');
        }

        if (is_null($this->type)) {
            $this->setTypeFromObject($resources);
        }

        if ($resources instanceof Model) {
            return parent::format($resources->toArray());
        }

        $formattedResources = [];
        foreach ($resources as $resource) {
            $formattedResources[] = parent::format($resource->toArray());
        }

        return $formattedResources;
    }

    protected function setTypeFromObject($object)
    {
        if ($object instanceof Collection) {
            $object = $object->first();
        }

        $reflected = new \ReflectionClass($object);
        $this->type = str_plural(snake_case(strtolower($reflected->getShortName())));
    }
}
