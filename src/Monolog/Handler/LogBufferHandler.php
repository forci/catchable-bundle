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

namespace Forci\Bundle\Catchable\Monolog\Handler;

use Monolog\Formatter\ScalarFormatter;
use Monolog\Handler\AbstractHandler;
use Monolog\Logger;

class LogBufferHandler extends AbstractHandler {

    /** @var [] */
    protected $buffer = [];

    public function __construct($level = Logger::DEBUG, $bubble = true) {
        parent::__construct($level, $bubble);
        $this->setFormatter(new ScalarFormatter());
    }

    public function handle(array $record) {
        $this->buffer[] = $this->getFormatter()->format($record);

        return false === $this->bubble;
    }

    public function close() {
        $this->clear();
    }

    public function clear() {
        $this->buffer = [];
    }

    public function getLogs(): array {
        return $this->buffer;
    }
}
