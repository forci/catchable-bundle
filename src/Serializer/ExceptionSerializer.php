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
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\Debug\Exception\FlattenException;

class ExceptionSerializer {

    public function createEntity(\Throwable $e): Catchable {
        $e = $this->convertIfThrowable($e);

        $throwable = $this->createEntityForFlatten(FlattenException::create($e));
        $this->setStackTraceAsString($throwable, $e);

        return $throwable;
    }

    protected function convertIfThrowable(\Throwable $e): \Exception {
        if ($e instanceof \Exception) {
            return $e;
        } elseif ($e instanceof \Throwable) {
            return new FatalThrowableError($e);
        }

        return $e;
    }

    protected function setStackTraceAsString(Catchable $catchable, \Exception $e) {
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
            $catchable->setPrevious($this->createEntityForFlatten($previous));
        }

        $catchable->setStatusCode($flatten->getStatusCode());
        $catchable->setHeaders($flatten->getHeaders());

        return $catchable;
    }

}
