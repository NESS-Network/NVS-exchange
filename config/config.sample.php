<?php

return [
    'ness' => [
        'host' => 'localhost',
        'port' => '6660',
        'wallets' => [
            '2021_12_02_fdgh.wlt' => '123456789$',
            '2022_06_15_fada.wlt' => 'qwerty'
        ],
        'main_wallet_id' => '2021_12_02_fdgh.wlt',
        'prefix' => 'http://',
        /** Generate new address for new slot or use existing one */
        'gen_address' => true
    ],
    'emercoin' => [
        'host' => '127.0.0.1',
        'port' => '6662',
        'user' => '',
        'password' => ''
    ],
    'db' => [
        'host' => 'localhost',
        'user' => '',
        'password' => '',
        'database' => ''
    ],
    'exchange' => [
        'min_sum' => [
            'emc' => 0.01,
            'ness' => 0.1,
            'nch' => 1
        ]
    ]
];
