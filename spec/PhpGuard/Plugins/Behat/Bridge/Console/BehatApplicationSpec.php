<?php

namespace spec\PhpGuard\Plugins\Behat\Bridge\Console;

use PhpGuard\Application\Bridge\CodeCoverage\CodeCoverageSession;
use PhpGuard\Application\Spec\ObjectBehavior;
use Prophecy\Argument;

class BehatApplicationSpec extends ObjectBehavior
{
    function let(CodeCoverageSession $coverageSession)
    {
        $this->beConstructedWith($coverageSession);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\Bridge\Console\BehatApplication');
    }

    function it_should_extends_the_BehatApplication()
    {
        $this->shouldHaveType('Behat\Behat\Console\BehatApplication');
    }

    function it_should_save_coverage_session(CodeCoverageSession $coverageSession)
    {
        $coverageSession->saveState()->shouldBeCalled();
        $this->saveCoverage();
    }
}