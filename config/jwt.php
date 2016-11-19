<?php

return [
    'JWT_SECRET'    => env('JWT_SECRET'),
    'JWT_ALGO'      => env('JWT_ALGO','SHA256'),
    'JWT_TTL'       => env('JWT_TTL',60),
    'JWT_REFRESH_TTL'=>env('JWT_REFRESH_TTL',60),
    'JWT_ENABLE'    => env('JWT_ENABLE',true),
];