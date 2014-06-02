<?php

namespace spec\PhpGuard\Plugins\Behat\Bridge;

use PhpGuard\Application\Spec\ObjectBehavior;
use Prophecy\Argument;

class PhpGuardExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\Bridge\PhpGuardExtension');
    }
}