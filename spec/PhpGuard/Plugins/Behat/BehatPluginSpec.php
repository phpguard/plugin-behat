<?php

namespace spec\PhpGuard\Plugins\Behat;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BehatPluginSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\BehatPlugin');
    }
}
