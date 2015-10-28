<?php

namespace spec\Pkracer\JsonApiDocuments;

use PhpSpec\ObjectBehavior;
use Pkracer\JsonApiDocuments\ArrayFormatter;
use Pkracer\JsonApiDocuments\Resource;
use Prophecy\Argument;

class DocumentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(\Pkracer\JsonApiDocuments\Document::class);
    }

    function it_accepts_a_json_api_resource_as_data()
    {
        $resource = new Resource('resource', '1', ['attribute' => 'value']);
        $this->data($resource)->shouldReturn($this);
        $this->getData()->shouldReturn($resource);
    }

    function it_accepts_an_array_of_json_api_resources_as_data()
    {
        $resource1 = new Resource('resource', '1', ['attribute' => 'value']);
        $resource2 = new Resource('resource', '2', ['attribute' => 'value']);
        $this->data([$resource1, $resource2])->shouldReturn($this);
        $this->getData()->shouldReturn([$resource1, $resource2]);
    }

    function it_allows_an_error_object_to_be_added()
    {
        $this->errors([])->shouldReturn($this);
        $this->getErrors()->shouldReturn([]);
    }

    function it_allows_only_json_api_errors_to_be_added()
    {
        $this->errors([])->shouldReturn($this);
        $this->getErrors()->shouldReturn([]);
    }

    function it_allows_a_meta_object_to_be_added()
    {
        $this->meta([])->shouldReturn($this);
        $this->getMeta()->shouldReturn([]);
    }

    function it_allows_an_object_to_describe_the_servers_implementation()
    {
        $this->describe([
            'version' => '1.0'
        ])->shouldReturn($this);

        $this->getDescription()->shouldReturn([
            'version' => '1.0'
        ]);
    }

//    function it_can_include_additional_resources()
//    {
//        $this->sideload([])->shouldReturn($this);
//        $this->getIncludes()->shouldReturn([[]]);
//    }

    function it_does_not_allow_the_data_and_error_members_to_exist_at_the_same_time()
    {
        $resource = new Resource('resource', '1', ['attribute' => 'value']);

        $this->data($resource);
        $this->errors([]);
        $this->getData()->shouldReturn(null);
        $this->data($resource);
        $this->getErrors()->shouldReturn([]);
    }

//    function it_accepts_a_data_formatter_object()
//    {
//        $formatter = new ArrayFormatter();
//        $this->formatter($formatter)->shouldReturn($this);
//        $this->getFormatter()->shouldReturn($formatter);
//    }
//
//    function it_converts_a_data_format_string_to_an_object()
//    {
//        $formatter = ArrayFormatter::class;
//        $this->formatter($formatter)->shouldReturn($this);
//        $this->getFormatter()->shouldBeAnInstanceOf($formatter);
//    }
//
//    function it_can_be_constructed_with_a_format_string()
//    {
//        $formatter = ArrayFormatter::class;
//        $this->beConstructedWith($formatter);
//        $this->getFormatter()->shouldBeAnInstanceOf($formatter);
//    }
//
//    function its_formatter_must_be_valid()
//    {
//        $this->shouldThrow(\Pkracer\JsonApiDocuments\Exceptions\InvalidDocumentFormatException::class)->duringFormatter('formatter');
//    }

    function it_fetches_links()
    {
        $this->getLinks()->shouldReturn([]);
    }

    function it_accepts_an_array_of_links()
    {
        $this->links([
            'previous' => 'previous link',
            'next' => 'next link'
        ])->shouldReturn($this);

        $this->getLinks()->shouldReturn([
            'previous' => 'previous link',
            'next' => 'next link'
        ]);
    }

    function it_can_be_converted_to_an_array()
    {
        $this->describe(['version' => '1.0'])->meta(['count' => '10'])->links([
            "self" => "http://example.com/articles",
            "next" => "http://example.com/articles?page[offset]=2",
            "last" => "http://example.com/articles?page[offset]=10"
        ]);

        $this->toArray()->shouldReturn([
            'jsonapi' => [
                'version' => '1.0'
            ],
            'links' => [
                "self" => "http://example.com/articles",
                "next" => "http://example.com/articles?page[offset]=2",
                "last" => "http://example.com/articles?page[offset]=10"
            ],
            'data' => null,
            'meta' => [
                'count' => '10'
            ]
        ]);
    }
}
