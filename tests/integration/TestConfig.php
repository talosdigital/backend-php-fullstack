<?php
return array(
    'modules' => array(
        'MyZend',
        'Application',
        'DoctrineModule',
        'DoctrineMongoODMModule',
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineMongoODM',
        'BjyAuthorize',
        'User',
        'Geolocation'
    ),
    
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../config/autoload/{,*.}{global,local}.php',
            '../../config/autoload/env.'.(getenv('APPLICATION_ENV') ?: 'production').'.config.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
        ),
    ),
    
);
