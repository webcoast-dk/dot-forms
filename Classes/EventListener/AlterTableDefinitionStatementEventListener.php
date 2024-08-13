<?php

declare(strict_types=1);


namespace WEBcoast\DotForms\EventListener;


use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;

class AlterTableDefinitionStatementEventListener
{
    public function __invoke(AlterTableDefinitionStatementsEvent $event): void
    {
        foreach ($GLOBALS['TCA'] as $tableName => $tableConfiguration) {
            foreach ($tableConfiguration['columns'] as $columnName => $columnConfiguration) {
                if (str_contains($columnName, '.')) {
                    $columnName = substr($columnName, 0, strpos($columnName, '.'));
                    $event->addSqlData('CREATE TABLE ' . $tableName . ' (' . $columnName . ' TEXT);');
                }
            }
        }
    }
}
