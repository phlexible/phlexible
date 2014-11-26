<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->in(__DIR__.'/src')
;

return new Sami($iterator, array(
    'title'                => 'Symfony2 API',
    'build_dir'            => __DIR__.'/build/sami',
    'cache_dir'            => __DIR__.'/build/cache',
    'default_opened_level' => 2,
));
