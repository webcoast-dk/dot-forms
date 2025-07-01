<?php

declare(strict_types=1);


namespace WEBcoast\DotForms\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;

#[AsEventListener('webcoast.dot_forms.remove_dot_fields_from_search')]
class RemoveDotFieldsFromSearchListener
{
    public function __invoke(AfterTcaCompilationEvent $event): void
    {
        $tca = $event->getTCA();
        foreach ($tca as $tableName => $tableConfig) {
            if ($tableConfig['ctrl']['searchFields'] ?? false) {
                $searchFields = explode(',', $tableConfig['ctrl']['searchFields']);
                $searchFields = array_filter($searchFields, static function ($field) {
                    return !str_contains(trim($field), '.');
                });
                $tca[$tableName]['ctrl']['searchFields'] = implode(',', $searchFields);
            }
        }

        $event->setTCA($tca);
    }
}
