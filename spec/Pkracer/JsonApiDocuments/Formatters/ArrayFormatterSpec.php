<?php

namespace spec\Pkracer\JsonApiDocuments\Formatters;

use PhpSpec\ObjectBehavior;;
use Pkracer\JsonApiDocuments\Interfaces\ResourceInterface;
use Pkracer\JsonApiDocuments\Resource;
use Prophecy\Argument;

class ArrayFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(\Pkracer\JsonApiDocuments\Interfaces\FormatterInterface::class);
    }

    /* ***********************************************
     * Abstract Format Tests Start
     */
    public function it_returns_the_resource_type()
    {
        $this->getType()->shouldReturn(null);
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

    function it_returns_the_default_links_that_should_be_added()
    {
        $this->type('resources');
        $this->defaultLinks(['id' => 1])->shouldReturn([
            'self' => '/resources/1'
        ]);
    }

    function it_appends_the_default_links_with_the_base_url()
    {
        $this->baseUrl('http://example.com/api/')->type('resources');
        $this->defaultLinks(['id' => 1])->shouldReturn([
            'self' => 'http://example.com/api/resources/1'
        ]);
    }

    function it_allows_the_default_links_to_be_overridden()
    {
        $this->defaultLinksAreOverriden()->shouldReturn(false);
        $this->overrideLinks(true)->shouldReturn($this);
        $this->defaultLinksAreOverriden()->shouldReturn(true);
    }

    function it_returns_the_relationships()
    {
        $this->getRelationships()->shouldReturn([]);
    }

    function it_allows_the_relationships_to_be_set()
    {
        $this->relationships(['relation1', 'relation2'])->shouldReturn($this);
        $this->getRelationships()->shouldReturn(['relation1', 'relation2']);
    }

    function it_has_available_includes_set_as_wildcard_by_default()
    {
        $this->getIncludes()->shouldReturn(['*']);
    }

    function it_allows_the_includes_to_be_set()
    {
        $this->includes(['relation1', 'relation2'])->shouldReturn($this);
        $this->getIncludes()->shouldReturn(['relation1', 'relation2']);
    }
    /*
     * Abstract Format Tests End
     * **********************************************************************
     */

    function it_throws_an_invalid_argument_exception_if_trying_to_format_non_array()
    {
        $this->type('resources');
        $this->shouldThrow(\InvalidArgumentException::class)->duringFormat('data');
    }

    function it_throws_an_invalid_argument_exception_if_a_type_has_not_been_set_before_formatting_an_array()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringFormat(['id' => 1, 'name' => 'Name', 'description' => 'This is a description']);
    }

    function it_returns_an_object_adhering_to_the_resource_interface_when_formatting_a_single_array()
    {
        $this->type('resources');
        $this->format([
            'id' => 1,
            'name' => 'Name',
            'description' => 'This is a description'
        ])->shouldHaveType(ResourceInterface::class);
    }

