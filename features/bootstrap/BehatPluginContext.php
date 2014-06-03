<?php

namespace PhpGuard\Plugins\Behat\Acceptance;

use Behat\Behat\Context\BehatContext;
use PhpGuard\Application\Test\ApplicationTester;
use PhpGuard\Application\Util\Filesystem;
use PhpGuard\Plugins\Behat\Bridge\Console\BehatApplication;
use PhpGuard\Plugins\Behat\Bridge\Context\PhpGuardContext;

class BehatPluginContext extends BehatContext
{
    protected $behatApplication;
    protected $behatTester;

    /**
     * @BeforeScenario
     */
    public function createBootstrapFile()
    {
        $contextClass = uniqid("FeatureContext");
        $content = <<<EOC
<?php

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;

class {$contextClass} extends BehatContext
{
    public function __construct(array \$parameters)
    {
    }

    /**
     * @Given /^I have passed step$/
     */
    public function iHavePassStep()
    {
        return true;
    }

    /**
     * @Then /^I have failed step$/
     */
    public function iHaveFailedStep()
    {
        throw new \Exception("failed");
    }
}
EOC;

        $fs = Filesystem::create();

        $file = 'features/bootstrap/'.$contextClass.'.php';
        $dir = dirname($file);
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
        }
        $fs->putFileContents($file,$content);

        $behatContents = <<<EOC
default:
    context:
        class: {$contextClass}
EOC;
        $fs->putFileContents('behat.yml',$behatContents);
    }

    /**
     * @When /^I run behat$/
     */
    public function iRunBehat()
    {
        $tester = $this->createApplicationTester();
        $this->getPhpGuardContext()->setApplicationTester($tester);
        $tester->run('--out=stdout',array('decorated'=>false));
    }

    /**
     * @return PhpGuardContext
     */
    protected function getPhpGuardContext()
    {
        return $this->getMainContext()->getSubcontext('phpguard');
    }

    /**
     * @return ApplicationTester
     */
    protected function createApplicationTester()
    {
        $app = new BehatApplication();
        $app->setAutoExit(false);
        $app->setCatchExceptions(true);

        $tester = new ApplicationTester($app);
        return $tester;
    }
}