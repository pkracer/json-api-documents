<?php

namespace Pkracer\JsonApiDocuments\Formatters;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class LaravelFormatter extends FormatterAbstract
{
    public function format($entity)
    {
        if ( ! $entity instanceof Model && ! $entity instanceof Collection) {
            throw new \InvalidArgumentException('The resources must be an instance of an Eloquent Model or an Eloquent Collection');
        }


        if (is_null($this->type)) {
            $this->setTypeFromObject($entity);
        }

        if ($entity instanceof Model) {
            return parent::format($entity);
        }

        $formattedResources = [];
        foreach ($entity as $item) {
            $formattedResources[] = parent::format($item);
        }

        return $formattedResources;
    }

    protected function setTypeFromObject($entity)
    {
        if ($entity instanceof Collection) {
            $entity = $entity->first();
        }

        $reflected = new \ReflectionClass($entity);
        $this->type = str_plural(snake_case(strtolower($reflected->getShortName())));
    }
}
