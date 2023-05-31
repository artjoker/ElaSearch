<?php

return [
    'hosts' => [
        env('ELASTIC_HOST', 'localhost') . ':' . env('ELASTIC_PORT', '9200'),
    ],
    'username' => env('ELASTIC_USERNAME', 'elastic'),
    'password' => env('ELASTIC_PASSWORD', ''),
];
