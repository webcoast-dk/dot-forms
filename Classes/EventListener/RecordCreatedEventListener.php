<?php

declare(strict_types=1);


namespace WEBcoast\DotForms\EventListener;


use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Domain\Event\RecordCreationEvent;
use TYPO3\CMS\Core\Schema\Exception\UndefinedSchemaException;
use TYPO3\CMS\Core\Schema\TcaSchemaFactory;

#[AsEventListener('dot-forms.record-created')]
class RecordCreatedEventListener
{
    public function __construct(protected readonly TcaSchemaFactory $schemaFactory) {}

    public function __invoke(RecordCreationEvent $event)
    {
        $rawRecord = $event->getRawRecord();

        try {
            $schema = $this->schemaFactory->get($event->getRawRecord()->getMainType())->getSubSchema($event->getRawRecord()->getRecordType());

            $processedFields = [];

            foreach ($schema->getFields() as $field) {
                if (str_contains($field->getName(), '.')) {
                    $parts = explode('.', $field->getName());
                    $mainField = array_shift($parts);

                    // Get the value by path from the raw record, necessary for content-blocks to work properly
                    $value = json_decode($rawRecord->toArray()[$mainField] ?? '', true);
                    foreach ($parts as $part) {
                        if (is_array($value)) {
                            $value = $value[$part] ?? null;
                        } else {
                            $value = null;
                        }
                    }
                    $event->setProperty($field->getName(), $value);
                    if (in_array($mainField, $processedFields)) {
                        continue;
                    }

                    // Set the JSON decoded value to the main field, to make it accessible in fluid template
                    $event->setProperty($mainField, json_decode($rawRecord->toArray()[$mainField] ?? '', true));
                    $processedFields[] = $mainField;
                }
            }
        } catch (UndefinedSchemaException $e) {
            // Ignore
        }
    }
}
