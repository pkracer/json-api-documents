<?php

namespace spec\Pkracer\JsonApiDocuments;

use PhpSpec\ObjectBehavior;;
use Prophecy\Argument;

class JsonApiResourceFormatSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pkracer\JsonApiDocuments\JsonApiResourceFormat');
    }

    public function it_returns_the_resource_type()
    {
        $this->getType()->shouldReturn('defaults');
    }

    public function it_allows_the_type_to_be_set()
    {
        $this->type('test')->shouldReturn($this);
        $this->getType()->shouldReturn('test');
    }

    public function it_returns_the_resource_id()
    {
        $this->getId()->shouldReturn('id');
    }

    public function it_allows_the_id_to_be_set()
    {
        $this->id('slug')->shouldReturn($this);
        $this->getId()->shouldReturn('slug');
    }

    function it_allows_a_base_url_to_be_set()
    {
        $this->baseUrl('http://www.example.com/api')->shouldReturn($this);
        $this->getBaseUrl()->shouldReturn('http://www.example.com/api');
    }

    function it_trims_the_trailing_slash_from_a_base_url()
    {
        $this->baseUrl('http://www.example.com/api//')->shouldReturn($this);
        $this->getBaseUrl()->shouldReturn('http://www.example.com/api');
    }

    function it_formats_an_items_type()
    {
        $this->format(['id' => 1])->shouldHaveKeyWithValue('type', 'defaults');
    }

    function it_formats_an_items_id()
    {
        $this->format(['id' => 1])->shouldHaveKeyWithValue('id', '1');
    }

    function it_formats_an_items_attributes()
    {
        $this->format([
            'id' => 1,
            'name' => 'test',
            'description' => 'This is a description',
            'created_at' => '2015-10-13-00:00:00',
            'updated_at' => '2015-10-13-00:00:00'
        ])->shouldHaveKeyWithValue('attributes', [
            'id' => 1,
            'name' => 'test',
            'description' => 'This is a description',
            'created_at' => '2015-10-13-00:00:00',
            'updated_at' => '2015-10-13-00:00:00'
        ]);
    }

    function it_formats_the_items_default_links_without_a_base_url()
    {
        $this->format([
            'id' => 1
        ])->shouldHaveKeyWithValue('links', [
            'self' => '/defaults/1'
        ]);
    }

    function it_formats_the_items_default_links_with_a_base_url()
    {
        $this->baseUrl('http://www.example.com/api');
        $this->format([
            'id' => 1
        ])->shouldHaveKeyWithValue('links', [
            'self' => 'http://www.example.com/api/defaults/1'
        ]);
    }

    function it_detects_if_an_item_has_a_valid_relation_loaded()
    {
        $this->relations(['relation1', 'relation2'])->shouldReturn($this);
        $this->hasRelations([
            'id' => 1,
            'relation1' => [
                'id' => 1
            ]
        ])->shouldReturn(true);
    }

    function it_does_not_detect_if_an_item_has_an_invalid_relation_loaded()
    {
        $this->relations(['relation1'])->shouldReturn($this);
        $this->hasRelations([
            'id' => 1,
            'relation2' => [
                'id' => 1
            ]
        ])->shouldReturn(false);
    }

    function it_returns_false_when_no_relations_are_loaded()
    {
        $this->hasRelations([
            'id' => 1,
            'relation2' => [
                'id' => 1
            ]
        ])->shouldReturn(false);
    }

    function it_throws_an_error_for_a_missing_type_on_a_related_item()
    {
        $this->shouldThrow(\Pkracer\JsonApiDocuments\Exceptions\MissingTypeException::class)->duringLoadRelatedItem('relation', ['id' => 1]);
    }

    function it_throws_an_error_for_a_missing_id_on_a_related_item()
    {
        $this->shouldThrow(\Pkracer\JsonApiDocuments\Exceptions\MissingIdException::class)->duringLoadRelatedItem('relation', ['type' => 'related']);
    }

    function it_throws_an_error_for_a_missing_type_on_a_related_item_in_a_related_collection()
    {
        $this->shouldThrow(\Pkracer\JsonApiDocuments\Exceptions\MissingTypeException::class)->duringLoadRelatedCollection('relations', [['type' => 'related', 'id' => 1], ['id' => 2]]);
    }

    function it_throws_an_error_for_a_missing_id_on_a_related_item_in_a_related_collection()
    {
        $this->shouldThrow(\Pkracer\JsonApiDocuments\Exceptions\MissingIdException::class)->duringLoadRelatedCollection('relations', [['type' => 'related', 'id' => 1], ['type' => 'related']]);
    }

    function it_can_load_a_related_item()
    {
        $this->loadRelatedItem('relation', ['type' => 'related', 'id' => 1])->shouldReturn($this);
        $this->getRelations()->shouldReturn(['relation' => [
            'type' => 'related',
            'id' => '1',
            'links' => [
                'self' => '/defaults/1/relationships/relation',
                'related' => '/defaults/1/relation'
            ]
        ]]);
    }

    function it_can_load_a_related_collection()
    {
        $this->loadRelatedCollection('relations',
            [['type' => 'related', 'id' => 1], ['type' => 'related', 'id' => 2]])->shouldReturn($this);
        $this->getRelations()->shouldReturn([
            'relations' => [
                [
                    'type' => 'related',
                    'id' => '1'
                ],
                [
                    'type' => 'related',
                    'id' => '2'
                ]
            ]
        ]);
    }

    function it_adds_a_related_item_to_includes_if_it_has_attributes()
    {
        $this->loadRelatedItem('relation', [
            'type' => 'related',
            'id' => 1,
            'attributes' => [
                'id' => 1,
                'name' => 'Name',
                'description' => 'This is a description'
            ]
        ]);

        $this->getRelations()->shouldReturn([
            'relation' => [
                'type' => 'related',
                'id' => '1',
                'links' => [
                    'self' => '/defaults/1/relationships/relation',
                    'related' => '/defaults/1/relation'
                ]
            ]
        ]);

        $this->getIncludes()->shouldReturn([
            [
                'type' => 'related',
                'id' => '1',
                'attributes' => [
                    'id' => 1,
                    'name' => 'Name',
                    'description' => 'This is a description'
                ]
            ]
        ]);
    }

    function it_adds_a_collection_of_related_items_to_includes_if_they_have_attributes()
    {
        $this->loadRelatedCollection('relations', [
            [
                'type' => 'related',
                'id' => 1,
                'attributes' => [
                    'id' => 1,
                    'name' => 'Name 1',
                    'description' => 'This is a description 1'
                ]
            ],
            [
                'type' => 'related',
                'id' => 2,
                'attributes' => [
                    'id' => 2,
                    'name' => 'Name 2',
                    'description' => 'This is a description 2'
                ]
            ]
        ]);

        $this->getRelations()->shouldReturn([
            'relations' => [
                ['type' => 'related', 'id' => '1'],
                ['type' => 'related', 'id' => '2']
            ]
        ]);

        $this->getIncludes()->shouldReturn([
            [
                'type' => 'related',
                'id' => '1',
                'attributes' => [
                    'id' => 1,
                    'name' => 'Name 1',
                    'description' => 'This is a description 1'
                ]
            ],
            [
                'type' => 'related',
                'id' => '2',
                'attributes' => [
                    'id' => 2,
                    'name' => 'Name 2',
                    'description' => 'This is a description 2'
                ]
            ]
        ]);
    }

    function it_adds_a_related_item_links_to_the_relationship_if_they_exist()
    {
        $this->loadRelatedItem('relation', [
            'type' => 'related',
            'id' => 1
        ]);

        $this->getRelations()->shouldReturn(['relation' => [
            'type' => 'related',
            'id' => '1',
            'links' => [
                'self' => '/defaults/1/relationships/relation',
                'related' => '/defaults/1/relation'
            ]
        ]]);
    }

    function it_adds_a_related_item_links_with_base_url_to_the_relationship_if_they_exist()
    {
        $this->baseUrl('http://www.example.com/');

        $this->loadRelatedItem('relation', [
            'type' => 'related',
            'id' => 1
        ]);

        $this->getRelations()->shouldReturn(['relation' => [
            'type' => 'related',
            'id' => '1',
            'links' => [
                'self' => 'http://www.example.com/defaults/1/relationships/relation',
                'related' => 'http://www.example.com/defaults/1/relation'
            ]
        ]]);
    }


}
