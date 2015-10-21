<?php

namespace spec\Pkracer\JsonApiDocuments;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LaravelResourceFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(\Pkracer\JsonApiDocuments\LaravelResourceFormatter::class);
    }
}
