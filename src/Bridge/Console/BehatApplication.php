<?php

/*
 * This file is part of the PhpGuard project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpGuard\Plugins\Behat\Bridge\Console;

use Behat\Behat\Console\BehatApplication as BaseApplication;
use PhpGuard\Application\Bridge\CodeCoverage\CodeCoverageSession;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class Application
 *
 */
class BehatApplication extends BaseApplication
{
    /**
     * @var CodeCoverageSession
     */
    private $coverageSession;

    private $internalOutput;

    public function __construct(CodeCoverageSession $coverageSession=null)
    {
        parent::__construct('PhpGuard-Behat');
        $this->coverageSession = $coverageSession;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->internalOutput = $output;
        return parent::doRun($input, $output);
    }

    public function renderException($e, $output=null)
    {
        if(is_null($output)){
            $output = $this->internalOutput;
        }
        parent::renderException($e, $output);
    }

    public function saveCoverage()
    {
        if($this->coverageSession){
            $this->coverageSession->saveState();
        }
    }

    protected function loadCoreExtension(ContainerBuilder $container, array $configs)
    {
        // patch configuration here
        $this->registerSubscriber($container);
        parent::loadCoreExtension($container, $configs);
    }

    protected function registerSubscriber(ContainerBuilder $container)
    {
        $definition = new Definition('PhpGuard\Plugins\Behat\Bridge\BehatEventListener',array($this->coverageSession));
        $definition->addTag('behat.event_subscriber');
        $container->setDefinition('behat.event_subscriber.phpguard',$definition);
    }
}
