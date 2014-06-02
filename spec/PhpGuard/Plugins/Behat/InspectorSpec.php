<?php

namespace spec\PhpGuard\Plugins\Behat;

use PhpGuard\Application\Container\ContainerInterface;
use PhpGuard\Application\Spec\PluginBehavior;
use PhpGuard\Application\Util\Filesystem;
use PhpGuard\Application\Util\Runner;
use PhpGuard\Plugins\Behat\BehatPlugin;
use PhpGuard\Application\Spec\Prophecy\Argument;
use PhpGuard\Plugins\Behat\Inspector;
use Symfony\Component\Process\Process;

class InspectorSpec extends PluginBehavior
{
    private $options;

    function let(
        ContainerInterface $container,
        Runner $runner,
        Process $process,
        BehatPlugin $plugin,
        Filesystem $filesystem
    )
    {
        $container->get('plugins.behat')
            ->willReturn($plugin)
        ;

        $container->get('runner')
            ->willReturn($runner)
        ;

        $this->options = array(
            'all_after_pass'    => false,
            'cli'               => 'run_cli',
            'run_all_cli'       => 'run_all_cli',
            'executable'        => 'executable',
        );
        $plugin->getOptions()
            ->willReturn($this->options)
        ;
        $runner->run(Argument::any())
            ->willReturn($process)
        ;
        $filesystem->pathExists(Inspector::getRerunFileName())
            ->willReturn(false);
        $container->get('filesystem')->willReturn($filesystem);


        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Plugins\Behat\Inspector');
    }

    function it_should_extends_the_ContainerAware()
    {
        $this->shouldHaveType('PhpGuard\\Application\\Container\\ContainerAware');
    }

    function it_should_run_behat(
        Runner $runner,
        Process $process
    )
    {

        $runner->run(Argument::runnerRun(array('executable','hello.feature','world.feature')))
            ->shouldBeCalled()
            ->willReturn($process)
        ;
        $process->getExitCode()
            ->shouldBeCalled()
            ->willReturn(0)
        ;
        $paths = array('hello.feature','world.feature');
        $results = $this->run($paths);
        $results->shouldHaveCount(2);
        $results->shouldContainMessage('hello.feature');
        $results->shouldContainMessage('world.feature');
    }

    function it_throws_when_running_with_an_empty_path()
    {
        $this->shouldThrow('RuntimeException')
            ->duringRun(array())
        ;
    }

    function it_should_run_all_after_running_features_passed(
        ContainerInterface $container,
        Runner $runner,
        Process $process,
        BehatPlugin $plugin
    )
    {

        $options = $this->options;
        $options['all_after_pass'] = true;


        $plugin->getOptions()->willReturn($options);

        $this->setContainer($container);

        $runner->run(Argument::any())
            ->shouldBeCalled()
            ->willReturn($process)
        ;
        $process->getExitCode()->willReturn(0);

        $paths = array('some.feature');
        $results = $this->run($paths);

        $results->shouldContainMessage('some.feature');
        $results->shouldContainMessage(Inspector::RUN_ALL_SUCCESS_MESSAGE);
    }

    function it_should_create_success_result_when_test_passed(
        Runner $runner
    )
    {
        $runner->run(Argument::runnerRun('executable,run_all_cli'))
            ->shouldBeCalled()
        ;
        $results = $this->runAll();
        $results->shouldHaveCount(1);
        $results->shouldContainMessage(Inspector::RUN_ALL_SUCCESS_MESSAGE);
    }

    function it_should_parse_rerun_file_result(
        Filesystem $filesystem,
        Runner $runner
    )
    {
        $content = <<<EOC
some.feature:4

EOC;

        $featureContent = <<<EOC
Feature: Some feature
    Some description

    Scenario: some_scenario

EOC;

        $filesystem->getFileContents(Inspector::getRerunFileName())
            ->willReturn($content);

        $filesystem->pathExists(Inspector::getRerunFileName())
            ->shouldBeCalled()
            ->willReturn(true);

        $filesystem->getFileContents('some.feature')
            ->willReturn($featureContent)
        ;

        $runner->run(Argument::runnerRun('executable,run_all_cli'))
            ->shouldBeCalled()
        ;
        $results = $this->runAll();
        $results->shouldHaveCount(2);

        $results->shouldContainMessage(Inspector::RUN_ALL_FAILED_MESSAGE);
        $results->shouldContainMessage('some_scenario');
    }

    function it_should_keep_failed_test_to_run(
        Filesystem $filesystem,
        Runner $runner
    )
    {
        $file = Inspector::getRerunFileName();
        $filesystem->pathExists($file)
            ->willReturn(true);
        $filesystem->getFileContents($file)
            ->shouldBeCalled()
            ->willReturn('')
        ;
        $runner->run(Argument::runnerRun('run_all_cli,'.$file))
            ->shouldBeCalled();

        $results = $this->runAll();
        $results->shouldContainMessage(Inspector::RUN_ALL_FAILED_MESSAGE);
    }
}