<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Event\ScenarioEvent;
use WebDriver\WebDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    private $parameters;

    private $sessions = array();

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->useContext('access', new AccessContext());
    }

    /**
      * @BeforeScenario
      */
    public function before($event)
    {
        if (empty($this->parameters['instances'])) {
            return; //Local Setup doesn't need to create sessions - use default
        }
        foreach ($this->parameters['instances'] as $browserName => $capabilities) {
            $driver = new Selenium2Driver($browserName, $capabilities, 'http://'. $this->parameters['key'].':'.$this->parameters['secret'].'@'.$this->parameters['wd_host']);
            $session = new Session($driver);
            $this->getMink()->registerSession($browserName, $session);
            $this->sessions[] = $browserName;
        }
        $this->getMink()->setDefaultSessionName(key($this->parameters['instances']));
    }

     /**
      * @AfterScenario
      */
    public function tearDown(ScenarioEvent $event)
    {
        if (empty($this->parameters['instances'])) {
            return; // Local Setup doesn't need to transmit data to testingbot
        }
        $url = $this->getSession()->getDriver()->wdSession->getURL();

        $parts = explode("/", $url);
        $sessionID = $parts[sizeof($parts) - 1];

        $data = array(
            'session_id' => $sessionID,
            'client_key' => $this->parameters['key'],
            'client_secret' => $this->parameters['secret'],
            'success' => ($event->getResult() === 0) ? true : false,
            'name' => $event->getScenario()->getTitle()
        );

        $this->apiCall($data);
    }

    public function jqueryWait($duration = 1000)
    {
        $this->getSession()->wait($duration, '(0 === jQuery.active)');
    }

    protected function apiCall(array $postData)
    {
        $data = http_build_query($postData);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://testingbot.com/hq");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);
        curl_close($curl);
    }
}
