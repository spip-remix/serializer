<?php

/**
 * @see https://cs.symfony.com/doc/config.html
 */

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
    ])
    ->setFinder($finder)
;
