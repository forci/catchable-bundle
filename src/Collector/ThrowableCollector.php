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
use Forci\Bundle\Catchable\Handler\LimitlessBufferHandler;
use Forci\Bundle\Catchable\Repository\CatchableRepository;
use Forci\Bundle\Catchable\Serializer\ExceptionSerializer;

class ThrowableCollector {

    /** @var ExceptionSerializer */
    protected $serializer;

    /** @var LimitlessBufferHandler */
    protected $bufferHandler;

    /** @var CatchableRepository */
    protected $catchableRepository;

    public function __construct(ExceptionSerializer $serializer, LimitlessBufferHandler $bufferHandler, CatchableRepository $catchableRepository) {
        $this->serializer = $serializer;
        $this->bufferHandler = $bufferHandler;
        $this->catchableRepository = $catchableRepository;
    }

    public function collect(\Exception $exception): Catchable {
        $catchable = $this->serializer->createEntity($exception);
        $catchable->setLogs($this->bufferHandler->getLogs());
        $this->catchableRepository->save($catchable);

        return $catchable;
    }
}
