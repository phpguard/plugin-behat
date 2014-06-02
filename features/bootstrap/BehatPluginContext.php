<?php

use Behat\Behat\Context\BehatContext;
use PhpGuard\Application\Util\Filesystem;

class BehatPluginContext extends BehatContext
{
    /**
     * @BeforeScenario
     */
    public function createBootstrapFile()
    {
        $content = <<<EOC
<?php

use Behat\Behat\Context\BehatContext;

class FeatureContext extends BehatContext
{
    public function __construct(array \$parameters)
    {
    }

    /**
     * @Then /^I have pass step$/
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
        throw new \Exception("failed step");
    }
}

EOC;

        $file = 'features/bootstrap/FeatureContext.php';
        $dir = dirname($file);
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
        }
        Filesystem::create()
            ->putFileContents($file,$content)
        ;
    }
}