<?php


namespace Concrete\Core\Foundation\Bus\Command\Page;

use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Foundation\Bus\Command\AbstractCommand;


/**
 * Class used to create new pages via the command bus
 *
 * Class CreatePageCommand
 * @package Concrete\Core\Foundation\Bus\Command
 */
class CreatePageCommand extends AbstractCommand
{

    /** @var \Concrete\Core\Entity\Page\Template $pageTemplate */
    protected $pageTemplate = null;
    /** @var \Concrete\Core\Page\Type\Type|null  $pageType */
    protected $pageType = null;
    /** @var Page|null $parent */
    protected $parent = null;
    /** @var string|null */
    protected $publishDate = null;
    /** @var Page|null $parent */
    protected $returnObject = null;

    /**
     * @return Type|null
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * @param Type|null $pageType
     */
    public function setPageType($pageType)
    {
        $this->pageType = $pageType;
    }

    /**
     * Function for setting the owner of a page to be created
     *
     * @param UserInfo $userInfo
     */
    public function setUser(UserInfo $userInfo) {
        $this->options['user'] = $userInfo;
    }

    /**

    /**
     * @param Page $parent
     */
    public function setParent($parent) {
        $this->parent = $parent;
    }

    /**
     * @return Page|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setPublishDate(\DateTime $dateTime)
    {
        $this->publishDate = $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * @return null|string
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * @param string $pageName
     */
    public function setPageName($pageName = '')
    {
        $this->options['pageName'] = $pageName;
    }

    /**
     * @param string $pageDescription
     */
    public function setPageDescription($pageDescription = '')
    {
        $this->options['pageDescription'] = $pageDescription;
    }

    /**
     * @param Template $pageTemplate
     */
    public function setPageTemplate($pageTemplate)
    {
        $this->pageTemplate = $pageTemplate;
    }

    /**
     * @return Template
     */
    public function getPageTemplate()
    {
        return $this->pageTemplate;
    }

    public function setUserID($userID)
    {
        $this->options['uID'] = $userID;
    }

    public function getUserID()
    {
        return $this->options['uID'];
    }

    public function getPageName()
    {
        return $this->options['pageName'];
    }

    public function getPageDescription()
    {
        return $this->options['pageDescription'];
    }

}