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

namespace Forci\Bundle\Catchable\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Forci\Bundle\Catchable\Repository\CatchableRepository")
 * @ORM\Table(name="forci_catchable__catchables", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"}, indexes={
 *     @ORM\Index(name="class", columns={"class"})
 * })
 */
class Catchable {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="message", type="text")
     */
    protected $message;

    /**
     * @ORM\Column(name="code", type="string")
     */
    protected $code;

    /**
     * @var Catchable
     * @ORM\OneToOne(targetEntity="Forci\Bundle\Catchable\Entity\Catchable", fetch="EAGER")
     * @ORM\JoinColumn(name="previous_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $previous;

    /**
     * @var Catchable
     * @ORM\OneToOne(targetEntity="Forci\Bundle\Catchable\Entity\Catchable", fetch="EAGER")
     * @ORM\JoinColumn(name="next_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $next;

    /**
     * @ORM\Column(name="trace", type="array")
     */
    protected $trace;

    /**
     * @ORM\Column(name="class", type="string")
     */
    protected $class;

    /**
     * @ORM\Column(name="status_code", type="string")
     */
    protected $statusCode;

    /**
     * @ORM\Column(name="headers", type="array")
     */
    protected $headers;

    /**
     * @ORM\Column(name="file", type="text")
     */
    protected $file;

    /**
     * @ORM\Column(name="line", type="integer", options={"unsigned"=true})
     */
    protected $line;

    /**
     * @ORM\Column(name="stack_trace_string", type="text")
     */
    protected $stackTraceString;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="logs", type="array", nullable=true)
     */
    protected $logs;

    public function getPrevious(): ?Catchable {
        return $this->previous;
    }

    public function setPrevious(?Catchable $previous) {
        $this->previous = $previous;

        return $this;
    }

    public function getNext(): ?Catchable {
        return $this->next;
    }

    public function setNext(?Catchable $next) {
        $this->next = $next;

        return $this;
    }

    public function __construct() {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return Catchable
     */
    public function setMessage($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Catchable
     */
    public function setCode($code) {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Set class.
     *
     * @param string $class
     *
     * @return Catchable
     */
    public function setClass($class) {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class.
     *
     * @return string
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * Set file.
     *
     * @param string $file
     *
     * @return Catchable
     */
    public function setFile($file) {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file.
     *
     * @return string
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Set line.
     *
     * @param int $line
     *
     * @return Catchable
     */
    public function setLine($line) {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line.
     *
     * @return int
     */
    public function getLine() {
        return $this->line;
    }

    /**
     * Set stackTraceString.
     *
     * @param string $stackTraceString
     *
     * @return Catchable
     */
    public function setStackTraceString($stackTraceString) {
        $this->stackTraceString = $stackTraceString;

        return $this;
    }

    /**
     * Get stackTraceString.
     *
     * @return string
     */
    public function getStackTraceString() {
        return $this->stackTraceString;
    }

    /**
     * Set trace.
     *
     * @param array $trace
     *
     * @return Catchable
     */
    public function setTrace($trace) {
        $this->trace = $trace;

        return $this;
    }

    /**
     * Get trace.
     *
     * @return array
     */
    public function getTrace() {
        return $this->trace;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Catchable
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set logs.
     *
     * @param array|null $logs
     *
     * @return Catchable
     */
    public function setLogs($logs = null) {
        $this->logs = $logs;

        return $this;
    }

    /**
     * Get logs.
     *
     * @return array|null
     */
    public function getLogs() {
        return $this->logs;
    }

    /**
     * @return mixed
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     */
    public function setStatusCode($statusCode): void {
        $this->statusCode = $statusCode;
    }

    /**
     * @return mixed
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @param mixed $headers
     */
    public function setHeaders($headers): void {
        $this->headers = $headers;
    }
}
