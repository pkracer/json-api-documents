<?php

namespace spec\Pkracer\JsonApiDocuments;

use PhpSpec\ObjectBehavior;
use Pkracer\JsonApiDocuments\Exceptions\InvalidDocumentResourceException;
use Pkracer\JsonApiDocuments\JsonApiResource;
use Pkracer\JsonApiDocuments\Resource;
use Prophecy\Argument;

class RelationshipSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('relationship');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(\Pkracer\JsonApiDocuments\Interfaces\RelationshipInterface::class);
    }

    function it_can_override_the_name()
    {
        $this->name('new_name')->shouldReturn($this);
    }

    function it_can_fetch_the_relationship_name()
    {
        $this->name('new_name');
        $this->getName()->shouldReturn('new_name');
    }

    function it_can_be_constructed_with_a_data_resource()
    {
        $resource = new Resource('type', '1', ['property' => 'value']);
        $this->beConstructedWith('relationship', $resource);
        $this->getData()->shouldReturn($resource);
    }

    function it_can_be_constructed_with_a_collection_of_resources()
    {
        $resource1 = new Resource('relation', 1, ['property' => 'value']);
        $resource2 = new Resource('relation', 2, ['property' => 'value']);
        $this->beConstructedWith('relationship', [$resource1, $resource2]);
        $this->getData()->shouldReturn([$resource1, $resource2]);
    }

    function it_throws_an_exception_for_an_invalid_collection()
    {
        $resource1 = new Resource('relation', 1, ['property' => 'value']);
        $this->beConstructedWith('relationship', [$resource1, 'relation']);
        $this->shouldThrow(InvalidDocumentResourceException::class)->duringInstantiation();
    }

    function it_be_constructed_with_an_array_of_links()
    {
        $resource = new Resource('relation', 1, ['property' => 'value']);
        $this->beConstructedWith('relationship', $resource, ['self' => 'default/1/relationships/relation']);
        $this->getLinks()->shouldReturn(['self' => 'default/1/relationships/relation']);
    }

    function it_can_have_an_array_of_links_set_after_instantiation()
    {
        $resource = new Resource('relation', 1, ['property' => 'value']);
        $this->beConstructedWith('relationship', $resource);
        $this->links(['self' => 'default/1/relationships/relation'])->shouldReturn($this);
        $this->getLinks()->shouldReturn(['self' => 'default/1/relationships/relation']);
    }

    function it_can_be_constructed_with_an_array_of_meta_information()
    {
        $this->beConstructedWith('relationship', null, null, ['count'=> 100]);
        $this->getMeta()->shouldReturn(['count'=> 100]);
    }

    function it_can_have_an_array_of_meta_information_set_after_instantiation()
    {
        $this->beConstructedWith('relationship', null);
        $this->meta(['count'=> 100])->shouldReturn($this);
        $this->getMeta()->shouldReturn(['count'=> 100]);
    }

    function it_detects_an_item_relation()
    {
        $resource1 = new Resource('relation', 1, ['property' => 'value']);
        $this->beConstructedWith('relationship', $resource1);
        $this->isCollection()->shouldReturn(false);
    }

    function it_detects_a_collection_relation()
    {
        $resource1 = new Resource('relation', 1, ['property' => 'value']);
        $resource2 = new Resource('relation', 2, ['property' => 'value']);
        $this->beConstructedWith('relationship', [$resource1, $resource2]);
        $this->isCollection()->shouldReturn(true);
    }

    function it_can_be_converted_to_an_array()
    {
        $resource1 = new Resource('relation', 1);
        $resource2 = new Resource('relation', 2);
        $this->beConstructedWith(
            // relationship name
            'relationship',

            // json api resources
            [
                $resource1,
                $resource2
            ],

            // links
            [
                'self' => 'default/1/relationships/relation'
            ],

            // meta
            [
                'count'=> 100
            ]);

        $this->toArray()->shouldReturn([
            'relationship' => [
                'data' => [
                    [
                        'type' => 'relation',
                        'id' => '1'
                    ],
                    [
                        'type' => 'relation',
                        'id' => '2'
                    ]
                ],
                'links' => [
                    'self' => 'default/1/relationships/relation'
                ],
                'meta' => [
                    'count'=> 100
                ]
            ]
        ]);
    }
}
