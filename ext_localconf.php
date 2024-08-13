<?php

declare(strict_types=1);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\DataHandling\DataHandler::class] = [
    'className' => \WEBcoast\DotForms\DataHandling\DataHandler::class,
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\WEBcoast\DotForms\Form\FormDataProvider\DotFormsDataProvider::class] = [
    'depends' => [
        \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEditRow::class
    ],
];
