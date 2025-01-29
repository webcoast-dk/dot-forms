<?php

declare(strict_types=1);


namespace WEBcoast\DotForms\DataProcessing;


use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class DotFormsDataProcessor implements DataProcessorInterface
{

    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        foreach ($GLOBALS['TCA'][$cObj->getCurrentTable()]['columns'] as $fieldName => $fieldConfig) {
            if (str_contains($fieldName, '.')) {
                $mainFieldName = substr($fieldName, 0, strpos($fieldName, '.'));
                if (isset($cObj->data[$mainFieldName])) {
                    if (!isset($processedData[$mainFieldName])) {
                        if ($mainFieldName === 'settings' && ($contentObjectConfiguration['settings.'] ?? [])) {
                            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
                            $settings = $typoScriptService->convertTypoScriptArrayToPlainArray($contentObjectConfiguration['settings.']);
                            $processedData[$mainFieldName] = array_merge($settings, json_decode($cObj->data[$mainFieldName], true));
                        } else {
                            $processedData[$mainFieldName] = json_decode($cObj->data[$mainFieldName], true);
                        }
                    }
                }
            }
        }
        return $processedData;
    }
}
