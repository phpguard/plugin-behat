<?php

namespace spec\PhpGuard\Plugins\Behat\Bridge\Console;

use PhpGuard\Application\Spec\ObjectBehavior;
use Prophecy\Argument;

class BehatCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\Bridge\Console\BehatCommand');
    }
}