<?php

namespace Pkracer\JsonApiDocuments\Interfaces;

interface RelationshipInterface
{
    public function name($name);

    public function getName();

    public function data($data);

    public function getData();

    public function links(array $links);

    public function getLinks();

    public function toArray();
}
