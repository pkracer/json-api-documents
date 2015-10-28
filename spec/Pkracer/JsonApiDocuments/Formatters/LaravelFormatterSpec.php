<?php

namespace spec\Pkracer\JsonApiDocuments\Formatters;

use PhpSpec\ObjectBehavior;
use Pkracer\JsonApiDocuments\Interfaces\FormatterInterface;
use Prophecy\Argument;

class LaravelFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FormatterInterface::class);
    }
}
