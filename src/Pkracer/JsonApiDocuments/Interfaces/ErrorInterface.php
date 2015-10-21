<?php

namespace Pkracer\JsonApiDocuments\Interfaces;

interface ErrorInterface
{
    public function id($id);

    public function links(array $links);

    public function httpStatus($status );

    public function code($code);

    public function title($title);

    public function detail($detail);

    public function source(array $source);

    public function meta(array $meta);
}
