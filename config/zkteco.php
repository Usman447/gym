<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ZKTeco Biometric Device Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for ZKTeco biometric device connection
    |
    */

    'device_ip' => env('ZKTECO_DEVICE_IP', '192.168.18.201'),
    'device_port' => env('ZKTECO_DEVICE_PORT', 4370),
    'device_timeout' => env('ZKTECO_DEVICE_TIMEOUT', 10),
    'device_password' => env('ZKTECO_DEVICE_PASSWORD', 0),
    
    /*
    |--------------------------------------------------------------------------
    | Python Script Path
    |--------------------------------------------------------------------------
    |
    | Path to the Python script for ZKTeco operations
    |
    */
    'python_script_path' => base_path('python_scripts/zkt.py'),
    'python_executable' => env('PYTHON_EXECUTABLE', 'python'),
];

