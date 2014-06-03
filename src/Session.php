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
use Behat\Behat\Event\FeatureEvent;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Event\StepEvent;
use PhpGuard\Application\PhpGuard;
use PhpGuard\Application\Util\Filesystem;
use PhpGuard\Plugins\Behat\Event\ResultEvent;

/**
 * Keep tracking failed behat test
 *
 */
class Session implements \Serializable
{
    const FILENAME = 'behat.session.dat';

    /**
     * @var ResultEvent[]
     */
    private $features = array();

    /**
     * @var ResultEvent[]
     */
    private $scenarios = array();

    /**
     * @var ResultEvent[]
     */
    private $steps = array();

    /**
     * An array of failed results
     * @var ResultEvent[]
     */
    private $results = array();

    /**
     * @var string
     */
    private $path;

    private $fs;

    public function __construct(Filesystem $filesystem=null)
    {
        $this->path = sprintf('%s/%s',
            PhpGuard::getPluginCache('behat'),
            static::FILENAME
        );

        if(is_null($filesystem)){
            $filesystem = new Filesystem();
        }
        $this->fs = $filesystem;
    }

    static public function create()
    {
        $dir    = PhpGuard::getPluginCache('behat');
        $file   = $dir.DIRECTORY_SEPARATOR.static::FILENAME;
        clearstatcache($file);
        if(file_exists($file)){
            return Filesystem::create()->unserialize($file);
        }else{
            return new self();
        }
    }

    /**
     * {inheritdoc}
     */
    public function serialize()
    {
        $data = array(
            'features'  => $this->features,
            'scenarios' => $this->scenarios,
            'steps'     => $this->steps,
            'results'   => $this->results,
            'path'      => $this->path,
            'fs'        => $this->fs,
        );

        return serialize($data);
    }

    /**
     * {inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->features     = $data['features'];
        $this->scenarios    = $data['scenarios'];
        $this->steps        = $data['steps'];
        $this->results      = $data['results'];
        $this->path         = $data['path'];
        $this->fs           = $data['fs'];
    }

    public function start()
    {
        $this->features     = array();
        $this->scenarios    = array();
        $this->steps        = array();
    }

    /**
     * @param mixed $event
     */
    public function addResult($event)
    {
        $result     = new ResultEvent($event);
        $key        = $result->getKey();

        if($result->isSucceed()){
            // not tracking success results
            $this->removeFailed($result);
        }else{
            if($event instanceof StepEvent){
                $this->steps[$key]      = $result;
            }elseif($event instanceof ScenarioEvent){
                $this->scenarios[$key]  = $result;
            }elseif($event instanceof FeatureEvent){
                $this->features[$key]   = $result;
            }
        }
    }

    public function stop()
    {
        $this->cleanup();
        $this->saveResult();
    }

    public function getResults()
    {
        return $this->results;
    }

    private function saveResult()
    {
        $this->fs->serialize($this->path,$this);
    }

    private function cleanup()
    {
        // keep tracking unchecked results
        $unchecked = $this->results;

        $results = array_merge(
            $this->features,
            $this->scenarios,
            $this->steps
        );

        foreach($results as $event){
            $key = $event->getKey();
            $this->results[$key] = $event;
            if(isset($unchecked[$key])){
                unset($unchecked[$key]);
            }
        }

        // now checking removed features,scenarios,or steps
        foreach($unchecked as $event){
            $file = $event->getFile();
            $line = $event->getLine();
            $key = $event->getKey();
            if(!file_exists($file)){
                // always remove unexistent file
                $this->removeFailed($event);
            }else{
                $contents = file($event->getFile());
                $index = $line-1;
                $lineContent = isset($contents[$index]) ? $contents[$index]:"";
                if(false===strpos($lineContent,$event->getText())){
                    unset($this->results[$key]);
                }
            }
        }
    }

    private function removeFailed(ResultEvent $event)
    {
        if($event->isFeature()){
            $file = $event->getFile();
            foreach($this->results as $result){
                if($result->getFile()===$file){
                    unset($this->results[$result->getKey()]);
                }
            }

        }
        unset($this->results[$event->getKey()]);
    }
}