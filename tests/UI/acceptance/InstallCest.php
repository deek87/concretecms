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
        $I->fillField('DB_SERVER','127.0.0.1');
        $I->fillField('DB_USERNAME','travis');
        $I->fillField('DB_DATABASE','concrete5_tests');
        $I->clickWithLeftButton('.btn-primary');
        $I->seeElement('#ignore-warnings');
        $I->checkOption('#ignore-warnings');
    }

    public function checkInstallTime(AcceptanceTester $I) {
        $I->clickWithLeftButton('.btn-primary');
        $I->waitForText('Starting installation and creating directories.',60,'#install-progress-summary');
        $I->waitForText('Creating database tables.',60,'#install-progress-summary');
        $I->waitForText('Creating site.',60,'#install-progress-summary');
        $I->waitForText('Adding admin user.',60,'#install-progress-summary');
        $I->waitForText('Installing permissions & workflow.',60,'#install-progress-summary');
        $I->waitForText('Installing Custom Data Objects.',60,'#install-progress-summary');
        $I->waitForText('Creating home page.',60,'#install-progress-summary');
        $I->waitForText('Installing attributes.',60,'#install-progress-summary');
        $I->waitForText('Adding Basic block types.',60,'#install-progress-summary');
        $I->waitForText('Adding Navigation block types.',60,'#install-progress-summary');
        $I->waitForText('Adding Form block types.',60,'#install-progress-summary');
        $I->waitForText('Adding Express block types.',60,'#install-progress-summary');
        $I->waitForText('Adding Social block types.',60,'#install-progress-summary');
        $I->waitForText('Adding Calendar block types.',60,'#install-progress-summary');
        $I->waitForText('Adding Multimedia block types.',60,'#install-progress-summary');
        $I->waitForText('Adding Desktop block types.',60,'#install-progress-summary');
        $I->waitForText('Adding other block types.',60,'#install-progress-summary');
        $I->waitForText('Adding gathering data sources.',60,'#install-progress-summary');
        $I->waitForText('Page type basic setup.',60,'#install-progress-summary');
        $I->waitForText('Adding themes.',60,'#install-progress-summary');
        $I->waitForText('Installing automated jobs.',60,'#install-progress-summary');
        $I->waitForText('Installing dashboard.',60,'#install-progress-summary');
        $I->waitForText('Installing login and registration pages.',60,'#install-progress-summary');
        $I->waitForText('Adding image editor functionality.',60,'#install-progress-summary');
        $I->waitForText('Configuring site.',60,'#install-progress-summary');
        $I->waitForText('Importing files.',60,'#install-progress-summary');
        $I->waitForText('Adding pages and content.',60,'#install-progress-summary');
        $I->waitForText('Adding desktops.',60,'#install-progress-summary');
        $I->waitForText('Installing API.',60,'#install-progress-summary');
        $I->waitForText('Setting site permissions.',60,'#install-progress-summary');
        $I->waitForText('Finishing.',60,'#install-progress-summary');
        $I->waitForText("Installation Complete",120);
    }

    public function _failed(\AcceptanceTester $I)
    {
        $I->canSeeElement('.alert-danger');
    }
}
