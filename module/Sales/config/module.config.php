<?php
namespace Sales;

return array(
    'controllers' => array(
        'invokables' => array(
            'Sales\Controller\Checkout'	=> 'Sales\Controller\CheckoutController'
        ),
    ),

    'router' => array(
        'routes' => array(
            /* /module / controller / action */
            'sales' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/sales',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Sales\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
					),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller]/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array(
							),
                        ),
                    ),
				),
			),
        ),
	),

    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Document')
            ),
            'odm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Document' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),

    'service_manager' => array(
    'factories' => array(
        'userHelper' => function ($serviceManager) {
            return new User\Helper\UserHelper($serviceManager);
        })
    ),

	'bjyauthorize' => array(
	    'guards' => array(
	        'BjyAuthorize\Guard\Controller' => array(
	            array('controller' => 'Sales\Controller\Checkout', 'roles' => array('user'))
	        ),
	    ),
	),
);