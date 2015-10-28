<?php

namespace Pkracer\JsonApiDocuments;

use Illuminate\Support\Facades\Facade;

class JsonApiFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'jsonapi.document';
    }
}
