<?php

namespace Pkracer\JsonApiDocuments;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class LaravelFormatter extends DocumentFormatter
{
    protected $type;

    protected $id = 'id';

    public function data($data)
    {
        if ($data instanceof Model) {
            $this->formatEloquentModel($data);
        }

        if ($data instanceof Collection) {
            $this->formatEloquentCollection($data);
        }

        if ($data instanceof \Illuminate\Database\Eloquent\Paginator) {
            $this->formatEloquentPaginator($data);
        }

        return $this;
    }

    public function formatEloquentModel(Model $model)
    {

        // if the type has not been set, assume it is the model name
        if (is_null($this->type)) {
            $reflected = new \ReflectionClass($model);
            $this->type = str_plural(snake_case(strtolower($reflected->getShortName())));
        }

        $id = $this->id;

        $resource = new Resource($this->type, $model->$id);
        $resource->attributes($this->attributes($model->toArray()));

        $this->data = $resource;

        return $this;
    }
}
