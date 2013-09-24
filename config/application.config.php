<?php
return array(
    // This should be an array of module namespaces used in the application.
    'modules' => array(
        //'BjyProfiler',
        //'ZendDeveloperTools',
        'Application',
        'DoctrineModule',
        'DoctrineORMModule',
        'FileBank',
        'AfcCommons',
        'User',
        'Company',
        'Shipper',
        'Truck',
        'Driver',
        'Trailer',
    	'Transport',
    	'SourceAddress',
    	'DestinationAddress'
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor'
        ),
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php'
        ),
        'config_cache_enabled' => false
    )
);