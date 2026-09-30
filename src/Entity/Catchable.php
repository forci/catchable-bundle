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
use Forci\Bundle\Catchable\Repository\CatchableRepository;

#[ORM\Entity(repositoryClass: CatchableRepository::class)]
#[ORM\Table(name: 'forci_catchable__catchables', options: ['collate' => 'utf8mb4_unicode_ci', 'charset' => 'utf8mb4'])]
#[ORM\Index(name: 'class', columns: ['class'])]
class Catchable
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'message', type: 'text')]
    protected string $message;

    #[ORM\Column(name: 'code', type: 'string')]
    protected string $code;

    #[ORM\OneToOne(targetEntity: Catchable::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'previous_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    protected ?Catchable $previous = null;

    #[ORM\OneToOne(targetEntity: Catchable::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'next_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    protected ?Catchable $next = null;

    #[ORM\Column(name: 'trace', type: 'array')]
    protected array $trace;

    #[ORM\Column(name: 'class', type: 'string')]
    protected string $class;

    #[ORM\Column(name: 'status_code', type: 'string')]
    protected string $statusCode;

    #[ORM\Column(name: 'headers', type: 'array')]
    protected array $headers;

    #[ORM\Column(name: 'file', type: 'text')]
    protected string $file;

    #[ORM\Column(name: 'line', type: 'integer', options: ['unsigned' => true])]
    protected int $line;

    #[ORM\Column(name: 'stack_trace_string', type: 'text')]
    protected string $stackTraceString;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(name: 'logs', type: 'array', nullable: true)]
    protected ?array $logs = null;

    public function getPrevious(): ?Catchable
    {
        return $this->previous;
    }

    public function setPrevious(?Catchable $previous): static
    {
        $this->previous = $previous;

        return $this;
    }

    public function getNext(): ?Catchable
    {
        return $this->next;
    }

    public function setNext(?Catchable $next): static
    {
        $this->next = $next;

        return $this;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): static
    {
        $this->class = $class;

        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function setLine(int $line): static
    {
        $this->line = $line;

        return $this;
    }

    public function getStackTraceString(): string
    {
        return $this->stackTraceString;
    }

    public function setStackTraceString(string $stackTraceString): static
    {
        $this->stackTraceString = $stackTraceString;

        return $this;
    }

    public function getTrace(): array
    {
        return $this->trace;
    }

    public function setTrace(array $trace): static
    {
        $this->trace = $trace;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLogs(): ?array
    {
        return $this->logs;
    }

    public function setLogs(?array $logs = null): static
    {
        $this->logs = $logs;

        return $this;
    }

    public function getStatusCode(): string
    {
        return $this->statusCode;
    }

    public function setStatusCode(string $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }
}
