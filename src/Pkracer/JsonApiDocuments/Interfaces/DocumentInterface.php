<?php

namespace Pkracer\JsonApiDocuments\Interfaces;


interface DocumentInterface
{
    public function describe(array $description);

    public function getDescription();

    public function data($data);

    public function getData();

    public function errors(array $error);

    public function getErrors();

    public function links(array $links);

    public function getLinks();

    public function meta(array $meta);

    public function getMeta();

    public function includes(ResourceInterface $resource);

    public function included(array $included);

    public function getIncluded();
}
