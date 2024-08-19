<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Dot Forms',
    'description' => 'Create virtual fields just like flex forms, but with pure TCA.',
    'version' => '1.0.2',
    'category' => 'be',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
        ],
    ],
    'state' => 'stable',
    'author' => 'Thorben Nissen',
    'author_email' => 'thorben@webcoast.dk',
    'author_company' => 'WEBcoast',
    'autoload' => [
        'psr-4' => [
            'Webcoast\\DotForms\\' => 'Classes',
        ],
    ],
];
