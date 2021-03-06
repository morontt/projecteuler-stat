<?php

$finder = Symfony\CS\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/features',
    ])
;

$config = Symfony\CS\Config::create()
    ->fixers([
        '-phpdoc_params',
        '-phpdoc_short_description',
        '-pre_increment',
        '-spaces_cast',
        'concat_with_spaces',
        'ordered_use',
    ])
    ->finder($finder)
;

return $config;
