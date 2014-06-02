<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(array('src'))
;

return Symfony\CS\Config\Config::create()
    ->fixers(array('-Psr0Fixer','all'))
    ->finder($finder)
;