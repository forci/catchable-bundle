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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Forci\Bundle\Catchable\Collector\ThrowableCollector;
use Forci\Bundle\Catchable\Command\PurgeCatchablesCommand;
use Forci\Bundle\Catchable\Monolog\Handler\LogBufferHandler;
use Forci\Bundle\Catchable\Repository\CatchableRepository;
use Forci\Bundle\Catchable\Serializer\CatchableSerializer;
use Forci\Bundle\Catchable\Subscriber\ExceptionSubscriber;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->load('Forci\\Bundle\\Catchable\\Controller\\', '../../Controller')
        ->autowire()
        ->autoconfigure()
        ->public()
        ->tag('controller.service_arguments');

    $services->set('forci.catchable.monolog.handler.log_buffer', LogBufferHandler::class);

    $services->set('forci.catchable.subscriber.exception', ExceptionSubscriber::class)
        ->args([
            service(ThrowableCollector::class),
        ])
        ->tag('kernel.event_subscriber');

    $services->set(CatchableRepository::class)
        ->autowire()
        ->autoconfigure();

    $services->set(CatchableSerializer::class)
        ->public()
        ->args([
            service('debug.file_link_formatter')->ignoreOnInvalid(),
            param('kernel.charset'),
            param('kernel.project_dir'),
        ]);

    $services->set(ThrowableCollector::class)
        ->args([
            service('forci.catchable.monolog.handler.log_buffer'),
            service(CatchableRepository::class),
        ]);

    $services->set(PurgeCatchablesCommand::class)
        ->args([
            service(CatchableRepository::class),
            param('forci_catchable.purge.keep_for_days'),
        ])
        ->tag('console.command', ['command' => 'forci:catchable:purge']);
};
