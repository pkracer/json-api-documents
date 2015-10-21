<?php

namespace spec\Pkracer\JsonApiDocuments;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ErrorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(\Pkracer\JsonApiDocuments\Interfaces\ErrorInterface::class);
    }

    function it_may_contain_an_id()
    {
        $this->id(5000)->shouldReturn($this);
        $this->getId()->shouldReturn(5000);
    }

    function it_may_contain_a_links_object_that_has_an_about_member()
    {
        $this->links(['about' => 'about info'])->shouldReturn($this);
        $this->getLinks()->shouldReturn(['about' => 'about info']);
    }

    function it_may_contain_an_HTTP_status_represented_as_a_string()
    {
        $this->httpStatus(404)->shouldReturn($this);
        $this->getHttpStatus()->shouldReturn('404');
    }

    function it_may_contain_a_code_represented_as_a_string()
    {
        $this->code(404)->shouldReturn($this);
        $this->getCode()->shouldReturn('404');
    }

    function it_may_contain_a_title()
    {
        $this->title('Invalid attribute')->shouldReturn($this);
        $this->getTitle()->shouldReturn('Invalid attribute');
    }

    function it_may_contain_a_detailed_explaination()
    {
        $this->detail('The name attribute must be at least 10 characters')->shouldReturn($this);
        $this->getDetail()->shouldReturn('The name attribute must be at least 10 characters');
    }

    function it_may_contain_a_source_with_a_pointer_and_or_parameter()
    {
        $this->source(['pointer' => '/data', 'parameter' => 'include'])->shouldReturn($this);
        $this->getSource()->shouldReturn(['pointer' => '/data', 'parameter' => 'include']);
        $this->getPointer()->shouldReturn('/data');
        $this->getParameter()->shouldReturn('include');
    }

    function it_may_contain_a_meta_object()
    {
        $this->meta(['meta info'])->shouldReturn($this);
        $this->getMeta()->shouldReturn(['meta info']);
    }
}
