<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ResponseTextException;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given I am logged as admin
     */
    public function iAmLoggedAsAdmin()
    {
        $this->visit('/login');
        $this->fillField('_username', 'admin');
        $this->fillField('_password', 'test');
        $this->pressButton('Логин');
    }

    /**
     * @Then I should see edit solutions flash
     */
    public function iShouldSeeEditSolutionsFlash()
    {
        $this->matchRegexpOnPage('/Решение ID: \d+ отредактировано/ui');
    }

    /**
     * @Then I should see delete solutions flash
     */
    public function iShouldSeeDeleteSolutionsFlash()
    {
        $this->matchRegexpOnPage('/Решение ID: \d+ удалено/ui');
    }

    /**
     * @param string $regex
     * @throws ResponseTextException
     */
    protected function matchRegexpOnPage($regex)
    {
        $actual = $this->getSession()->getPage()->getText();
        $actual = preg_replace('/\s+/u', ' ', $actual);
        $message = sprintf('The regexp "%s" was not matched on the current page.', $regex);

        if (!preg_match($regex, $actual)) {
            throw new ResponseTextException($message, $this->getSession()->getDriver());
        }
    }
}
