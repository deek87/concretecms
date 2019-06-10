<?php


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
        $I->wait(5);
        $I->see('The two passwords provided do not match.');

    }

    public function enterDetails(AcceptanceTester $I) {
        $I->fillField('uPassword','RandomPassword1');
        $I->fillField('uPasswordConfirm','RandomPassword1');
        $I->fillField('DB_SERVER','localhost');
        $I->fillField('DB_USERNAME','travis');
        $I->clickWithLeftButton('.btn-primary');
    }

    public function checkInstallTime(AcceptanceTester $I) {

        /* Actually too fast to see the text
         * $I->waitForText('Starting installation and creating directories.');
        $I->waitForText('Creating database tables.');
        $I->waitForText('Creating site.');
        $I->waitForText('Adding admin user.');
        $I->waitForText('Installing permissions & workflow.');
        $I->waitForText('Installing Custom Data Objects.');
        $I->waitForText('Creating home page.');
        $I->waitForText('Installing attributes.');
        $I->waitForText('Adding Basic block types.');
        $I->waitForText('Adding Navigation block types.');
        $I->waitForText('Adding Form block types.');
        $I->waitForText('Adding Express block types.');
        $I->waitForText('Adding Social block types.');
        $I->waitForText('Adding Calendar block types.');
        $I->waitForText('Adding Multimedia block types.');
        $I->waitForText('Adding Desktop block types.');
        $I->waitForText('Adding other block types.');
        $I->waitForText('Adding gathering data sources.');
        $I->waitForText('Page type basic setup.');
        $I->waitForText('Adding themes.');
        $I->waitForText('Installing automated jobs.');
        $I->waitForText('Installing dashboard.');
        $I->waitForText('Installing login and registration pages.');
        $I->waitForText('Adding image editor functionality.');
        $I->waitForText('Configuring site.');
        $I->waitForText('Importing files.');
        $I->waitForText('Adding pages and content.');
        $I->waitForText('Adding desktops.');
        $I->waitForText('Installing API.');
        $I->waitForText('Setting site permissions.');
        $I->waitForText('Finishing.');
        */
        $I->waitForElementVisible('#success-message',100);
        $I->waitForText("Installation Complete",10,'div.ccm-install-title ul.breadcrumb li.active');
    }
}
