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

    $services->load('MauticPlugin\\MauticOpportunitiesBundle\\', '../')
        ->exclude('../{'.implode(',', array_merge(MauticCoreExtension::DEFAULT_EXCLUDES, $excludes)).'}');

    $services->load('MauticPlugin\\MauticOpportunitiesBundle\\Entity\\', '../Entity/*Repository.php')
        ->tag(Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\ServiceRepositoryCompilerPass::REPOSITORY_SERVICE_TAG);

    $services->alias('mautic.opportunity.model.opportunity', MauticPlugin\MauticOpportunitiesBundle\Model\OpportunityModel::class);

    // Register custom segment filter query builder
    $services->set('mautic.opportunities.segment.query.builder.opportunity_field', MauticPlugin\MauticOpportunitiesBundle\Segment\Query\Filter\OpportunityFieldFilterQueryBuilder::class)
        ->public();
};
