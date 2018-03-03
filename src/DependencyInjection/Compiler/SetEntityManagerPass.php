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

namespace Forci\Bundle\Catchable\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SetEntityManagerPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {
        $serviceId = $container->getParameter('forci.catchable.config.entity_manager');

        if (!$container->hasDefinition($serviceId)) {
            throw new \InvalidArgumentException(sprintf('The provided Entity Manager Service ID %s is missing', $serviceId));
        }

        $container->setAlias('forci.catchable.entity_manager', $serviceId);
    }
}
