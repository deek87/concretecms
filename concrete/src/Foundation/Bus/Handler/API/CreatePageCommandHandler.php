<?php


namespace Concrete\Core\Foundation\Bus\Handler;

use Concrete\Core\Foundation\Bus\Command\CreatePageCommand;
use Concrete\Core\API\Transformer\BasicTransformer;
use League\Fractal\Resource\Item;

class CreatePageCommandHandler
{
    public function handle( CreatePageCommand $createPageCommand) {
        return $createPageCommand->execute();
    }

    public function handleApiRequest(CreatePageCommand $createPageCommand) {

        $pageDraft = $createPageCommand->execute();
        if (is_object($pageDraft)) {
            if ($pageDraft->isPageDraft()  && is_object($pageDraft->getPageTypeObject())) {
                $messageArray = ['message'=>t('Page Submited to Workflow'), 'page'=>$pageDraft->getJSONObject()];
            } elseif (is_object($pageDraft->getPageTypeObject())) {
                $messageArray = ['message'=>t('Page Added Successfully.'), 'page'=>$pageDraft->getJSONObject()];
            } else {
                $messageArray = ['message'=>t('Page Draft Created'), 'page'=>$pageDraft->getJSONObject()];
            }
        } else {
            $messageArray = ['message'=>t('An error occurred while creating this page')];
        }

        return new Item($messageArray, new BasicTransformer());

    }

}