<?php
use \AcceptanceTester;

class LoginCest
{
    public function _before(AcceptanceTester $I)
    {
        /**
         * If the last test wasn't braking in the middle,
         * the dev database can be renamed for the usage
         * of a clean test-database.
         */
        if (!file_exists('yiipass.sqlite.dev')) {
            rename('yiipass.sqlite', 'yiipass.sqlite.dev');
        } else {
            /**
             * If the last test broke, delete the database.
             * It could contain garbage data.
             */
            unlink('yiipass.sqlite');
        }

        copy('tests/_data/yiipass.sqlite', 'yiipass.sqlite');
    }

    public function _after(AcceptanceTester $I)
    {
        unlink('yiipass.sqlite');
        rename('yiipass.sqlite.dev', 'yiipass.sqlite');
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        // Frontpage
        $I->wantTo('ensure that password encryption with teamsecret works');
        $I->amOnPage('/');
        $I->see('Home');

        // Login
        $I->fillField('LoginForm[username]', 'admin');
        $I->fillField('LoginForm[password]', 'admin');
        $I->click('login-button');

        // Team secret setting
        $I->see('Please set the team secret for your team.');
        $I->fillField('TeamSecretForm[teamSecret]', 'myTeamSecret123');
        $I->click('Submit');
        $I->see('Team secret successfully saved.');
        $I->seeCookie('teamSecret');

        // Save password
        $I->click('Add Password');
        $I->fillField('Password[title]', 'test-password-title');
        $I->fillField('Password[group]', 'test-group');
        $I->fillField('Password[password]', 'test-password');
        $I->click('Create');

        // Check password
        // Click on link for index page.
        $I->click('Home');
        $I->amOnPage('/');

        // Check if password is wrong, if cookie is changed.
        $I->setCookie('teamSecret', 'wrong-value');
        $I->see('test-password-title');
        $I->click('test-password-title');
        $I->dontSee('test-password');

        // Check if password is right by right team secret.
        $I->see('Inserted team secret is wrong.');
        $I->fillField(['name' => 'TeamSecretForm[teamSecret]'], 'myTeamSecret123');

        // The cookie must be re-set in PHPBrowser. Otherwise the team secret cannot be set.
        $I->resetCookie('teamSecret');

        $I->click('Submit');
        $I->see('Team secret successfully saved.');
        $I->amOnPage('/');
        $I->see('test-password-title');
        $I->click('test-password-title');
        $I->see('test-password');
    }
}
