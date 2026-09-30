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

namespace Forci\Bundle\Catchable\Filter;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CatchableFilter
{
    private ?\ReflectionClass $reflection = null;

    protected int $page = 1;

    protected int $limit = 20;

    protected ?string $message = null;

    protected ?string $file = null;

    protected ?string $class = null;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): void
    {
        $this->file = $file;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class): void
    {
        $this->class = $class;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function loadFromRequest(Request $request, FormInterface $form): void
    {
        $bag = 'POST' === $form->getConfig()->getMethod() ? $request->request : $request->query;
        $params = $bag->all();

        $fields = $this->getProtectedVars();
        foreach ($fields as $field) {
            $val = array_key_exists($field, $params) ? $params[$field] : null;
            if ($val) {
                $this->$field = $val;
            }
        }

        $form->handleRequest($request);
    }

    protected function getProtectedVars(): array
    {
        $reflection = $this->getReflection();
        $vars = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);
        $ret = [];
        foreach ($vars as $var) {
            $ret[] = $var->name;
        }

        return $ret;
    }

    protected function getReflection(): \ReflectionClass
    {
        if (null === $this->reflection) {
            $this->reflection = new \ReflectionClass($this);
        }

        return $this->reflection;
    }
}
