<?php
    $EM_CONF[$_EXTKEY] = [
        'title' => 'HTML tables (IRRE)',
        'description' => 'Create sophisticated HTML tables easily using Inline Records (IRRE)',
        'category' => 'plugin',
        'author' => 'Martin Zarth',
        'author_company' => 'Zarthwork',
        'author_email' => 'martin@zarthwork.de',
        'state' => 'stable',
        'clearCacheOnLoad' => true,
        'version' => '2.0.0',
        'constraints' => [
            'depends' => [
                'typo3' => '12.4.0-14.99.99',
            ]
        ]
    ];
