<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('var')
    ->exclude('Migrations')
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;