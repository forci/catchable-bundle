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

use Monolog\Formatter\ScalarFormatter;
use Monolog\Handler\AbstractHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class LimitlessBufferHandler extends AbstractHandler
{
    /** @var [] */
    protected $buffer = [];

    private ScalarFormatter $formatter;

    public function __construct($level = Level::Debug, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->formatter = new ScalarFormatter();
    }

    public function handle(LogRecord $record): bool
    {

        if (!$this->isHandling($record)) {
            return false;
        }

        $this->buffer[] = $this->formatter->format($record);

        return false === $this->bubble;
    }

    public function close(): void
    {
        $this->clear();
    }

    public function clear(): void
    {
        $this->buffer = [];
    }

    public function getLogs()
    {
        return $this->buffer;
    }
}
