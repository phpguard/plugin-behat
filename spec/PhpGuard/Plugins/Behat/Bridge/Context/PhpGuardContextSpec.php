<?php

namespace spec\PhpGuard\Plugins\Behat\Bridge\Context;

use PhpGuard\Application\Spec\ObjectBehavior;
use Prophecy\Argument;

class PhpGuardContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\Bridge\Context\PhpGuardContext');
    }
}