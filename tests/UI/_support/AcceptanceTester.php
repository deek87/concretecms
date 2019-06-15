<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    static public $sessionCookie;
    static public $cookies;
    static public $currentUrl;
   /**
    * Define custom actions here
    */

    public function login($username = 'admin', $password = 'RandomPassword1') {
        // if snapshot exists - skipping login
        if ($this->loadSessionSnapshot('login')) {
            if (empty($this->grabCookie('CONCRETE5'))) {

                if (empty(self::$sessionCookie)) {
                 throw new RuntimeException('I DONT KNOW WHY!');
                }
                foreach (self::$cookies as $cookie) {
                    $this->setCookieObject($cookie);
                }

                $this->amOnPage(self::$currentUrl);
                $this->saveSessionSnapshot('login');
            }

            return true;
        }

        // logging in
        $this->amOnPage('/login');
        $this->fillField('uName', $username);
        $this->fillField('uPassword', $password);
        $this->click('Log in');
        $this->waitForText('Welcome', 60);
        self::$sessionCookie = $this->grabCookie('CONCRETE5');
        self::$cookies = $this->grabCookieObject();
        // saving snapshot
        $this->amOnPage('/');
        $this->saveSessionSnapshot('login');


        return true;


    }

    public function setCurrentUrl($url)
    {
        self::$currentUrl = $url;
    }

    public function logout() {
        if (empty($this->grabCookie('CONCRETE5'))) return true;

        $this->clickWithLeftButton('//li[@data-guide-toolbar-action="dashboard"]');
        $this->waitForElement('//div[@class="ccm-panel-dashboard-footer"]',20);
        $this->scrollTo('//div[@class="ccm-panel-dashboard-footer"]');
        $this->click('Sign Out.');
        $this->wait(1);
        $this->clearCookies();
        $this->amOnPage('/');

    }


}
