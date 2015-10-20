<?php

namespace spec\Pkracer\JsonApiDocuments;

use PhpSpec\ObjectBehavior;
use Pkracer\JsonApiDocuments\JsonApiRelationship;
use Pkracer\JsonApiDocuments\JsonApiResource;
use Prophecy\Argument;

class JsonApiResourceSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            'default',
            '1'
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(\Pkracer\JsonApiDocuments\JsonApiResource::class);
    }

    public function it_can_override_the_constructed_resource_type()
    {
        $this->type('test')->shouldReturn($this);
        $this->getType()->shouldReturn('test');

    }

    public function it_can_fetch_the_resource_type()
    {
        $this->getType()->shouldReturn('default');
    }

    public function it_can_override_the_constructed_resource_id()
    {
        $this->id(1000)->shouldReturn($this);
        $this->getId()->shouldReturn('1000');
    }

    public function it_can_fetch_the_resource_id()
    {
        $this->getId()->shouldReturn('1');
    }

    public function it_can_set_attributes_on_the_resource()
    {
        $this->attributes([
            'name' => 'new name',
            'description' => 'This is a description'
        ])->shouldReturn($this);
    }

    public function it_can_fetch_the_attributes_of_the_resource()
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

    public function it_can_set_links_on_the_resource()
    {
        $this->links([
            'self' => '/resource/1',
        ])->shouldReturn($this);
    }

    public function it_can_fetch_the_links_set_on_the_resource()
    {
        $this->links([
            'self' => '/resource/1',
        ]);

        $this->getLinks()->shouldReturn([
            'self' => '/resource/1',
        ]);
    }

    public function it_can_set_meta_information_on_the_resource()
    {
        $this->meta([
            'count' => 1,
            'other' => 'This is a other meta'
        ])->shouldReturn($this);
    }

    public function it_can_fetch_the_meta_information_set_on_the_resource()
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

    public function it_accepts_a_json_api_relationship_as_a_has_one_relation()
    {
        $resource = new JsonApiResource('object', '100', ['property' => 'value']);
        $relationship = new JsonApiRelationship('relationship', $resource);
        $this->relationship($relationship)->shouldReturn($this);
    }

    public function it_accepts_a_json_api_relationship_as_a_has_many_relation()
    {
        $resource = new JsonApiResource('object', '100', ['property' => 'value']);
        $resource2 = new JsonApiResource('object', '101', ['property2' => 'value2']);
        $relationship = new JsonApiRelationship('relationships', [$resource, $resource2]);
        $this->relationship($relationship)->shouldReturn($this);
    }

//    public function it_accepts_a_json_api_relationship_as_a_has_one_relation()
//    {
//        $relationship = new JsonApiRelationship('relationship');
//        $this->hasMany($relationship)->shouldReturn($this);
//    }

//    public function it_fetches_the_resource_item_relation()
//    {
//        $resource = new JsonApiResource('object', '100', ['property' => 'value']);
//        $this->itemRelation('relationship', $resource, ['self' => '/default/1/relationships/object'])->shouldReturn($this);
//
//        $this->getRelationships()->shouldReturn([
//            'relationship' => [
//                'data' => $resource,
//                'links' => ['self' => '/default/1/relationships/object']
//            ]
//        ]);
//    }
//
//    public function it_can_set_a_resource_collection()
//    {
//        $resource = new JsonApiResource('object', '100', ['property' => 'value']);
//        $resource2 = new JsonApiResource('object', '101', ['property2' => 'value2']);
//
//        $this->collectionRelation('relationships', [$resource, $resource2], ['self' => '/default/1/relationships/object'])->shouldReturn($this);
//
//        $this->getRelationships()->shouldReturn([
//            'relationships' => [
//                'data' => [$resource, $resource2],
//                'links' => ['self' => '/default/1/relationships/object']
//            ]
//        ]);
//    }
//
//    public function it_fetches_the_resource_collection()
//    {
//        $resource = new JsonApiResource('object', '100', ['property' => 'value']);
//        $resource2 = new JsonApiResource('object', '101', ['property2' => 'value2']);
//
//        $this->collectionRelation('relationships', [
//            $resource, $resource2
//        ])->shouldReturn($this);
//    }

    public function it_can_set_another_resource_to_include()
    {
        $resource = new JsonApiResource('object', '100', ['property' => 'value']);
        $this->includes($resource)->shouldReturn($this);
    }

    public function it_fetches_the_included_resources()
    {
        $resource = new JsonApiResource('object', '100', ['property' => 'value']);

        $this->includes($resource)->shouldReturn($this);

        $this->getIncluded()->shouldReturn([
            $resource
        ]);

        $resource2 = new JsonApiResource('object2', '101', ['property1' => 'value1']);

        $this->includes($resource2)->shouldReturn($this);

        $this->getIncluded()->shouldReturn([
            $resource, $resource2
        ]);
    }

//    public function it_can_be_converted_to_an_array()
//    {
//        $this->links(['self' => '/defaults/1',]);
//        $this->meta([
//            'count' => 1,
//            'other' => 'This is a other meta'
//        ]);
//
//        $resource = new JsonApiResource('others', '100', ['property' => 'value']);
//        $resource2 = new JsonApiResource('others', '101', ['property2' => 'value2']);
//        $this->collectionRelation('objects', [$resource, $resource2]);
//        $this->toArray()->shouldReturn([
//            'type' => 'default',
//            'id' => '1',
//            'attributes' => [
//                'name' => 'Name',
//                'description' => 'description'
//            ],
//            'relationships' => [
//                'objects' => [
//                    'data' => [
//                        [
//                            'type' => 'others',
//                            'id' => '100',
//                        ],
//                        [
//                            'type' => 'others',
//                            'id' => '101',
//                        ]
//                    ]
//                ]
//            ]
//        ]);
//    }
//
//    public function its_includes_can_be_converted_to_an_array()
//    {
//
//    }
}
