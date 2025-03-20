<?php

declare(strict_types=1);


namespace WEBcoast\DotForms;


use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Schema\Parser\Parser;
use TYPO3\CMS\Core\Database\Schema\SchemaMigrator;
use WEBcoast\DotForms\Database\Schema\TcaSchemaOverride;

class ServiceProvider extends \TYPO3\CMS\Core\ServiceProvider
{

    public function getFactories(): array
    {
        return [
            SchemaMigrator::class => self::getSchemaMigrator(...)
        ];
    }

    public function getExtensions(): array
    {
        return [];
    }

    public static function getSchemaMigrator(ContainerInterface $container): SchemaMigrator
    {
        return self::new($container, SchemaMigrator::class, [
            $container->get(ConnectionPool::class),
            $container->get(Parser::class),
            new TcaSchemaOverride(),
        ]);
    }
}
