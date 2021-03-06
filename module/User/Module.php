<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

class Module

{
	const ERROR_UNEXPECTED = 0;
    const ERROR_NOT_SUPPORTED_FUNCTION = 10;
	const ERROR_DUPLICATED_EMAIL = 100;
	const ERROR_LOGIN_FAILED = 200;
    const ERROR_CHANGE_PASSWORD_FAILED = 300;
    const ERROR_CHANGE_EMAIL_FAILED = 400;
    const ERROR_FACEBOOK_REGISTER_FAILED = 500;
    const ERROR_FACEBOOK_ALREADY_MERGED = 501;
    const ERROR_NOT_LOGGED_IN = 600;
    const ERROR_EMPTY_USER = 700;
    const ERROR_EMPTY_USER_NAME = 710;
    const ERROR_ADDRESS_NOT_FOUND = 800;
    const ERROR_BAD_REQUEST = 900;
    const ERROR_USER_WITHOUT_PICTURE = 1000;
    const ERROR_PICTURE_UPLOAD_FAILED = 1100;
    const ERROR_TWITTER_API_BAD_REQUEST = 1200;
    const ERROR_TWITTER_ALREADY_MERGED = 1300;
	
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $sharedManager = $eventManager->getSharedManager();
        //controller not found, invalid, or route is not matched anymore
    }


    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}