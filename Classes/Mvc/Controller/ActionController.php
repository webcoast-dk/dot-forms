<?php

declare(strict_types=1);


namespace WEBcoast\DotForms\Mvc\Controller;


use TYPO3\CMS\Extbase\Mvc\Controller\ActionController as ExtbaseActionController;

class ActionController extends ExtbaseActionController
{
    public function initializeAction()
    {
        $contentObject = $this->request->getAttribute('currentContentObject');
        if (isset($contentObject->data['settings'])) {
            $this->settings = array_replace_recursive($this->settings, json_decode($contentObject->data['settings'], true));
        }
    }
}
