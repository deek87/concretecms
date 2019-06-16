<?php


namespace Concrete\UITests\Page;

use AcceptanceTester;


class AddCest {


    public function _before(AcceptanceTester $I)
    {
        $I->login();
    }

    public function _after(AcceptanceTester $I)
    {
        $I->setCurrentUrl($I->grabFromCurrentUrl());
    }

    public function loadNewPage(AcceptanceTester $I)
    {

        $I->seeElement('//a[@data-launch-panel="sitemap"]');
        $I->clickWithLeftButton('//a[@data-launch-panel="sitemap"]');
        $I->waitForText('New Page', '30');
        $I->click('Empty Page');
        $I->waitForElement('.ccm-toolbar-page-edit-mode-active');
        $I->waitForJS('return document.readyState == "complete"', 60);
        $I->waitForJS('return !!window . jQuery && window . jQuery . active == 0;', 60);

    }

    public function addNewBlock(AcceptanceTester $I)
    {
        $I->comment("I'm Adding A Content Block To The Page");
        $I->clickWithLeftButton('//div[contains(@class,"ccm-area-drag-area")][text()="Empty Main Area"]');
        $I->waitForElementVisible('//div[@class="popover fade bottom"]//a[@data-menu-action="area-add-block"]');
        $I->clickWithLeftButton('//div[@class="popover fade bottom"][contains(@style,"display: block;")]//a[@data-menu-action="area-add-block"]');
        $I->waitForText('Blocks',20);
        $I->waitForElement('//a[@data-block-type-handle="content"]');
        $I->clickWithLeftButton('//a[@data-block-type-handle="content"]');
        $I->waitForElement('div.cke_inner');
        $I->type('XYZ TESTING');
        $I->comment("I'm Adding Bold To The Next Entered Text");
        $I->canSeeElement('div.cke_inner');
        $I->clickWithLeftButton('//div[@class="cke_inner"]//span[@role="presentation"]//span[contains(@class,"cke_button__bold_icon")]');
        $I->wait(0.5);
        $I->clickWithLeftButton('//div[@contenteditable="true"][contains(@class,"cke_editable")]');
        $I->wait(0.5);
        $I->type(' EXTRA TEST');
        $I->clickWithLeftButton('//div[@class="cke_inner"]//span[@role="presentation"]//span[contains(@class,"cke_button__bold_icon")]');
        $I->wait(0.5);
        $I->clickWithLeftButton('//div[@class="cke_inner"]//span[@class="cke_button_label cke_button__save_label"]');
        $I->waitForText('The block has been added successfully.');


    }

    public function dragAndDropBlock(AcceptanceTester $I) {
        $I->clickWithLeftButton('.ccm-toolbar-add');
        $I->waitForText('Image');
        $I->dragAndDrop('//a[@data-panel-add-block-drag-item="block"][@title="Image"]',
            '//div[contains(@class,"ccm-area-drag-area")][text()="Empty Page Footer Area"]');
        $I->waitForElement('div.ccm-file-selector-choose-new');
        $I->clickWithLeftButton('div.ccm-file-selector-choose-new');
        $I->waitForElement('//div[contains(@class,"ccm-ui")][@data-header="file-manager"]');
        $I->waitForElement('//tr[@data-file-manager-tree-node-type="file"][.//*[contains(text(), "bridge.jpg")]]');
        $I->clickWithLeftButton('//tr[@data-file-manager-tree-node-type="file"][.//*[contains(text(), "bridge.jpg")]]');
        $I->waitForText('Add');
        $I->click('Add');
        $I->waitForText('The block has been added successfully.');

    }


    public function enterPageDetails(AcceptanceTester $I) {
        $I->clickWithLeftButton('//li[@data-guide-toolbar-action="page-settings"]');
        $I->waitForText('Composer');
        $I->fillField('input[id="ptComposer[1][name]"][name="ptComposer[1][name]"]','My Auto Page');
        $I->fillField('textarea[id="ptComposer[2][description]"]', 'This is my description of this automatic page.');
        $I->see('URL Slug');
        $I->fillField('//input[contains(@id, "url_slug")]','test-page');
        // Override for viewing manually as bottom navbar gets in way sometimes
        $I->executeJS("var objDiv = document.getElementById('ccm-panel-detail-page-composer');
objDiv.scrollTop = objDiv.scrollHeight;");
        $I->clickWithLeftButton('//div[@class="ccm-item-selector"]//a[@data-page-selector-link="choose"]');
        $I->waitForText('Full Sitemap',20);
        $I->clickWithLeftButton('//span[@class="fancytree-title"][contains(text(),"Home")]');

    }

    public function publishAndVist(AcceptanceTester $I) {

        $I->waitForText('Publish Page');
        $I->click('Publish Page');
        $I->waitForText('XYZ TESTING EXTRA TEST');
        $I->logout();
        $I->amOnPage('/test-page');
        $I->waitForText('XYZ TESTING EXTRA TEST');

    }
}
