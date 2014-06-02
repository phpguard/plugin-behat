<?php

namespace spec\PhpGuard\Plugins\Behat\Bridge\Console;

use PhpGuard\Application\Spec\ObjectBehavior;
use Prophecy\Argument;

class BehatApplicationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\Bridge\Console\BehatApplication');
    }

    function it_should_extends_the_BehatApplication()
    {
        $this->shouldHaveType('Behat\Behat\Console\BehatApplication');
    }
}