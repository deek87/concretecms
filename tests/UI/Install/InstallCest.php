<?php

namespace Concrete\UITests\Install;

use AcceptanceTester;

class InstallCest
{

    public function checkInstallRedirect(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->wait(1);
        $I->canSeeInCurrentUrl('/index.php/install');

    }


    public function checkRequirements(AcceptanceTester $I) {
        $I->seeElement('.btn-primary');
        $I->clickWithLeftButton('.btn-primary');
        $I->waitForText("Required Items");
        $I->seeNumberOfElements('i.fa-check',[16,18]);
        $I->clickWithLeftButton('.btn-primary');
    }

    public function enterInvalidDetails(AcceptanceTester $I) {
        $I->fillField('SITE','Concrete5 Travis');
        $I->fillField('uEmail','test@concrete5-test.test');
        $I->fillField('uPassword','RandomPassword1');
        $I->fillField('uPasswordConfirm','RandomPassword2');
        $I->fillField('DB_SERVER','unkownhost');
        $I->fillField('DB_USERNAME','invalid_user');
        $I->fillField('DB_DATABASE','concrete5_tests');
        $I->checkOption('form input[name="privacy"]');
        $I->seeCheckboxIsChecked('form [name="privacy"]');
        $I->clickWithLeftButton('.btn-primary');
        $I->waitForText('The two passwords provided do not match.',20);

    }

    public function enterDetails(AcceptanceTester $I) {
        $I->fillField('uPassword','RandomPassword1');
        $I->fillField('uPasswordConfirm','RandomPassword1');
        $I->fillField('DB_SERVER','127.0.0.1');
        $I->fillField('DB_USERNAME','travis');
        $I->fillField('DB_DATABASE','concrete5_tests');
        $I->clickWithLeftButton('.btn-primary');
        $I->seeElement('#ignore-warnings');
        $I->checkOption('#ignore-warnings');
    }

    public function checkInstallTime(AcceptanceTester $I) {
        $I->clickWithLeftButton('.btn-primary');
        $I->waitForText("Installation Complete",180);
        $I->saveSessionSnapshot('login_admin');
        $I->click('Edit Your Site');

        $I->waitForText('Learn the basics');
        // Fix for chrome deleting cookies on each test
        AcceptanceTester::$sessionCookie = $I->grabCookie('CONCRETE5');
        AcceptanceTester::$cookies = $I->grabCookieObject();
        $I->click('button.ui-dialog-titlebar-close');
    }

}
