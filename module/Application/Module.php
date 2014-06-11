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
        //controller not found, invalid, or route is not matched anymore
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, 
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
     
     
    public function handleControllerNotFoundAndControllerInvalidAndRouteNotFound(MvcEvent $e)
    {
        
        $error  = $e->getError();
        $logText =  'Internal server error';
        $statusCode = 500;
		$errorCode = 0;
        if ($error == Application::ERROR_CONTROLLER_NOT_FOUND) {
            //there is no controller named $e->getRouteMatch()->getParam('controller')
            $logText =  'The requested controller '
                        .$e->getRouteMatch()->getParam('controller'). '  could not be mapped to an existing controller class.';
            $statusCode = 404;
        }
        elseif ($error == Application::ERROR_CONTROLLER_INVALID) {
            //the controller doesn't extends AbstractActionController
            $logText =  'The requested controller '
                        .$e->getRouteMatch()->getParam('controller'). ' is not dispatchable';
            $statusCode = 404;
        }
        elseif ($error == Application::ERROR_ROUTER_NO_MATCH) {
            // the url doesn't match route, for example, there is no /foo literal of route
            $logText =  'The requested URL could not be matched by routing.';
            $statusCode = 404;
        }
        elseif ($error == Application::ERROR_EXCEPTION) {
            if($e->getParam('exception')) {
                $logText =  $e->getParam('exception')->getMessage();
				$errorCode = $e->getParam('exception')->getCode();
            }
            $statusCode = 500;
        }

        $response = $e->getResponse();
        echo json_encode(array('error_code' => $errorCode, 'message' => $logText));
        $response->setStatusCode($statusCode);
        $response->sendHeaders();
        exit;
    } 

}
