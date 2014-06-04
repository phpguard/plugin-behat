<?php

namespace spec\PhpGuard\Plugins\Behat;

use Behat\Behat\Event\StepEvent;
use PhpGuard\Application\Container\ContainerInterface;
use PhpGuard\Application\PhpGuard;
use PhpGuard\Application\Spec\PluginBehavior;
use PhpGuard\Application\Util\Filesystem;
use PhpGuard\Application\Util\Runner;
use PhpGuard\Plugins\Behat\BehatPlugin;
use PhpGuard\Application\Spec\Prophecy\Argument;
use PhpGuard\Plugins\Behat\Event\ResultEvent;
use PhpGuard\Plugins\Behat\Inspector;
use PhpGuard\Plugins\Behat\Session;
use Symfony\Component\Process\Process;

class InspectorSpec extends PluginBehavior
{
    private $options;

    private $rerunFile;

    static private $cwd;


    function let(
        ContainerInterface $container,
        Runner $runner,
        Process $process,
        BehatPlugin $plugin,
        Filesystem $filesystem
    )
    {
        if(is_null(static::$cwd)){
            static::$cwd = getcwd();
        }

        if(!is_dir(static::$tmpDir)){
            mkdir(static::$tmpDir,0777,true);
        }

        chdir(static::$tmpDir);
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

        $this->rerunFile = Inspector::getRerunFileName();
        $this->setContainer($container);
    }

    function letgo()
    {
        chdir(static::$cwd);
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
        Process $process,
        Filesystem $filesystem
    )
    {

        $rerunFile = Inspector::getRerunFileName();
        $runner->run(Argument::runnerRun('executable,'.$rerunFile))
            ->shouldBeCalled()
            ->willReturn($process)
        ;
        $process->getExitCode()
            ->shouldBeCalled()
            ->willReturn(0)
        ;
        $filesystem->putFileContents($rerunFile,Argument::any())
            ->shouldBeCalled()
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
        BehatPlugin $plugin,
        Filesystem $filesystem
    )
    {

        $filesystem->putFileContents($this->rerunFile,Argument::any())
            ->willReturn();
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
}