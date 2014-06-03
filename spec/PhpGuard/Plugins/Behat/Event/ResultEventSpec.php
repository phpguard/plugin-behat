<?php

namespace spec\PhpGuard\Plugins\Behat\Event;

use Behat\Behat\Event\ScenarioEvent;
use Behat\Gherkin\Node\ScenarioNode;
use PhpGuard\Application\Spec\ObjectBehavior;
use Prophecy\Argument;

class ResultEventSpec extends ObjectBehavior
{
    function let(
        ScenarioEvent $event,
        ScenarioNode $node
    )
    {
        $event->getScenario()->willReturn($node);
        $this->beConstructedWith($event);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\Event\ResultEvent');
    }
}