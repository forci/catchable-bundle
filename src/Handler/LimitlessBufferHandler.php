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

namespace Forci\Bundle\Catchable\Handler;

use Monolog\Handler\AbstractHandler;

class LimitlessBufferHandler extends AbstractHandler {

    /** @var [] */
    protected $buffer = [];

    /** @var bool */
    protected $initialized = false;

    public function handle(array $record) {
        if (!$this->initialized) {
            // __destructor() doesn't get called on Fatal errors
            register_shutdown_function([$this, 'close']);
            $this->initialized = true;
        }

        if ($this->processors) {
            foreach ($this->processors as $processor) {
                $record = call_user_func($processor, $record);
            }
        }

        $this->buffer[] = $record;

        return false === $this->bubble;
    }

    public function __destruct() {
        // suppress the parent behavior since we already have register_shutdown_function()
        // to call close(), and the reference contained there will prevent this from being
        // GC'd until the end of the request
    }

    public function close() {
        $this->clear();
    }

    public function clear() {
        $this->buffer = [];
    }

    public function getLogs() {
        return $this->buffer;
    }
}
