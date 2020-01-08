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

namespace Forci\Bundle\Catchable\Serializer;

use Forci\Bundle\Catchable\Entity\Catchable;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class ExceptionSerializer {

    public function createEntity(\Throwable $e): Catchable {
        $throwable = $this->createEntityForFlatten(FlattenException::createFromThrowable($e));
        $this->setStackTraceAsString($throwable, $e);

        return $throwable;
    }

    protected function setStackTraceAsString(Catchable $catchable, \Throwable $e) {
        do {
            $catchable->setStackTraceString($e->getTraceAsString());
            $catchable = $catchable->getPrevious();
            $e = $e->getPrevious();
        } while ($catchable && $e);
    }

    protected function createEntityForFlatten(FlattenException $flatten) {
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
