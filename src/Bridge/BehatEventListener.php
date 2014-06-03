<?php

/*
 * This file is part of the PhpGuard project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpGuard\Plugins\Behat\Bridge;

use Behat\Behat\Event\FeatureEvent;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Event\StepEvent;
use PhpGuard\Application\Bridge\CodeCoverage\CodeCoverageSession;
use PhpGuard\Application\Container;
use PhpGuard\Application\Util\Filesystem;
use PhpGuard\Plugins\Behat\Inspector;
use PhpGuard\Plugins\Behat\Event\ResultEvent;
use PhpGuard\Plugins\Behat\Session;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BehatEventListener
 *
 */
class BehatEventListener implements EventSubscriberInterface
{

    /**
     * @var \PhpGuard\Application\Bridge\CodeCoverage\CodeCoverageSession
     */
    private $coverageSession;

    /**
     * @var \PhpGuard\Plugins\Behat\Session
     */
    private $session;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        CodeCoverageSession $coverageSession=null,
        Session $session = null,
        Filesystem $filesystem = null
    )
    {
        if(is_null($filesystem)){
            $filesystem = Filesystem::create();
        }

        if(is_null($session)){
            $session = new Session();
        }

        $this->coverageSession  = $coverageSession;
        $this->filesystem       = $filesystem;
        $this->session          = $session;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    static public function getSubscribedEvents()
    {
        return array(
            'beforeStep' => array('beforeStep',-10),
            'afterStep' => array('afterStep',-10),
            'afterFeature' => array('afterFeature',-10),
            'afterScenario' => array('afterScenario',-10),
            'beforeSuite' => array('beforeSuite',-10),
            'afterSuite' => array('afterSuite',-10),
        );
    }

    public function beforeStep(StepEvent $event)
    {
        $title  = $event->getStep()->getParent()->getTitle();
        $text   = $event->getStep()->getText();
        $type   = $event->getStep()->getType();

        $id = sprintf('Scenario: %s on Step: %s %s',$title,$type,$text);
        $this->startCoverage($id);
    }

    public function afterStep(StepEvent $event)
    {
        $this->session->addResult($event);
        $this->stopCoverage();
    }

    public function afterScenario(ScenarioEvent $event)
    {
        $this->session->addResult($event);
    }

    public function afterFeature(FeatureEvent $event)
    {
        $this->session->addResult($event);
    }

    public function beforeSuite()
    {
        $this->session->start();
    }

    public function afterSuite()
    {
        if($this->coverageSession){
            $this->coverageSession->saveState();
        }
        $this->session->stop();
    }

    private function startCoverage($name)
    {
        if($this->coverageSession){
            $this->coverageSession->start($name);
        }
    }

    private function stopCoverage()
    {
        if($this->coverageSession){
            $this->coverageSession->stop();
        }
    }
}