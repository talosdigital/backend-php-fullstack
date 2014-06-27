<?php
namespace Sales;

use Zend\Module\Consumer\AutoloaderProvider,
	Zend\EventManager\StaticEventManager,
	Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface;

use Sales\Service\OrderService;
use Sales\Service\QuoteService;
use Sales\Service\VoucherService;
use User\Service\UserService;

use Sales\Helper\OrderHelper;
use Sales\Helper\QuoteHelper;
use Sales\Helper\VoucherHelper;
use User\Helper\UserHelper;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
	const EVENT_NEW_ORDER = "event_new_order";
    const ERROR_PAYPAL_ERROR = 3000;
	
    /**
     * Get Autoloader Configuration
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array();
	}

    public function init($moduleManager)
    {
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, \Zend\Mvc\MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 100);

		// Emails
		$emailHelper = new \Sales\Helper\EmailHelper();
        $sharedEvents->attach("Sales", self::EVENT_NEW_ORDER, array($emailHelper, 'newOrder'));
	}

	public function preDispatch($event)
    {
    	//Unauthorized request after success login
    	/*$session = $event->getApplication()->getServiceManager()->get('session');
		if($lastRequest = $session->get("lastRequest")) {
			$event->getTarget()->getRequest()->setMethod($lastRequest["request"]->getMethod());
			$event->getTarget()->getRequest()->setPost($lastRequest["request"]->getPost());
			$event->getTarget()->getRequest()->setQuery($lastRequest["request"]->getQuery());

			//Delete request
			$session->set("lastRequest", null);
		}

        //Easy
        $event->getTarget()->user = $event->getTarget()->authPlugin()->getIdentity();

        //ServiceManager
		$sm = $event->getApplication()->getServiceManager();

        //Services
        //...
		$event->getTarget()->orderService = new OrderService($sm);
		$event->getTarget()->quoteService = new QuoteService($sm);
		$event->getTarget()->voucherService = new VoucherService($sm);
		$event->getTarget()->userService = new UserService($sm);

        //Helpers
		$event->getTarget()->quoteHelper = new QuoteHelper($sm);
		$event->getTarget()->voucherHelper = new VoucherHelper($sm);
		$event->getTarget()->orderHelper = new OrderHelper($sm);
		$event->getTarget()->userHelper = new UserHelper($sm);		

        //Validator
        //...

        //Vendor Helpers
        $event->getTarget()->email = $sm->get('email');
        $event->getTarget()->session = $sm->get('session');*/
    }

}