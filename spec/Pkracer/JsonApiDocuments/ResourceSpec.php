<?php

namespace spec\Pkracer\JsonApiDocuments;

use PhpSpec\ObjectBehavior;
use Pkracer\JsonApiDocuments\Interfaces\ResourceInterface;
use Pkracer\JsonApiDocuments\JsonApiRelationship;
use Pkracer\JsonApiDocuments\JsonApiResource;
use Pkracer\JsonApiDocuments\Relationship;
use Pkracer\JsonApiDocuments\Resource;
use Prophecy\Argument;

class ResourceSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('default', '1');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(\Pkracer\JsonApiDocuments\Interfaces\ResourceInterface::class);
    }

    function it_can_override_the_constructed_resource_type()
    {
        $this->type('test')->shouldReturn($this);
    }

    function it_can_fetch_the_resource_type()
    {
        $this->getType()->shouldReturn('default');
    }

    function it_can_override_the_constructed_resource_id()
    {
        $this->id(1000)->shouldReturn($this);
    }

    function it_can_fetch_the_resource_id()
    {
        $this->getId()->shouldReturn('1');
    }

    function it_can_set_attributes_on_the_resource()
    {
        $this->attributes([
            'name' => 'new name',
            'description' => 'This is a description'
        ])->shouldReturn($this);
    }

    function it_can_fetch_the_attributes_of_the_resource()
    {
        $this->attributes([
            'name' => 'new name',
            'description' => 'This is a description'
        ]);

        $this->getAttributes()->shouldReturn([
            'name' => 'new name',
            'description' => 'This is a description'
        ]);
    }

    function it_can_set_links_on_the_resource()
    {
        $this->links([
            'self' => '/resource/1',
        ])->shouldReturn($this);
    }

    function it_can_fetch_the_links_set_on_the_resource()
    {
        $this->links([
            'self' => '/resource/1',
        ]);

        $this->getLinks()->shouldReturn([
            'self' => '/resource/1',
        ]);
    }

    function it_can_set_meta_information_on_the_resource()
    {
        $this->meta([
            'count' => 1,
            'other' => 'This is a other meta'
        ])->shouldReturn($this);
    }

    function it_can_fetch_the_meta_information_set_on_the_resource()
    {
        $this->meta([
            'count' => 1,
            'other' => 'This is a other meta'
        ]);

        $this->getMeta()->shouldReturn([
            'count' => 1,
            'other' => 'This is a other meta'
        ]);
    }

    function it_accepts_a_single_relationship()
    {
        $resource = new Resource('object', '100', ['property' => 'value']);
        $relationship = new Relationship('relationship', $resource);
        $this->relationship($relationship)->shouldReturn($this);
    }

    function it_accepts_an_array_of_relationships()
    {
        $resource1 = new Resource('object', '100', ['property' => 'value']);
        $relationship1 = new Relationship('relationship', $resource1);
        $resource2 = new Resource('other', '100', ['property' => 'value']);
        $relationship2 = new Relationship('other_relationship', $resource2);
        $this->relationships([$relationship1, $relationship2])->shouldReturn($this);
    }

    function it_can_fetch_a_specific_relation_on_the_resource()
    {
        $resource1 = new Resource('object', '100', ['property' => 'value']);
        $relationship1 = new Relationship('relationship', $resource1);
        $resource2 = new Resource('other', '100', ['property' => 'value']);
        $relationship2 = new Relationship('other_relationship', $resource2);
        $this->relationships([$relationship1, $relationship2]);
        $this->getRelationship('other_relationship')->shouldReturn($relationship2);
    }

    function it_can_fetch_all_relationships()
    {
        $resource1 = new Resource('object', '100', ['property' => 'value']);
        $relationship1 = new Relationship('relationship', $resource1);
        $resource2 = new Resource('other', '100', ['property' => 'value']);
        $relationship2 = new Relationship('other_relationship', $resource2);
        $this->relationships([$relationship1, $relationship2]);

        $this->getRelationships()->shouldReturn([
            'relationship' => $relationship1,
            'other_relationship' => $relationship2
        ]);

    }

    function it_can_have_another_resource_set_to_be_included()
    {
        $resource = new Resource('object', '100', ['property' => 'value']);
        $this->includes($resource)->shouldReturn($this);
    }

    function it_can_fetch_included_resources()
    {
        $resource = new Resource('object', '100', ['property' => 'value']);
        $this->includes($resource);
        $this->getIncludes()->shouldReturn([$resource]);
    }

    function it_can_have_an_array_of_resources_set_to_be_included()
    {
        $resource1 = new Resource('object', '100', ['property' => 'value']);
        $resource2 = new Resource('object', '101', ['property' => 'value']);
        $this->includes([$resource1, $resource2])->shouldReturn($this);
        $this->getIncludes()->shouldReturn([$resource1, $resource2]);
    }

    function it_can_be_converted_to_an_array()
    {
        $this->links(['self' => '/defaults/1']);
        $this->meta([
            'count' => 1,
            'other' => 'This is a other meta'
        ]);

        $this->attributes([
            'name' => 'Name',
            'description' => 'description'
        ]);

        $resource = new Resource('others', '100', ['property' => 'value']);
        $resource2 = new Resource('others', '101', ['property2' => 'value2']);
        $relationship = new Relationship('objects', [$resource, $resource2]);
        $this->relationship($relationship);
        $this->toArray()->shouldReturn([
            'type' => 'default',
            'id' => '1',
            'attributes' => [
                'name' => 'Name',
                'description' => 'description'
            ],
            'relationships' => [
                'objects' => [
                    'data' => [
                        [
                            'type' => 'others',
                            'id' => '100',
                        ],
                        [
                            'type' => 'others',
                            'id' => '101',
                        ]
                    ]
                ]
            ],
            'links' => [
                'self' => '/defaults/1'
            ],
            'meta' => [
                'count' => 1,
                'other' => 'This is a other meta'
            ]
        ]);
    }
}
