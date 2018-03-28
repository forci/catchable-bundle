<?php

namespace Forci\Bundle\Catchable\Collector;

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

    public function collect(\Throwable $exception) {
        $catchable = $this->serializer->createEntity($exception);
        $catchable->setLogs($this->bufferHandler->getLogs());
        $this->catchableRepository->save($catchable);
    }

}