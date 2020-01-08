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

namespace Forci\Bundle\Catchable\Collector;

use Forci\Bundle\Catchable\Entity\Catchable;
use Forci\Bundle\Catchable\Monolog\Handler\LogBufferHandler;
use Forci\Bundle\Catchable\Repository\CatchableRepository;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class ThrowableCollector {

    /** @var LogBufferHandler */
    protected $bufferHandler;

    /** @var CatchableRepository */
    protected $catchableRepository;

    public function __construct(
        LogBufferHandler $bufferHandler, CatchableRepository $catchableRepository
    ) {
        $this->bufferHandler = $bufferHandler;
        $this->catchableRepository = $catchableRepository;
    }

    public function collect(\Throwable $exception): Catchable {
        $catchable = $this->createEntity($exception);
        $catchable->setLogs($this->bufferHandler->getLogs());
        $this->catchableRepository->save($catchable);

        return $catchable;
    }

    protected function createEntity(\Throwable $e): Catchable {
        $catchable = $this->createEntityForFlatten(FlattenException::createFromThrowable($e));
        $this->setStackTraceAsString($catchable, $e);

        return $catchable;
    }

    private function setStackTraceAsString(Catchable $catchable, \Throwable $e): void {
        do {
            $catchable->setStackTraceString($e->getTraceAsString());
            $catchable = $catchable->getPrevious();
            $e = $e->getPrevious();
        } while ($catchable && $e);
    }

    private function createEntityForFlatten(FlattenException $flatten): Catchable {
        $catchable = new Catchable();
        $catchable->setMessage($flatten->getMessage());
        $catchable->setCode($flatten->getCode());
        $catchable->setTrace($flatten->getTrace());
        $catchable->setClass($flatten->getClass());
        $catchable->setFile($flatten->getFile());
        $catchable->setLine($flatten->getLine());

        if ($previous = $flatten->getPrevious()) {
            $previous = $this->createEntityForFlatten($previous);
            $catchable->setPrevious($previous);
            $previous->setNext($catchable);
        }

        $catchable->setStatusCode($flatten->getStatusCode());
        $catchable->setHeaders($flatten->getHeaders());

        return $catchable;
    }
}
