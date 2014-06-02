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

use PhpGuard\Application\Plugin\Plugin;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BehatPlugin
 *
 */
class BehatPlugin extends Plugin
{
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

    /**
     * Run all command
     *
     * @return \PhpGuard\Application\Event\ProcessEvent
     */
    public function runAll()
    {
    }

    /**
     * @param array $paths
     *
     * @return \PhpGuard\Application\Event\ProcessEvent
     */
    public function run(array $paths = array())
    {
    }

    /**
     * @param  OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cli'           => null,
            'run_all_cli'   => null,
            'coverage'      => false,
        ));
    }
}