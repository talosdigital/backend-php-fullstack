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
    // Perhaps some other config here

    // Start to overwrite zfcuser's route
    'router' => array(
        'routes' => array(
            'zfcuser' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/users[/:action][/:id]',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'index',
                    ),
                ),
            ),
            'Session' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/session',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'session',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'User\Controller\User' => 'User\Controller\UserController',
            'User\Helper\Awesome' => 'User\Helper\AwesomeController',
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