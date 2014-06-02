<?php

namespace spec\PhpGuard\Plugins\Behat\Bridge;

use PhpGuard\Application\Spec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\Bridge\Extension');
    }

    function it_should_be_the_behat_extension()
    {
        $this->shouldImplement('Behat\Behat\Extension\ExtensionInterface');
    }

    function it_should_configure_extension(
        ContainerBuilder $container
    )
    {

    }
}