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

use PhpGuard\Application\Event\GenericEvent;
use PhpGuard\Application\Event\ProcessEvent;
use PhpGuard\Application\Plugin\Plugin;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BehatPlugin
 *
 */
class BehatPlugin extends Plugin
{
    public function __construct()
    {
        $this->setOptions(array());
    }

    public function configure()
    {
        parent::configure();
        $container = $this->container;

        $executable = realpath(__DIR__.'/../bin/behat-phpguard');
        $this->options['executable'] = $executable;
        $container->setShared('behat.inspector',function(){
            return new Inspector();
        });
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'behat';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Behat';
    }

    public function start(GenericEvent $event)
    {
        $results = $this->container->get('behat.inspector')
            ->runAll();

        $event->addProcessEvent(new ProcessEvent($this,$results));
    }

    /**
     * Run all command
     *
     * @return \PhpGuard\Application\Event\ProcessEvent
     */
    public function runAll()
    {
        $results = $this->container->get('behat.inspector')
            ->runAll();

        return new ProcessEvent($this,$results);
    }

    /**
     * @param array $paths
     *
     * @return \PhpGuard\Application\Event\ProcessEvent
     */
    public function run(array $paths = array())
    {
        $results = $this->container->get('behat.inspector')
            ->run($paths);

        return new ProcessEvent($this,$results);
    }

    /**
     * @param OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cli'               => null,
            'run_all_cli'       => null,
            'coverage'          => false,
            'all_after_pass'    => false,
            'all_on_start'      => false,
            'keep_failed'       => false,
        ));
    }
}
