<?php

namespace Pkracer\JsonApiDocuments\Interfaces;

interface ResourceInterface
{
    public function type($type);

    public function getType();

    public function id($id);

    public function getId();

    public function attributes(array $attributes);

    public function getAttributes();

    public function links(array $links);

    public function getLinks();

    public function meta(array $meta);

    public function getMeta();

    public function relationship(RelationshipInterface $relationship);

    public function getRelationship($relationshipName);

    public function relationships(array $relationships);

    public function getRelationships();

    public function toArray();
}