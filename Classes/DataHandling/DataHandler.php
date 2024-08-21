<?php

declare(strict_types=1);


namespace WEBcoast\DotForms\DataHandling;


use TYPO3\CMS\Backend\Utility\BackendUtility;

class DataHandler extends \TYPO3\CMS\Core\DataHandling\DataHandler
{
    public function compareFieldArrayWithCurrentAndUnset($table, $id, $fieldArray)
    {
        $incomingFieldArray = $fieldArray;
        $fieldArray = array_filter($fieldArray, function ($value) {
            return !str_contains($value, '.');
        }, ARRAY_FILTER_USE_KEY);
        $fieldArray = parent::compareFieldArrayWithCurrentAndUnset($table, $id, $fieldArray);

        // Compare dot fields with original records and add changes to history
        $currentRecord = BackendUtility::getRecord($table, $id);
        foreach ($incomingFieldArray as $fieldName => $fieldValue) {
            if (str_contains($fieldName, '.')) {
                $mainFieldName = substr($fieldName, 0, strpos($fieldName, '.'));
                $fieldParts = explode('.', substr($fieldName, strpos($fieldName, '.') + 1));
                $currentValue = json_decode($currentRecord[$mainFieldName] ?? '[]', true);
                foreach ($fieldParts as $fieldPart) {
                    if (!is_array($currentValue)) {
                        $currentValue = [];
                    }
                    $currentValue = $currentValue[$fieldPart] ?? null;
                }
                if ($currentValue !== $fieldValue) {
                    $this->historyRecords[$table . ':' . $id]['oldRecord'][$fieldName] = $currentValue;
                    $this->historyRecords[$table . ':' . $id]['newRecord'][$fieldName] = $fieldValue;
                    $fieldArray[$fieldName] = $fieldValue;
                }
            }
        }

        return $fieldArray;
    }

    public function updateDB($table, $id, $fieldArray)
    {
        $fieldArray = $this->processDotFields($table, $id, $fieldArray, 'update');

        parent::updateDB($table, $id, $fieldArray);
    }

    public function insertDB($table, $id, $fieldArray, $newVersion = false, $suggestedUid = 0, $dontSetNewIdIndex = false)
    {
        $fieldArray = $this->processDotFields($table, $id, $fieldArray, 'new');

        return parent::insertDB($table, $id, $fieldArray, $newVersion, $suggestedUid, $dontSetNewIdIndex);
    }

    protected function processDotFields($table, $id, $fieldArray, $status): array
    {
        if ($status !== 'new') {
            $currentRecord = BackendUtility::getRecord($table, $id);
        } else {
            $currentRecord = [];
        }

        $dotFields = [];

        foreach ($fieldArray as $fieldName => $fieldValue) {
            if (str_contains($fieldName, '.')) {
                $mainFieldName = substr($fieldName, 0, strpos($fieldName, '.'));
                if (!isset($dotFields[$mainFieldName])) {
                    $dotFields[$mainFieldName] = json_decode($currentRecord[$mainFieldName] ?? '[]', true);
                }
                // Convert dot notation to array
                $fieldParts = explode('.', substr($fieldName, strpos($fieldName, '.') + 1));
                $currentArray = &$dotFields[$mainFieldName];
                foreach ($fieldParts as $fieldPart) {
                    if (!is_array($currentArray)) {
                        $currentArray = [];
                    }
                    $currentArray = &$currentArray[$fieldPart];
                }
                $currentArray = $fieldValue;
                unset($fieldArray[$fieldName]);
            }
        }

        foreach ($dotFields as $fieldName => $fieldValue) {
            $fieldArray[$fieldName] = json_encode($fieldValue);
        }

        return $fieldArray;
    }

}
