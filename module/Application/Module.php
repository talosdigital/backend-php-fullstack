<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;
use Application\Controller\ErrorController as Error;
use Zend\View\Model\JsonModel;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $sharedManager = $eventManager->getSharedManager();
        //controller can't dispatch request action that passed to the url
        $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController',
               'dispatch',
               array($this, 'handleControllerCannotDispatchRequest' ), 101);
        //controller not found, invalid, or route is not matched anymore
        $eventManager->attach('dispatch.error', 
               array($this,
              'handleControllerNotFoundAndControllerInvalidAndRouteNotFound' ), 100);
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
     
    public function handleControllerCannotDispatchRequest(MvcEvent $e)
    {
        $action = $e->getRouteMatch()->getParam('action');
        $controller = get_class($e->getTarget());
         
        // error-controller-cannot-dispatch
        if (! method_exists($e->getTarget(), $action.'Action')) {
            $logText = 'The requested controller '.
                        $controller.' was unable to dispatch the request : '.$action.'Action';
            //you can do logging, redirect, etc here..
             echo $logText;
        }
    }
     
    public function handleControllerNotFoundAndControllerInvalidAndRouteNotFound(MvcEvent $e)
    {
        $error  = $e->getError();
        if ($error == Application::ERROR_CONTROLLER_NOT_FOUND) {
            //there is no controller named $e->getRouteMatch()->getParam('controller')
            $logText =  'The requested controller '
                        .$e->getRouteMatch()->getParam('controller'). '  could not be mapped to an existing controller class.';
             
            //you can do logging, redirect, etc here..
            echo $logText;
        }
         
        if ($error == Application::ERROR_CONTROLLER_INVALID) {
            //the controller doesn't extends AbstractActionController
            $logText =  'The requested controller '
                        .$e->getRouteMatch()->getParam('controller'). ' is not dispatchable';
             
            //you can do logging, redirect, etc here..
            echo $logText;
        }
         
        if ($error == Application::ERROR_ROUTER_NO_MATCH) {
            $event = \Zend\Mvc\MvcEvent::EVENT_DISPATCH;
            // the url doesn't match route, for example, there is no /foo literal of route
            var_dump($event->getTarget()->getRequest());
            die();
            $logText =  'The requested URL could not be matched by routing.';
            //you can do logging, redirect, etc here...
            echo $logText;
        }
    } 

}
