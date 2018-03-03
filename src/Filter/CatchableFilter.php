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

class CatchableFilter {

    private $reflection = null;

    protected $page = 1;

    protected $limit = 20;

    /** @var string|null */
    protected $message;

    /** @var string|null */
    protected $file;

    /** @var string|null */
    protected $class;

    public function getMessage(): ?string {
        return $this->message;
    }

    /**
     * @param string|null $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    public function getFile(): ?string {
        return $this->file;
    }

    /**
     * @param string|null $file
     */
    public function setFile($file): void {
        $this->file = $file;
    }

    public function getClass(): ?string {
        return $this->class;
    }

    /**
     * @param string|null $class
     */
    public function setClass($class): void {
        $this->class = $class;
    }

    public function getPage(): int {
        return $this->page;
    }

    /**
     * @param int|null $page
     */
    public function setPage($page): void {
        $this->page = $page;
    }

    public function getLimit(): int {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit($limit): void {
        $this->limit = $limit;
    }

    public function loadFromRequest(Request $request, FormInterface $form) {
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

    protected function getProtectedVars() {
        $reflection = $this->getReflection();
        $vars = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);
        $ret = [];
        foreach ($vars as $var) {
            $ret[] = $var->name;
        }

        return $ret;
    }

    protected function getReflection() {
        if (null === $this->reflection) {
            try {
                $this->reflection = new \ReflectionClass($this);
            } catch (\ReflectionException $e) {
            }
        }

        return $this->reflection;
    }
}
