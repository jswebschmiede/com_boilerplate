<?php

/**
 * @package     com_boilerplate
 * @version     1.0.0
 * @copyright   Copyright (C) 2024. All rights reserved.
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withSkip([
        __DIR__ . '/src/administrator/components/com_boilerplate/vendor',
        '*/vendor/*',
        '*/node_modules/*',
        ChangeSwitchToMatchRector::class,
        ArrayToFirstClassCallableRector::class,
    ])
    ->withPhpSets(php81: true)
    ->withPreparedSets(
        deadCode: false,
        codeQuality: false,
        codingStyle: false,
        typeDeclarations: false,
        privatization: false,
        naming: false,
        instanceOf: false,
        earlyReturn: false,
    );
