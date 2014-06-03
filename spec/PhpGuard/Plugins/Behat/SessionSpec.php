<?php

namespace spec\PhpGuard\Plugins\Behat;

use PhpGuard\Application\Spec\ObjectBehavior;
use Prophecy\Argument;

class SessionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\Session');
    }
}