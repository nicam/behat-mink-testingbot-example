<?php

use Behat\Behat\Context\BehatContext;
/**
 * Features context.
 */
class AccessContext extends BehatContext
{
    /**
     * @When /^I click on "([^"]*)"$/
     */
    public function iClickOn($linkname)
    {
        $this->getMainContext()->clickLink($linkname);
    }

    /**
     * @Given /^I waited until the page loads$/
     */
    public function iWaitedUntilThePageLoads()
    {
        $this->getMainContext()->jqueryWait(20000);
    }

    /**
     * @Then /^I wait for the suggestion box to appear$/
     */
    public function iWaitForTheSuggestionBoxToAppear()
    {
        $this->getMainContext()->getSession()->wait(5000,
            "$('.suggestions-results').children().length > 0"
        );
    }
}