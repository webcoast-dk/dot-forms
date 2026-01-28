<?php

declare(strict_types=1);

namespace WEBcoast\DotForms\Form\FormDataProvider;

use JsonSchema\Exception\InvalidSchemaException;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Schema\Exception\UndefinedSchemaException;
use TYPO3\CMS\Core\Schema\TcaSchemaFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DotFormsDataProvider implements FormDataProviderInterface
{
    public function __construct(protected readonly TcaSchemaFactory $schemaFactory) {}

    public function addData(array $result): array
    {
        $schema = $this->schemaFactory->get($result['tableName']);
        if ($typeField = $schema->getSubSchemaDivisorField()) {
            $typeValue = (string) $result['databaseRow'][$typeField->getName()];
            if ($typeValue === '' && isset($typeField->getConfiguration()['items'][0]['value'])) {
                $typeValue = (string) $typeField->getConfiguration()['items'][0]['value'];
            }
            if ($typeValue === '') {
                [$_, $typeValue] = GeneralUtility::trimExplode('.', $schema->getSubSchemata()[0]->getName());
            }
            try {
                $schema = $schema->getSubSchema($typeValue);
            } catch (InvalidSchemaException|UndefinedSchemaException) {
                // Ignore invalid type value
            }
        }
        // Check
        foreach ($schema->getFields(fn ($field) => str_contains($field->getName(), '.')) as $field) {
            $fieldName = $field->getName();
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
            } else {
                $result['databaseRow'][$fieldName] = null;
            }

            if (is_array($result['defaultLanguageRow'])) {
                if (isset($result['defaultLanguageRow'][$mainFieldName])) {
                    // Get value by dot notation
                    $fieldParts = explode('.', substr($fieldName, strpos($fieldName, '.') + 1));
                    $currentArray = json_decode($result['defaultLanguageRow'][$mainFieldName], true);
                    foreach ($fieldParts as $fieldPart) {
                        if (!is_array($currentArray)) {
                            $currentArray = [];
                        }
                        $currentArray = $currentArray[$fieldPart] ?? null;
                    }
                    $result['defaultLanguageRow'][$fieldName] = $currentArray ?? $result['defaultLanguageRow'][$fieldName] ?? null;
                } else {
                    $result['defaultLanguageRow'][$fieldName] = null;
                }
            }
        }

        return $result;
    }
}
