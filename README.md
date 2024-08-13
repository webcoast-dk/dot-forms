# Dot forms extension for TYPO3 CMS

This extension allows configuring TCA fields using the dot notation, e.g.
`settings.pagination.itemsPerPage`. Like with flex forms, this avoids
extra database fields for the configured TCA fields, but it allows to place
the fields in a more flexible way instead of grouping them together inside
that flex form container.

## Installation

```bash
composer require webcoast/dot-forms
```

No further configuration is necessary to use this extension.

## Usage
Configure your TCA fields as usual, but use the dot notation for the field
name, e.g.
```php
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'settings.pagination.itemsPerPage' => [
        'label' => 'Items per page',
        'config' => [
            'type' => 'number',
            'format' => 'integer'
            'size' => 5,
            'default' => 10,
        ],
    ],
    'settings.pagination.maxNumberOfLinks' => [
        'label' => 'Max number of links',
        'config' => [
            'type' => 'number',
            'format' => 'integer'
            'size' => 5,
            'default' => 7,
        ],
    ],
]);
```

**Optional:** Create a palette with your fields:
```php
$GLOBALS['TCA']['tt_content']['palettes']['settings.pagination'] = [
    'label' => 'Pagination',
    'showitem' => 'settings.pagination.itemsPerPage, settings.pagination.maxNumberOfLinks',
];
```

Add your palette or fields to the `types` section:
```php
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_content', '--palette--;;settings.pagination', '{yourCType}', 'after:header');
```

### Extbase plugins (settings)
When using Extbase plugins, you can use the `\WEBcoast\DotForms\Mvc\Controller\ActionController`
as base class for your controller, to automatically map the `settings.*` fields to the `$this->settings`
property in your controller.

The magic is done using the `initializeAction()` method, which is called before the actual action method.
If you also use this method in your controller, make sure to call `parent::initializeAction()` at the beginning.

### Data processor
When working the data processors, e.g. in a `FLUIDTEMPLATE` content object,
your can use the included data processor to map all fields with the dot
notation to the respective main field in the data array.

```typo3_typoscript
tt_content.myContentObject {
    dataProcessing {
        10 = dot-forms
    }
}
```
