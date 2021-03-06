<?php

/*
 * This file is part of the PhpGuard project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpGuard\Plugins\Behat;

use PhpGuard\Application\Container\ContainerAware;
use PhpGuard\Application\Container\ContainerInterface;
use PhpGuard\Application\Event\ResultEvent;
use PhpGuard\Application\PhpGuard;
use PhpGuard\Listen\Exception\RuntimeException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class Inspector
 *
 */
class Inspector extends ContainerAware
{
    const RUN_ALL_SUCCESS_MESSAGE = 'Run all success';
    const RUN_ALL_FAILED_MESSAGE = 'Run all failed';

    /**
     * @var array
     */
    private $runCliArgs;

    /**
     * @var array
     */
    private $runAllArgs;

    private $options;

    /**
     * @var Session
     */
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    public static function getRerunFileName()
    {
        $dir = PhpGuard::getPluginCache('behat');
        $file = $dir.DIRECTORY_SEPARATOR.'rerun.dat';

        return $file;
    }

    public function setContainer(ContainerInterface $container)
    {
        parent::setContainer($container);

        $options            = $this->getPlugin()->getOptions();
        $executable         = $options['executable'].' ';

        $cli                = !is_null($options['cli']) ? $options['cli']:'';
        $runAllCli          = !is_null($options['run_all_cli']) ? $options['run_all_cli']:$cli;
        $this->runCliArgs   = explode(' ',trim($executable.$cli));
        $this->runAllArgs   = explode(' ',trim($executable.$runAllCli));
        $this->options      = $options;
    }

    public function run(array $paths = array())
    {
        if (empty($paths)) {
            throw new RuntimeException(
                'Can not run <comment>behat plugin</comment> '.
                'with an <comment>empty paths</comment>'
            );
        }
        $arguments = $this->runCliArgs;

        $features = array();
        foreach ($paths as $path) {
            $path = trim($path);
            if($path){
                $features[] = ltrim(str_replace(getcwd(),'',$path),'\\/');
            }
        }


        $rerunFile = static::getRerunFileName();
        $features = array_unique($features);
        $content = implode("\n",$features);
        $this->getFilesystem()->putFileContents($rerunFile,$content);

        $arguments[] = '--rerun='.$rerunFile;

        $builder = new ProcessBuilder($arguments);
        $process = $this->getRunner()->run($builder);

        $results = array();
        if ($process->getExitCode()===0) {
            $format = 'Run success: <highlight>%s</highlight>';
            foreach ($paths as $path) {
                $message = sprintf($format,$path);
                $results[] = ResultEvent::createSucceed($message);
            }
            if ($this->options['all_after_pass']) {
                $results = array_merge($results,$this->doRunAll());
            }
        }else{
            $this->session = Session::create();
            $results = $this->session->getResults();
        }

        return $results;
    }

    public function runAll()
    {
        $results = $this->doRunAll();

        return $results;
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        //return $this->getFilesystem()->pathExists(static::getRerunFileName());
        return count($this->session->getResults()) > 0;
    }

    /**
     * @return ResultEvent[]
     */
    private function doRunAll()
    {
        $results = array();

        $arguments = $this->runAllArgs;

        $this->session = Session::create();

        if ($this->isFailed()) {
            $rerunFile = $this->session->generateRerunFile();
            $arguments[] = '--rerun='.$rerunFile;
        }

        $builder = new ProcessBuilder($arguments);

        $this->getRunner()->run($builder);

        $this->session = Session::create();

        if (!$this->isFailed()) {
            $results[] = ResultEvent::createSucceed(static::RUN_ALL_SUCCESS_MESSAGE);
        } else {
            $results = $this->session->getResults();
            $results[] = ResultEvent::createFailed(static::RUN_ALL_FAILED_MESSAGE);
        }

        return $results;
    }

    /**
     * @return \PhpGuard\Application\Util\Runner
     */
    private function getRunner()
    {
        return $this->container->get('runner');
    }

    /**
     * @return BehatPlugin
     */
    private function getPlugin()
    {
        return $this->container->get('plugins.behat');
    }

    /**
     * @return \PhpGuard\Application\Util\Filesystem
     */
    private function getFilesystem()
    {
        return $this->container->get('filesystem');
    }
}
