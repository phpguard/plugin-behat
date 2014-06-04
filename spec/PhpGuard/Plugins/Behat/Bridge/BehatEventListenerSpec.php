<?php

namespace spec\PhpGuard\Plugins\Behat\Bridge;

use Behat\Behat\Event\FeatureEvent;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Event\StepEvent;
use Behat\Behat\Event\SuiteEvent;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Gherkin\Node\StepNode;
use PhpGuard\Application\Bridge\CodeCoverage\CodeCoverageSession;
use PhpGuard\Application\Container\ContainerInterface;
use PhpGuard\Application\Spec\ObjectBehavior;
use PhpGuard\Application\Util\Filesystem;
use PhpGuard\Plugins\Behat\Session;
use Prophecy\Argument;

class BehatEventListenerSpec extends ObjectBehavior
{
    function let(
        CodeCoverageSession $coverageSession,
        Session $session,
        Filesystem $filesystem,
        StepEvent $stepEvent,
        StepNode $stepNode,
        ScenarioEvent $scenarioEvent,
        ScenarioNode $scenarioNode
    )
    {
        $stepEvent->getStep()->willReturn($stepNode);
        $stepEvent->getResult()->willReturn(StepEvent::PASSED);

        $scenarioEvent->getScenario()->willReturn($scenarioNode);
        $scenarioEvent->getResult()->willReturn(StepEvent::PASSED);

        $this->beConstructedWith($coverageSession,$session,$filesystem);
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

    function it_should_start_coverage_on_beforeStep_event(
        CodeCoverageSession $coverageSession,
        StepEvent $stepEvent,
        StepNode $stepNode,
        ScenarioNode $scenarioNode
    )
    {
        $stepEvent->getStep()->willReturn($stepNode);
        $stepNode->getText()->willReturn('text');
        $stepNode->getParent()->willReturn($scenarioNode);
        $stepNode->getFile()->willReturn('some_file');
        $stepNode->getLine()->willReturn(1);

        $stepNode->getType()->willReturn('type');
        $coverageSession->start('some_file on type text line: 1')
            ->shouldBeCalled()
        ;

        $this->beforeStep($stepEvent);
    }

    function it_should_stop_coverage_on_afterStep_event(
        CodeCoverageSession $coverageSession,
        StepEvent $step,
        StepNode $node
    )
    {
        $step->getStep()->willReturn($node);
        $step->getResult()->willReturn(StepEvent::PASSED);
        $coverageSession->stop()
            ->shouldBeCalled()
        ;
        $this->afterStep($step);
    }

    function it_should_save_coverage_session_on_afterSuite_event(
        CodeCoverageSession $coverageSession
    )
    {
        $coverageSession->saveState()->shouldBeCalled();
        $this->afterSuite();
    }

    function it_should_start_session_on_beforeSuite_event(
        Session $session
    )
    {
        $session->start()->shouldBeCalled();
        $this->beforeSuite();
    }

    function it_should_stop_session_on_afterSuite_event(
        Session $session
    )
    {
        $session->stop()->shouldBeCalled();
        $this->afterSuite();
    }

    function it_should_add_result_to_session_on_afterStep_event(
        Session $session,
        StepEvent $stepEvent
    )
    {
        $session->addResult($stepEvent)
            ->shouldBeCalled();
        $this->afterStep($stepEvent);
    }

    function it_should_add_scenario_result_to_session_on_afterScenario_event(
        ScenarioEvent $scenarioEvent,
        Session $session
    )
    {
        $session->addResult($scenarioEvent)
            ->shouldBeCalled();
        $this->afterScenario($scenarioEvent);
    }

    function it_should_add_feature_result_to_session_on_afterFeature_event(
        FeatureEvent $event,
        Session $session
    )
    {
        $session->addResult($event)
            ->shouldBeCalled();
        $this->afterFeature($event);
    }
}