<?php


class EnterDetailsCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function redirectToInstall(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->wait(1);

        $I->canSeeInCurrentUrl('/index.php/install');
        $I->seeElement('.btn-primary');
        //$I->click('.btn-primary');
        $I->clickWithLeftButton('.btn-primary');
        $I->see("Required Items");
        $I->seeNumberOfElements('i.fa-check',[16,18]);
    }
}
