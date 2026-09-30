<?php

/*
 * This file is part of the ForciCatchableBundle package.
 *
 * Copyright (c) Forci Web Consulting Ltd.
 *
 * Author Tatyana Mincheva <tatjana@forci.com>
 * Author Martin Kirilov <martin@forci.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\Catchable\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('forci_catchable');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('purge')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('keep_for_days')
                            ->defaultNull()
                            ->min(1)
                            ->info('Default retention window (in days) used by forci:catchable:purge when --days is omitted. Null means no default, so --days is required.')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
