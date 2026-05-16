<?php
/**
 * Muslim Clock Web - Sample Config
 * File ini akan di-generate otomatis menjadi config.php oleh installer.
 */
return [
    'db' => [
        'host'     => 'localhost',
        'port'     => 3306,
        'name'     => 'muslimclock',
        'user'     => 'root',
        'pass'     => '',
        'charset'  => 'utf8mb4',
        'prefix'   => 'mc_',
    ],
    'app' => [
        'name'     => 'Muslim Clock',
        'base_url' => '',           // diisi otomatis (mis. http://masjid.test)
        'timezone' => 'Asia/Jakarta',
        'secret'   => '',           // session/csrf secret
        'installed'=> false,
    ],
];
