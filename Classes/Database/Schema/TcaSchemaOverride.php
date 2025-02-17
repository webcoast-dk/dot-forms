<?php

declare(strict_types=1);


namespace WEBcoast\DotForms\Database\Schema;


use Doctrine\DBAL\Schema\Table;
use TYPO3\CMS\Core\Database\Schema\DefaultTcaSchema;

class TcaSchemaOverride extends DefaultTcaSchema
{
    /**
     * @param array|Table[] $tables
     * @return array|Table[]
     */
    protected function enrichSingleTableFields($tables): array
    {
        /** @var Table[] $tables */
        $tables = parent::enrichSingleTableFields($tables);
        foreach ($tables as $table) {
            foreach ($table->getColumns() as $column) {
                if (str_contains($column->getName(), '.')) {
                    $table->dropColumn($column->getName());
                }
            }
        }

        return $tables;
    }
}
