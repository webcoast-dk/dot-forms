<?php

declare(strict_types=1);


namespace WEBcoast\DotForms\Database\Schema;


use TYPO3\CMS\Core\Database\Schema\DefaultTcaSchema;

class TcaSchemaOverride extends DefaultTcaSchema
{
    protected function enrichSingleTableFieldsFromTcaColumns($tables): array
    {
        $tables = parent::enrichSingleTableFieldsFromTcaColumns($tables);
        foreach ($tables as &$table) {
            foreach ($table->getColumns() as $column) {
                if ($column->getNamespaceName()) {
                    $table->dropColumn($column->getName());
                }
            }
        }

        return $tables;
    }
}
