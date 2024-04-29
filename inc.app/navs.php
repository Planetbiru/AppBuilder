<?php

use AppBuilder\AppNav;
use AppBuilder\AppNavs;

require_once dirname(__DIR__) . "/inc.lib/vendor/autoload.php";

$appNavs = (new AppNavs())
    ->add(new AppNav('builder', 'Builder'))
    ->add(new AppNav('application', 'Application'))
    ->add(new AppNav('module', 'Module', true))
    ->add(new AppNav('column', 'Column'))
    ->add(new AppNav('generated-file', 'File'))
    ->add(new AppNav('generated-query', 'Query'))
    ->add(new AppNav('docs', 'Docs'))
;