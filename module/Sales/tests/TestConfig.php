<?php
return array(
    'modules' => array(
        'MyZend',

		//Database
		'DoctrineModule',
 		'DoctrineMongoODMModule',

		//User
 		'ZfcBase',
 		'ZfcUser',
 		'ZfcUserDoctrineMongoODM',
 		//'BjyAuthorize',
 		//'Facebook',
 		'User',
		'Media',
    	'Sales',
    	'Email',
    	'Geolocation',
		'Notification',
    	'Subscription',
    	'Email'
    ),

    'module_listener_options' => array(
        'config_glob_paths'    => array(
            '../../../config/autoload/{,*.}{global,local,testing}.php',
        ),
        'module_paths' => array(
            'module',
            'vendor',
        ),
    ),

);
