<?php

namespace Forci\Bundle\Catchable\Serializer;

use Forci\Bundle\Catchable\Entity\Catchable;

class ExceptionSerializer {

    public function createEntity(\Throwable $e): Catchable {
        $catchable = new Catchable();
        $catchable->setMessage($e->getMessage());
        $catchable->setCode($e->getCode());
        $catchable->setClass(get_class($e));
        $catchable->setFile($e->getFile());
        $catchable->setLine($e->getLine());
        $catchable->setStackTraceString($e->getTraceAsString());
        $catchable->setTrace($this->sanitizeTrace($e));

        if ($previousEx = $e->getPrevious()) {
            $previous = $this->createEntity($previousEx);
            $catchable->setPrevious($previous);
        }

        return $catchable;
    }

    protected function sanitizeTrace(\Throwable $e) {
        $trace = [];
        foreach ($e->getTrace() as $call) {
            $traceRow = $call;
            $traceRow['args'] = [];

            if (isset($call['args'])) {
                foreach ($call['args'] as &$value) {
                    if ($value instanceof \Closure) {
                        $closureReflection = new \ReflectionFunction($value);
                        $value = sprintf(
                            '(Closure at %s:%s)',
                            $closureReflection->getFileName(),
                            $closureReflection->getStartLine()
                        );
                    } elseif (is_array($value)) {
                        $value = sprintf('array %s', count($value));
                    } elseif (is_object($value)) {
                        $value = sprintf('object(%s)', get_class($value));
                    } elseif (is_resource($value)) {
                        $value = sprintf('resource(%s)', get_resource_type($value));
                    }

                    // encoding for DB safe insert
                    $traceRow['args'][] = iconv('UTF-8', 'UTF-8//IGNORE', $value);
                }
            }

            $trace[] = $traceRow;
        }

        return $trace;
    }

}