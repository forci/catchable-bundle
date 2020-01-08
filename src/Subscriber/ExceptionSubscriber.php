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

namespace Forci\Bundle\Catchable\Subscriber;

use Forci\Bundle\Catchable\Collector\ThrowableCollector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface {

    /** @va.r ThrowableCollector */
    protected $collector;

    public function __construct(ThrowableCollector $collector) {
        $this->collector = $collector;
    }

    public static function getSubscribedEvents() {
        return [
            KernelEvents::EXCEPTION => 'exception'
        ];
    }

    public function exception(ExceptionEvent $event) {
        $this->collector->collect($event->getThrowable());
    }
}
