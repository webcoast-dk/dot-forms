<?php

declare(strict_types=1);


namespace WEBcoast\DotForms\Form\FormDataProvider;


use TYPO3\CMS\Backend\Form\FormDataProviderInterface;

class DotFormsDataProvider implements FormDataProviderInterface
{

    public function addData(array $result): array
    {
        foreach ($GLOBALS['TCA'][$result['tableName']]['columns'] as $fieldName => $fieldConfig) {
            if (str_contains($fieldName, '.')) {
                $mainFieldName = substr($fieldName, 0, strpos($fieldName, '.'));
                if (isset($result['databaseRow'][$mainFieldName])) {
                    // Get value by dot notation
                    $fieldParts = explode('.', substr($fieldName, strpos($fieldName, '.') + 1));
                    $currentArray = json_decode($result['databaseRow'][$mainFieldName], true);
                    foreach ($fieldParts as $fieldPart) {
                        if (!is_array($currentArray)) {
                            $currentArray = [];
                        }
                        $currentArray = $currentArray[$fieldPart] ?? null;
                    }
                    $result['databaseRow'][$fieldName] = $currentArray ?? $result['databaseRow'][$fieldName] ?? null;
                }
            }
        }

        return $result;
    }
}
