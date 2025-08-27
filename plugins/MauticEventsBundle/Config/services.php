<?php

declare(strict_types=1);

use Mautic\CoreBundle\DependencyInjection\MauticCoreExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $excludes = [];

    $services->load('MauticPlugin\\MauticEventsBundle\\', '../')
        ->exclude('../{'.implode(',', array_merge(MauticCoreExtension::DEFAULT_EXCLUDES, $excludes)).'}');

    $services->load('MauticPlugin\\MauticEventsBundle\\Entity\\', '../Entity/*Repository.php')
        ->tag(Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\ServiceRepositoryCompilerPass::REPOSITORY_SERVICE_TAG);

    $services->alias('mautic.event.model.event', MauticPlugin\MauticEventsBundle\Model\EventModel::class);

    // Register custom segment filter query builder
    $services->set('mautic.events.segment.query.builder.event_field', MauticPlugin\MauticEventsBundle\Segment\Query\Filter\EventFieldFilterQueryBuilder::class)
        ->public();
};
