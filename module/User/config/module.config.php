<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

return array(

    'controllers' => array(
        'invokables' => array(
            'User\Controller\Auth' => 'User\Controller\AuthController',
            'User\Controller\Profile' => 'User\Controller\ProfileController',
            'User\Controller\Address' => 'User\Controller\AddressController',
            'User\Controller\Phone' => 'User\Controller\PhoneController',
            'User\Controller\Picture' => 'User\Controller\PictureController'
		),
    ),

    'service_manager' => array(
        'factories' => array(
            'userService' => function ($serviceManager) {
                return new Service\UserService();
            },
        ),
    ),	

	'bjyauthorize' => array(
	    'guards' => array(
	        'BjyAuthorize\Guard\Controller' => array(
	            array('controller' => 'User\Controller\Auth', 'roles' => array('user', 'guest')),           
	            array('controller' => 'User\Controller\Profile', 'roles' => array('user')),            
	            array('controller' => 'User\Controller\Address', 'roles' => array('user')),
                array('controller' => 'User\Controller\Phone', 'roles' => array('user')),
                array('controller' => 'User\Controller\Picture', 'roles' => array('user'))            
	        ),
	    ),
	),

    'router' => array(
        'routes' => array(
        	// override zfcuser controllers
            'zfcuser' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/user',
                    'defaults' => array(
                        '__NAMESPACE__' => 'User\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'child_routes' => array(),
            ),
            // User default routing
            'user' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/user',
                    'defaults' => array(
                        '__NAMESPACE__' => 'User\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            // User RESTful routing
            'user-rest' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/user',
                    'defaults' => array(
                        '__NAMESPACE__' => 'User\Controller',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:controller',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
		)
    ),
    
    'doctrine' => array(
	    'driver' => array(
	          __NAMESPACE__ . '_driver' => array(
	            'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
	            'cache' => 'array',
	            'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
          ),
	          'odm_default' => array(
	          'drivers' => array(
	           	__NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
	           )
			)
		)
	),

);