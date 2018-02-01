<?php


namespace Concrete\Core\Foundation\Bus\Command;


use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Page\Template as TemplateType;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\User\User;
use Concrete\Core\Page\Type\Composer\Control\Control;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Validation\SanitizeService;


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
    public function setPageType(Type $pageType)
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
     * Function used to generate needed information such as a pageType/Template/parent page
     *
     * @param Type|null $pageType
     * @param Template|null $pageTemplate
     */
    protected function buildPageData (Type $pageType = null ,  Template $pageTemplate = null)
    {
        if ($this->options['user'] instanceof UserInfo) {
            $this->data['uID'] = $this->options['user']->getUserID();
        } else {
            $this->data['uID'] = $this->app->make(User::class)->getUserID();
        }

        if (!is_object($this->parent)) {
            $this->parent= $this->app->make('site')->getSite()->getSiteHomePageObject();
        }

        if (!is_object($this->parent) || $this->parent->isError()) {
            $this->parent = Page::getByID(HOME_CID);
        }

        if (is_object($pageType)) {
            $this->pageType = $pageType;
        } else {
            $this->pageType = $this->parent->getPageTypeObject();
            if (!is_object($this->pageType)) {
                $this->pageType = Type::getByHandle('page');
            }

        }
        if (is_object($pageTemplate)) {
            $this->pageTemplate = $pageTemplate;
        } else {
            if (!is_object($this->pageType)) {
                $this->pageTemplate = $this->parent->getPageTemplateObject();

            } else {
                $this->pageTemplate = $this->pageType->getPageTypeDefaultPageTemplateObject();
            }

        }

    }

    /**
     * @param Page $parent
     */
    public function setParent(Page $parent) {
        $this->parent = $parent;
    }

    /**
     *  Function used to set various options such as pageName, user, parentPage, pageDescription, etc
     *
     * @param array $options
     */
    public function setOptions($options=[])
    {
        if ($options['user'] instanceof UserInfo) {
            $this->options['user'] = $options['user'];
        }
        unset($options['user']);
        if ($options['parent'] instanceof Page) {
            $this->parent = $options['parent'];
        }
        unset($options['parent']);
        if ($options['parentPage'] instanceof Page) {
            $this->parent = $options['parentPage'];
        }
        unset($options['parentPage']);
        if (is_array($options['data'])) {
            $this->setData($options['data']);
        }
        unset($options['data']);
        if ($options['pageType'] instanceof Type) {
            $this->options['pageType'] = $options['pageType'];
        } else {
            $this->validatePageType($options['pageType']);
        }
        unset($options['pageType']);
        if ($options['pageTemplate'] instanceof Template) {
            $this->options['pageTemplate'] = $options['pageTemplate'];
        } else {
            $this->validateTemplate($options['pageTemplate']);
        }
        unset($options['pageTemplate']);
        $this->options = array_merge($this->options, $options);

    }

    protected function validatePageType($pageType = null) {
        /** @var SanitizeService $sanitizer */
        $sanitizer = $this->app->make(SanitizeService::class);
        $pageTypeID = $sanitizer->SanitizeInt($pageType);
        $pageTypeHandle = $sanitizer->SanitizeString($pageType);

        //Try ID first
        $pageType = Type::getByID($pageTypeID);
        if (!is_object($pageType)) {
            // If it fails then try the handle
            $pageType = Type::getByHandle($pageTypeHandle);
        }
        if (!is_object($pageType)) {
            // If both fail then set to null
            $pageType = null;
        }
        $this->options['pageType'] = $pageType;
    }

    protected function validateTemplate($pageTemplate = null) {
        /** @var SanitizeService $sanitizer */
        $sanitizer = $this->app->make(SanitizeService::class);

        $pageTemplateID = $sanitizer->SanitizeInt($pageTemplate);
        $pageTemplateHandle = $sanitizer->SanitizeString($pageTemplate);

        //Try ID first
        $pageTemplate = TemplateType::getByID($pageTemplateID);
        if (!is_object($pageTemplate)) {
            // If it fails then try the handle
            $pageTemplate = TemplateType::getByHandle($pageTemplateHandle);
        }
        if (!is_object($pageTemplate)) {
            // If both fail then set to null
            $pageTemplate = null;
        }

        $this->options['pageTemplate'] = $pageTemplate;
    }

    /**
     * Function used to get data from request
     * Mainly used for API calls
     */
    public function getDataFromRequest()
    {
        /** @var SanitizeService $sanitizer */
        $sanitizer = $this->app->make(SanitizeService::class);
        $this->options['pageName'] = $this->request->get('name') ?: $this->options['pageName'];
        $this->options['user'] = $this->request->getCustomRequestUser()->getUserID();
        $this->options['pageDescription'] = $this->request->get('description') ?: $this->options['pageDescription'];
        $this->options['pageHandle'] = $sanitizer->sanitizeURL($this->request->get('url_slug'))?: $this->options['pageHandle'];
        $this->options['publishDate'] = $this->request->get('publish_date');
        $this->validateTemplate($this->request->get('page_template'));
        $this->validatePageType($this->request->get('page_type'));
        $pageID = $this->request->get('parent');

        $this->parent = Page::getByID($pageID);
        if (!is_object($this->parent) || !$this->parent->isError()) {
            $this->parent = null;
        }

        $this->publishDate = $this->validateDate($this->request->get('publish_date'));
        $this->data = $this->parseData($this->request->request->get('data'));
    }

    /**
     * @param $publishDate
     * @return null|string
     */
    protected function validateDate($publishDate) {
        if (is_null($publishDate) || empty($publishDate)) {
            return null;
        }

        $date = date_create($publishDate);
        if ($date) {
            return $date->format('Y-m-d H:i:s');
        } else {
            return null;
        }
    }

    protected function parseData($data)
    {

        if (is_array($data)) {
            $this->data = array_merge($data, ['cvIsApproved' => 0, 'cIsDraft' => 1, 'cIsActive' => false, 'cAcquireComposerOutputControls' => true]);
        } else {
            $this->data = ['cvIsApproved' => 0, 'cIsDraft' => 1, 'cIsActive' => false, 'cAcquireComposerOutputControls' => true];
        }

        $this->data['cName'] = $this->options['pageName'];
        $this->data['cDescription'] = $this->options['pageDescription'];
        $this->data['cHandle'] = $this->options['pageHandle'];


        $this->buildPageData($this->options['pageType'], $this->options['pageTemplate']);

        return $this->data;
    }


    /**
     * Fuction used by the command handler to execute the command
     *
     * @return Page|null
     */
    public function execute()
    {
        if ($this->isApiRequest()) {
            $this->getDataFromRequest();
        } else {
            $this->data = $this->parseData($this->options['data']);
        }

        if (is_object($this->parent) && !$this->parent->isError()) {
        /** @var Page $pageDraft */
        $pageDraft = $this->parent->add($this->pageType, $this->data, $this->pageTemplate);
        } else {
            $pageDraft = null;
        }
        if (is_object($pageDraft) && !$pageDraft->isError()) {
            $pageDraft->setPageDraftTargetParentPageID($this->parent->getCollectionID());
            if (is_object($pageDraft->getPageTypeObject())) {
                $controlList = Control::getList($pageDraft->getPageTypeObject());
                foreach ($controlList as $control) {

                    $control->onPageDraftCreate($pageDraft);
                    $control->publishToPage($pageDraft, $this->data, $controlList);
                }



                $pageDraft->getPageTypeObject()->publish($pageDraft, $this->publishDate);
                // We need to get the most recent version as $pageDraft is currently the pageDraft version.
                $pageDraft = Page::getByID($pageDraft->getCollectionID(), 'RECENT');
                return $pageDraft;
            } else {
                return $pageDraft;
            }
        } else {
            return null;
        }



    }

}