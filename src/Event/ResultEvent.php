<?php

/*
 * This file is part of the PhpGuard project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpGuard\Plugins\Behat\Event;

use Behat\Behat\Event\FeatureEvent;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Event\StepEvent;
use Behat\Gherkin\Node\AbstractNode;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Gherkin\Node\StepNode;
use PhpGuard\Application\Event\ResultEvent as BaseResultEvent;

/**
 * Class ResultEvent
 *
 */
class ResultEvent extends BaseResultEvent
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $file;

    /**
     * @var int
     */
    private $line;

    /**
     * @var AbstractNode
     */
    private $node;

    /**
     * @param mixed $event
     */
    public function __construct($event)
    {
        $map = array(
            StepEvent::PASSED       => static::SUCCEED,
            StepEvent::FAILED       => static::FAILED,
            StepEvent::PENDING      => static::FAILED,
            StepEvent::SKIPPED      => static::SUCCEED,
            StepEvent::UNDEFINED    => static::BROKEN,
        );

        $result = static::BROKEN;

        if(array_key_exists($event->getResult(),$map)){
            $result = $map[$event->getResult()];
        }
        $message = 'Unknown Result';
        if($event instanceof ScenarioEvent){
            $message = $this->buildScenarioEvent($event);
        }elseif($event instanceof FeatureEvent){
            $message = $this->buildFeatureEvent($event);
        }elseif($event instanceof StepEvent){
            $message = $this->buildStepEvent($event);
        }

        $this->key  = md5($this->file.$this->line);
        parent::__construct($result,$message);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return mixed
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    public function isFeature()
    {
        return $this->node instanceof FeatureNode;
    }

    public function isScenario()
    {
        return $this->node instanceof ScenarioNode;
    }

    public function isStep()
    {
        return $this->node instanceof StepNode;
    }

    private function buildStepEvent(StepEvent $event)
    {
        $node = $event->getStep();

        $message    = "    ".$node->getType().' '.$node->getText().' line: '.$node->getLine();
        $this->line = $node->getLine();
        $this->file = $node->getFile();
        $this->text = $node->getText();
        $this->node = $node;

        return $message;
    }

    private function buildScenarioEvent(ScenarioEvent $event)
    {
        $node       = $event->getScenario();
        $message    = "  Scenario: ".$node->getTitle().' line: '.$node->getLine();
        $this->file = $node->getFile();
        $this->line = $node->getLine();
        $this->text = $node->getTitle();
        $this->node = $node;

        return $message;
    }

    private function buildFeatureEvent(FeatureEvent $event)
    {
        $node = $event->getFeature();
        $message    = 'Feature: '.$node->getTitle().' line: '.$node->getLine();
        $this->file = $node->getFile();
        $this->line = $node->getLine();
        $this->text = $node->getTitle();
        $this->node = $node;

        return $message;
    }
}