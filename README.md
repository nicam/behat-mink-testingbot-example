##Behat with Mink for Mobile Devices running on testingbot.com

It was quite hard to get a working setup running that works with iOS devices and Selenium on testingbot so I made this a bit more advanced example.

###Setup

####Clone this repo

	git clone https://github.com/nicam/behat-mink-testingbot-example.git

####Install Composer
	curl http://getcomposer.org/installer | php
	php composer.phar install
	
####Create a testingbot account
Create a testingbot account on www.testingbot.com and get your Key and Secret from <a href="https://testingbot.com/members/user/edit">here</a>

####Enter Key & Secret into behat.yml
Replace all places where it says `YOURKEYHERE` with your key and `YOURSECRETHERE` with your secret.

####Run Behat
#####Run it on an iPhone
	bin/behat -p iphone
#####Run it on firefox
	bin/behat -p firefox

You should get the following output:

    nicam-air:behat nicam$ bin/behat -p iphone
    Feature: Access

      @javascript
      Scenario: Searching for a page with autocompletion # features/access.feature:4
        Given I am on "/wiki/Main_Page"                  # FeatureContext::visit()
        When I fill in "search" with "Behavior Driv"     # FeatureContext::fillField()
        And I wait for the suggestion box to appear      # AccessContext::iWaitForTheSuggestionBoxToAppear()
        Then I should see "Behavior-driven development"  # FeatureContext::assertPageContainsText()

    1 Szenario (1 passed)
    4 Steps (4 passed)
    0m23.434s
    
    
####Look at the test results
You can now see your testing results <a href="https://testingbot.com/members">here</a> and even watch a video!


###Additional Stuff
If you want to run it on pages that are only accessible from your network or your machine then you need the testingbot-tunnel. It's just a jar that you need to run. Make sure you change the urls in the behat.yml. See the comments about testingbot-tunnel.