<?php

/*
 * This file is part of the PhpGuard project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpGuard\Plugins\Behat\Bridge\Context;
use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\PyStringNode;
use PhpGuard\Application\Test\ApplicationTester;
use PhpGuard\Application\Test\TestApplication;
use PhpGuard\Application\Util\Filesystem;
use PhpGuard\Plugins\PhpSpec\Bridge\Console\Application;

/**
 * Class PhpGuardContext
 *
 */
class PhpGuardContext extends BehatContext
{
    /**
     * @var string|null
     */
    protected $workDir;

    /**
     * @var ApplicationTester|null
     */
    protected $applicationTester = null;

    /**
     * @var Application|null
     */
    protected $application = null;

    /**
     * @BeforeScenario
     */
    public function createWorkDir()
    {
        $this->workDir = sprintf(
            '%s/phpguard-behat/%s',
            sys_get_temp_dir(),
            uniqid('context_')
        );

        $fs = new Filesystem();
        $fs->mkdir($this->workDir,0777);
        chdir($this->workDir);
    }

    /**
     * @AfterScenario
     */
    public function removeWorkDir()
    {
        Filesystem::create()
            ->cleanDir($this->workDir)
        ;
    }

    /**
     * @When /^(?:|I )start phpguard$/
     */
    public function iStartPhpGuard()
    {
        $this->applicationTester = $this->createApplicationTester();
        $this->applicationTester->run('start',array('decorated'=>false));
    }

    /**
     * @When /^I run phpguard with "([^"]*)" arguments$/
     */
    public function iRunPhpGuardWith($arguments)
    {
        $this->applicationTester = $this->createApplicationTester();
        $this->applicationTester->run($arguments,array('decorated'=>false));
    }

    /**
     * @When /^I (?:create |modify )file "(?P<file>[^"]+)" with contents:$/
     */
    public function iDoSomethingWithFile($file,PyStringNode $string)
    {
        $this->theFileContains($file,$string);
        $this->evaluate();
    }

    /**
     * @Given /^(?:|the ) file "(?P<file>[^"]+)" contains:$/
     */
    public function theFileContains($file,PyStringNode $string)
    {
        $fs = Filesystem::create();
        $dirname = dirname($file);
        if(!file_exists($dirname)){
            $fs->mkdir($dirname);
        }

        $fs->putFileContents($file,$string->getRaw());
    }

    /**
     * @Given /^the config file contains:$/
     */
    public function theConfigFileContains(PyStringNode $string)
    {
        file_put_contents('phpguard.yml', $string->getRaw());
    }

    /**
     * @Then /^(?:|I )should see "(?P<message>[^"]*)"$/
     */
    public function iShouldSee($message)
    {
        expect($this->applicationTester->getDisplay())->toMatch('/'.preg_quote($message, '/').'/sm');
    }

    /**
     * @Then /^(?:|I )should see file "(?P<file>[^"]+)"$/
     */
    public function iShouldSeeFile($file)
    {
        expect(file_exists($file))->toBe(true);
    }

    /**
     * @return Application|null
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Evaluate filesystem changes
     */
    protected function evaluate()
    {
        $this->getApplication()->getContainer()
            ->get('phpguard')
            ->evaluate()
        ;
    }

    /**
     * @return ApplicationTester
     */
    protected function createApplicationTester()
    {
        $this->application = $application = new TestApplication();

        $application->setAutoExit(false);

        return new ApplicationTester($application);
    }
}