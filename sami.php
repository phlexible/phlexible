<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('bin')
    ->exclude('build')
    ->exclude('Resources')
    ->exclude('Tests')
    ->exclude('vendor')
    ->in(__DIR__.'/src')
;

return new Sami($iterator, array(
    'title'                => 'phlexible API',
    'build_dir'            => __DIR__.'/build/apidocs',
    'cache_dir'            => __DIR__.'/build/cache',
    'default_opened_level' => 2,
));
