<?php

$headerComment = <<<COMMENT
This file is part of the ForciCatchableBundle package.

Copyright (c) Forci Web Consulting Ltd.

Author Tatyana Mincheva <tatjana@forci.com>
Author Martin Kirilov <martin@forci.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
COMMENT;

$finder = PhpCsFixer\Finder::create()->in(__DIR__ . '/src');


// https://cs.symfony.com/
$config = (new \PhpCsFixer\Config());

$config
    ->setRules([
        '@Symfony' => true,
        'no_blank_lines_after_class_opening' => false,
        'array_syntax' => [
            'syntax' => 'short'
        ],
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'same'
        ],
        'trailing_comma_in_multiline' => false,
        'header_comment' => [
            'header' => $headerComment
        ]
    ])
    ->setUsingCache(false)
    ->setFinder($finder);

return $config;
