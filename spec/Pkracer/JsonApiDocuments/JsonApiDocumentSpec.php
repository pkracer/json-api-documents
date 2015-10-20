<?php

namespace spec\Pkracer\JsonApiDocuments;

use PhpSpec\ObjectBehavior;
use Pkracer\JsonApiDocuments\JsonApiDocumentFormat;
use Pkracer\JsonApiDocuments\JsonApiDocumentFormatInterface;
use Pkracer\JsonApiDocuments\JsonApiResource;
use Pkracer\JsonApiDocuments\JsonApiResourceFormat;
use Prophecy\Argument;

class JsonApiDocumentSpec extends ObjectBehavior
{
    function let(JsonApiResourceFormat $format)
    {
        $this->beConstructedWith($format);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(\Pkracer\JsonApiDocuments\JsonApiDocument::class);
    }

    function it_accepts_a_json_api_resource_as_data()
    {
        $resource = new JsonApiResource('resource', '1', ['attribute' => 'value']);
        $this->item($resource)->shouldReturn($this);
        $this->getData()->shouldReturn($resource);
    }

    function it_accepts_an_array_of_json_api_resources_as_data()
    {
        $resource1 = new JsonApiResource('resource', '1', ['attribute' => 'value']);
        $resource2 = new JsonApiResource('resource', '2', ['attribute' => 'value']);
        $this->collection([$resource1, $resource2])->shouldReturn($this);
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

    function it_can_include_additional_resources()
    {
        $this->sideload([])->shouldReturn($this);
        $this->getIncludes()->shouldReturn([[]]);
    }

    function it_does_not_allow_the_data_and_error_members_to_exist_at_the_same_time()
    {
        $resource = new JsonApiResource('resource', '1', ['attribute' => 'value']);

        $this->item($resource);
        $this->errors([]);
        $this->getData()->shouldReturn(null);
        $this->item($resource);
        $this->getErrors()->shouldReturn([]);
    }

    function it_accepts_a_data_format_object()
    {
        $format = new JsonApiResourceFormat();
        $this->format($format)->shouldReturn($this);
        $this->getFormat()->shouldReturn($format);
    }

    function it_converts_a_data_format_string_to_an_object()
    {
        $format = JsonApiResourceFormat::class;
        $this->format($format)->shouldReturn($this);
        $this->getFormat()->shouldBeAnInstanceOf($format);
    }

    function it_can_be_constructed_with_a_format_string()
    {
        $format = JsonApiResourceFormat::class;
        $this->beConstructedWith($format);
        $this->getFormat()->shouldBeAnInstanceOf($format);
    }

    function its_format_must_be_valid()
    {
        $this->shouldThrow(\Pkracer\JsonApiDocuments\Exceptions\InvalidDocumentFormatException::class)->duringFormat('format');
    }

    function it_throws_an_exception_when_format_is_missing()
    {
        $this->beConstructedWith(null);
        $this->shouldThrow(\Pkracer\JsonApiDocuments\Exceptions\MissingFormatException::class)->duringToArray();
    }

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
