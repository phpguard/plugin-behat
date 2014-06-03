<?php

namespace spec\PhpGuard\Plugins\Behat\Bridge;

use Behat\Behat\Event\StepEvent;
use Behat\Behat\Event\SuiteEvent;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Gherkin\Node\StepNode;
use PhpGuard\Application\Bridge\CodeCoverage\CodeCoverageSession;
use PhpGuard\Application\Spec\ObjectBehavior;
use Prophecy\Argument;

class BehatEventListenerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\Bridge\BehatEventListener');
    }

    function it_should_subscribe_events()
    {
        $subscribed = $this->getSubscribedEvents();
        $subscribed->shouldHaveKey('beforeStep');
        $subscribed->shouldHaveKey('afterStep');
    }

    function it_should_start_coverage(
        CodeCoverageSession $coverageSession,
        StepEvent $stepEvent,
        StepNode $stepNode,
        ScenarioNode $scenarioNode
    )
    {
        $stepEvent->getStep()->willReturn($stepNode);
        $stepNode->getText()->willReturn('text');
        $stepNode->getParent()->willReturn($scenarioNode);
        $scenarioNode->getTitle()->willReturn('scenario');

        $stepNode->getType()->willReturn('type');
        $coverageSession->start('Scenario: scenario on Step: type text')
            ->shouldBeCalled()
        ;

        $this->beConstructedWith($coverageSession);
        $this->beforeStep($stepEvent);
    }

    function it_should_stop_coverage(
        CodeCoverageSession $coverageSession,
        StepEvent $step
    )
    {
        $coverageSession->stop()
            ->shouldBeCalled()
        ;

        $this->beConstructedWith($coverageSession);
        $this->afterStep($step);
    }
}