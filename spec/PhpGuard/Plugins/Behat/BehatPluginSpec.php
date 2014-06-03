<?php

namespace spec\PhpGuard\Plugins\Behat;

use PhpGuard\Application\Util\Runner;
use PhpGuard\Plugins\Behat\Inspector;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PhpGuard\Application\Container\ContainerInterface;
use PhpGuard\Application\Event\GenericEvent;

class BehatPluginSpec extends ObjectBehavior
{
    function let(
        ContainerInterface $container,
        Inspector $inspector,
        Runner $runner
    )
    {
        $container->get('behat.inspector')
            ->willReturn($inspector)
        ;
        $container->setShared('behat.inspector',Argument::any())
            ->willReturn();
        $container->get('runner')->willReturn($runner);
        $this->setContainer($container);

        $this->configure();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\BehatPlugin');
    }

    function it_should_configure_inspector(
        ContainerInterface $container,
        Runner $runner
    )
    {
        $this->beConstructedWith();
        /*$container->get('runner')
            ->shouldBeCalled()
            ->willReturn($runner)
        ;
        $runner->findExecutable('behat-phpguard')
            ->shouldBeCalled()
            ->willReturn('some')
        ;*/
        $container->setShared('behat.inspector',Argument::any())
            ->shouldBeCalled()
        ;
        $this->configure();
    }

    function it_delegate_run(
        Inspector $inspector
    )
    {
        $inspector->run(array('some'))
            ->shouldBeCalled()
            ->willReturn(array('result'))
        ;

        $processEvent = $this->run(array('some'));
        $processEvent->shouldHaveType('PhpGuard\Application\Event\ProcessEvent');
        $processEvent->getResults()->shouldContain('result');
    }

    function it_delegate_run_all(
        Inspector $inspector
    )
    {
        $inspector->runAll()
            ->shouldBeCalled()
            ->willReturn(array('result'))
        ;

        $processEvent = $this->runAll();
        $processEvent->shouldHaveType('PhpGuard\Application\Event\ProcessEvent');
        $processEvent->getResults()->shouldContain('result');
    }

    function it_should_run_all_on_start_if_defined(
        Inspector $inspector,
        GenericEvent $event
    )
    {
        $event->addProcessEvent(Argument::any())
            ->shouldBeCalled();
        $inspector->runAll()
            ->shouldBeCalled()
            ->willReturn(array())
        ;

        $this->start($event);
    }
}
