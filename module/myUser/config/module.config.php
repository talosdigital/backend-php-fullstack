<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace myUser;

return array(
    // Perhaps some other config here

    // Start to overwrite zfcuser's route
    'router' => array(
        'routes' => array(
            'zfcuser' => array(
                'options' => array(
                    'route' => '/users',
                    'defaults' => array(
                        'controller' => 'myUser\Controller\User'

                    ),
                ),
            ),
            'Session' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/session',
                    'defaults' => array(
                        'controller' => 'myUser\Controller\User',
                        'action'     => 'session',
                    ),
                ),
            ),
        'Awesome' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/awesomeThings',
                    'defaults' => array(
                        'controller' => 'myUser\Helper\Awesome',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'myUser\Controller\User' => 'myUser\Controller\UserController',
            'myUser\Helper\Awesome' => 'myUser\Helper\AwesomeController',
        ),
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
    )
);