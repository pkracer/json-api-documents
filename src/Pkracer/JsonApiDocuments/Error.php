<?php

namespace Pkracer\JsonApiDocuments;

use Pkracer\JsonApiDocuments\Interfaces\ErrorInterface;

class Error implements ErrorInterface
{
    protected $id;

    protected $links;

    protected $httpStatus;

    protected $code;

    protected $title;

    protected $detail;

    protected $source;

    protected $meta;

    public function id($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function links(array $links)
    {
        $this->links = $links;
        return $this;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function httpStatus($status)
    {
        $this->httpStatus = (string) $status;
        return $this;
    }

    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    public function code($code)
    {
        $this->code = (string) $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function title($title)
    {
        $this->title = (string) $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function detail($detail)
    {
        $this->detail = (string) $detail;
        return $this;
    }

    public function getDetail()
    {
        return $this->detail;
    }

    public function source(array $source)
    {
        $this->source = $source;
        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getPointer()
    {
        $pointer = null;
        if (array_key_exists('pointer', $this->source)) {
            $pointer = $this->source['pointer'];
        }

        return $pointer;
    }

    public function getParameter()
    {
        $parameter = null;
        if (array_key_exists('parameter', $this->source)) {
            $parameter = $this->source['parameter'];
        }

        return $parameter;
    }

    public function meta(array $meta)
    {
        $this->meta = $meta;
        return $this;
    }

    public function getMeta()
    {
        return $this->meta;
    }
}
