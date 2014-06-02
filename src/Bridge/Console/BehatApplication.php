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
use PhpGuard\Plugins\Behat\Bridge\PhpGuardExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class Application
 *
 */
class BehatApplication extends BaseApplication
{
    public function __construct()
    {
        BaseApplication::__construct('PhpGuard::Behat');
    }

    protected function loadCoreExtension(ContainerBuilder $container, array $configs)
    {
        BaseApplication::loadCoreExtension($container, $configs);
    }
}
