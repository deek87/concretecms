<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I


use Facebook\WebDriver\Cookie;

class Acceptance extends \Codeception\Module
{

    public function type($keys = "")
    {
        $this->getModule('WebDriver')->webDriver->getKeyboard()->sendKeys($keys);

    }

    /**
     * @param null $name
     * @return Cookie[] | null
     * @throws \Codeception\Exception\ModuleException
     */
    public function grabCookieObject($name = null)
    {
        if (empty($name)) {
         return $this->getModule('WebDriver')->webDriver->manage()->getCookies();
        } else {
            $params['name'] = $name;
            $cookies = $this->getModule('WebDriver')->filterCookies($this->getModule('WebDriver')->webDriver->manage()->getCookies(), $params);
            if (empty($cookies)) {
                return null;
            }
            return $cookies;
        }


    }

    public function setCookieObject(Cookie $cookie)
    {
        if (empty($cookie->getExpiry())) {
            $cookie->setExpiry((time() + 160000));
        }
        $this->getModule('WebDriver')->webDriver->manage()->addCookie($cookie);
    }

    public function clearCookies() {
        $this->getModule('WebDriver')->webDriver->manage()->deleteAllCookies();
    }

}