//
//    function it_formats_an_items_attributes()
//    {
//        $this->format([
//            'id' => 1,
//            'name' => 'test',
//            'description' => 'This is a description',
//            'created_at' => '2015-10-13-00:00:00',
//            'updated_at' => '2015-10-13-00:00:00'
//        ])->shouldReturn();
//    }
//
//    function it_formats_the_items_default_links_without_a_base_url()
//    {
//        $this->format([
//            'id' => 1
//        ])->shouldHaveKeyWithValue('links', [
//            'self' => '/resources/1'
//        ]);
//    }
//
//    function it_formats_the_items_default_links_with_a_base_url()
//    {
//        $this->baseUrl('http://www.example.com/api');
//        $this->format([
//            'id' => 1
//        ])->shouldHaveKeyWithValue('links', [
//            'self' => 'http://www.example.com/api/resources/1'
//        ]);
//    }
//
//    function it_detects_if_an_item_has_a_valid_relation_loaded()
//    {
//        $this->relations(['relation1', 'relation2'])->shouldReturn($this);
//        $this->hasRelationsToFormat([
//            'id' => 1,
//            'relation1' => [
//                'id' => 1
//            ]
//        ])->shouldReturn(true);
//    }
//
//    function it_does_not_detect_if_an_item_has_an_invalid_relation_loaded()
//    {
//        $this->relations(['relation1'])->shouldReturn($this);
//        $this->hasRelationsToFormat([
//            'id' => 1,
//            'relation2' => [
//                'id' => 1
//            ]
//        ])->shouldReturn(false);
//    }
//
//    function it_returns_false_when_no_relations_are_loaded()
//    {
//        $this->hasRelationsToFormat([
//            'id' => 1,
//            'relation2' => [
//                'id' => 1
//            ]
//        ])->shouldReturn(false);
//    }
//
//    function it_throws_an_error_for_a_missing_type_on_a_related_item()
//    {
//        $this->shouldThrow(\Pkracer\JsonApiDocuments\Exceptions\MissingTypeException::class)->duringLoadRelatedItem('relation', ['id' => 1]);
//    }
//
//    function it_throws_an_error_for_a_missing_id_on_a_related_item()
//    {
//        $this->shouldThrow(\Pkracer\JsonApiDocuments\Exceptions\MissingIdException::class)->duringLoadRelatedItem('relation', ['type' => 'related']);
//    }
//
//    function it_throws_an_error_for_a_missing_type_on_a_related_item_in_a_related_collection()
//    {
//        $this->shouldThrow(\Pkracer\JsonApiDocuments\Exceptions\MissingTypeException::class)->duringLoadRelatedCollection('relations', [['type' => 'related', 'id' => 1], ['id' => 2]]);
//    }
//
//    function it_throws_an_error_for_a_missing_id_on_a_related_item_in_a_related_collection()
//    {
//        $this->shouldThrow(\Pkracer\JsonApiDocuments\Exceptions\MissingIdException::class)->duringLoadRelatedCollection('relations', [['type' => 'related', 'id' => 1], ['type' => 'related']]);
//    }
//
//    function it_can_load_a_related_item()
//    {
//        $this->loadRelatedItem('relation', ['type' => 'related', 'id' => 1])->shouldReturn($this);
//        $this->getRelations()->shouldReturn(['relation' => [
//            'type' => 'related',
//            'id' => '1',
//            'links' => [
//                'self' => '/resources/1/relationships/relation',
//                'related' => '/resources/1/relation'
//            ]
//        ]]);
//    }
//
//    function it_can_load_a_related_collection()
//    {
//        $this->loadRelatedCollection('relations',
//            [['type' => 'related', 'id' => 1], ['type' => 'related', 'id' => 2]])->shouldReturn($this);
//        $this->getRelations()->shouldReturn([
//            'relations' => [
//                [
//                    'type' => 'related',
//                    'id' => '1'
//                ],
//                [
//                    'type' => 'related',
//                    'id' => '2'
//                ]
//            ]
//        ]);
//    }
//
//    function it_adds_a_related_item_to_includes_if_it_has_attributes()
//    {
//        $this->loadRelatedItem('relation', [
//            'type' => 'related',
//            'id' => 1,
//            'attributes' => [
//                'id' => 1,
//                'name' => 'Name',
//                'description' => 'This is a description'
//            ]
//        ]);
//
//        $this->getRelations()->shouldReturn([
//            'relation' => [
//                'type' => 'related',
//                'id' => '1',
//                'links' => [
//                    'self' => '/resources/1/relationships/relation',
//                    'related' => '/resources/1/relation'
//                ]
//            ]
//        ]);
//
//        $this->getIncludes()->shouldReturn([
//            [
//                'type' => 'related',
//                'id' => '1',
//                'attributes' => [
//                    'id' => 1,
//                    'name' => 'Name',
//                    'description' => 'This is a description'
//                ]
//            ]
//        ]);
//    }
//
//    function it_adds_a_collection_of_related_items_to_includes_if_they_have_attributes()
//    {
//        $this->loadRelatedCollection('relations', [
//            [
//                'type' => 'related',
//                'id' => 1,
//                'attributes' => [
//                    'id' => 1,
//                    'name' => 'Name 1',
//                    'description' => 'This is a description 1'
//                ]
//            ],
//            [
//                'type' => 'related',
//                'id' => 2,
//                'attributes' => [
//                    'id' => 2,
//                    'name' => 'Name 2',
//                    'description' => 'This is a description 2'
//                ]
//            ]
//        ]);
//
//        $this->getRelations()->shouldReturn([
//            'relations' => [
//                ['type' => 'related', 'id' => '1'],
//                ['type' => 'related', 'id' => '2']
//            ]
//        ]);
//
//        $this->getIncludes()->shouldReturn([
//            [
//                'type' => 'related',
//                'id' => '1',
//                'attributes' => [
//                    'id' => 1,
//                    'name' => 'Name 1',
//                    'description' => 'This is a description 1'
//                ]
//            ],
//            [
//                'type' => 'related',
//                'id' => '2',
//                'attributes' => [
//                    'id' => 2,
//                    'name' => 'Name 2',
//                    'description' => 'This is a description 2'
//                ]
//            ]
//        ]);
//    }
//
//    function it_adds_a_related_item_links_to_the_relationship_if_they_exist()
//    {
//        $this->loadRelatedItem('relation', [
//            'type' => 'related',
//            'id' => 1
//        ]);
//
//        $this->getRelations()->shouldReturn(['relation' => [
//            'type' => 'related',
//            'id' => '1',
//            'links' => [
//                'self' => '/resources/1/relationships/relation',
//                'related' => '/resources/1/relation'
//            ]
//        ]]);
//    }
//
//    function it_adds_a_related_item_links_with_base_url_to_the_relationship_if_they_exist()
//    {
//        $this->baseUrl('http://www.example.com/');
//
//        $this->loadRelatedItem('relation', [
//            'type' => 'related',
//            'id' => 1
//        ]);
//
//        $this->getRelations()->shouldReturn(['relation' => [
//            'type' => 'related',
//            'id' => '1',
//            'links' => [
//                'self' => 'http://www.example.com/resources/1/relationships/relation',
//                'related' => 'http://www.example.com/resources/1/relation'
//            ]
//        ]]);
//    }


}
