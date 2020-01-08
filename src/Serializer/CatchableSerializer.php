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

namespace Forci\Bundle\Catchable\Serializer;

use Forci\Bundle\Catchable\Entity\Catchable;
use Symfony\Component\HttpFoundation\Response;

class CatchableSerializer {

    protected $fileLinkFormat;

    /** @var string */
    protected $charset;

    /** @var string */
    protected $projectDir;

    public function __construct($fileLinkFormat, string $charset, string $projectDir) {
        $this->fileLinkFormat = $fileLinkFormat ?: ini_get('xdebug.file_link_format') ?: get_cfg_var('xdebug.file_link_format');
        $this->charset = $charset;
        $this->projectDir = rtrim($projectDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    public function serialize(Catchable $catchable): array {
        $trace = $catchable->getTrace();

        foreach ($trace as $key => $t) {
            $t['args_formatted'] = $this->formatArgs($t['args']);

            $t['file_formatted'] = $this->formatFile($t['file'], $t['line']);

            $trace[$key] = $t;
        }

        $statusCode = $catchable->getStatusCode();
        $statusCodeText = 'unknown status';

        if ($statusCode && isset(Response::$statusTexts[$statusCode])) {
            $statusCodeText = Response::$statusTexts[$statusCode];
        }

        $dataArray = [
            'id' => $catchable->getId(),
            'message' => $catchable->getMessage(),
            'code' => $catchable->getCode(),
            'class' => $catchable->getClass(),
            'file' => $catchable->getFile(),
            'line' => $catchable->getLine(),
            'stackTraceString' => $catchable->getStackTraceString(),
            'trace' => $trace,
            'createdAt' => $catchable->getCreatedAt()->getTimestamp(),
            'headers' => $catchable->getHeaders(),
            'statusCode' => $statusCode,
            'statusCodeText' => $statusCodeText
        ];

        if ($catchable->getPrevious()) {
            $dataArray['previous'] = $this->serialize($catchable->getPrevious());
        }

        if ($catchable->getLogs()) {
            foreach ($catchable->getLogs() as $key => $log) {
                if (isset($log['datetime'])) {
                    $log['timestamp'] = (new \DateTime($log['datetime']))->getTimestamp();
                }
                $dataArray['logs'][] = $log;
            }
        }

        return $dataArray;
    }

    protected function formatArgs($args) {
        $result = [];
        foreach ($args as $key => $item) {
            if ('object' === $item[0]) {
                $parts = explode('\\', $item[1]);
                $short = array_pop($parts);
                $formattedValue = sprintf('<em>object</em>(<abbr title="%s">%s</abbr>)', $item[1], $short);
            } elseif ('array' === $item[0]) {
                $formattedValue = sprintf('<em>array</em>(%s)', is_array($item[1]) ? $this->formatArgs($item[1]) : $item[1]);
            } elseif ('null' === $item[0]) {
                $formattedValue = '<em>null</em>';
            } elseif ('boolean' === $item[0]) {
                $formattedValue = '<em>'.strtolower(var_export($item[1], true)).'</em>';
            } elseif ('resource' === $item[0]) {
                $formattedValue = '<em>resource</em>';
            } else {
                $formattedValue = str_replace("\n", '', htmlspecialchars(var_export($item[1], true), ENT_COMPAT | ENT_SUBSTITUTE, $this->charset));
            }

            $result[] = is_int($key) ? $formattedValue : sprintf("'%s' => %s", $key, $formattedValue);
        }

        return implode(', ', $result);
    }

    /**
     * Formats a file path.
     *
     * @param string $file An absolute file path
     * @param int    $line The line number
     * @param string $text Use this text for the link rather than the file path
     *
     * @return string
     */
    protected function formatFile($file, $line, $text = null) {
        $file = trim($file);

        if (null === $text) {
            $text = str_replace('/', DIRECTORY_SEPARATOR, $file);
            if (0 === strpos($text, $this->projectDir)) {
                $text = substr($text, strlen($this->projectDir));
                $text = explode(DIRECTORY_SEPARATOR, $text, 2);
                $text = sprintf('<abbr title="%s%2$s">%s</abbr>%s', $this->projectDir, $text[0], isset($text[1]) ? DIRECTORY_SEPARATOR.$text[1] : '');
            }
        }

        return sprintf('%s at line %s', $text, $line);

        // Original Twig Extension Code
        $file = trim($file);

        if (null === $text) {
            $text = str_replace('/', DIRECTORY_SEPARATOR, $file);
            if (0 === strpos($text, $this->projectDir)) {
                $text = substr($text, strlen($this->projectDir));
                $text = explode(DIRECTORY_SEPARATOR, $text, 2);
                $text = sprintf('<abbr title="%s%2$s">%s</abbr>%s', $this->projectDir, $text[0], isset($text[1]) ? DIRECTORY_SEPARATOR.$text[1] : '');
            }
        }

        $text = "$text at line $line";

        if (false !== $link = $this->getFileLink($file, $line)) {
            return sprintf('<a href="%s" title="Click to open this file" class="file_link">%s</a>', htmlspecialchars($link, ENT_COMPAT | ENT_SUBSTITUTE, $this->charset), $text);
        }

        return $text;
    }

    /**
     * Returns the link for a given file/line pair.
     *
     * @param string $file An absolute file path
     * @param int    $line The line number
     *
     * @return string|false A link or false
     */
    public function getFileLink($file, $line) {
        if ($fmt = $this->fileLinkFormat) {
            return is_string($fmt) ? strtr($fmt, ['%f' => $file, '%l' => $line]) : $fmt->format($file, $line);
        }

        return false;
    }
}
