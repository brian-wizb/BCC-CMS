<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Communication Delivery Mode
    |--------------------------------------------------------------------------
    |
    | queued    => Use background queue workers (recommended for production).
    | immediate => Send inside the request (no worker required).
    | auto      => Immediate only when queue.default is sync, else queued.
    |
    */
    'delivery_mode' => env('COMMUNICATION_DELIVERY_MODE', 'queued'),
];
