<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractRestfulController
{
    public function indexAction()
    {
        $awesomeArray[0] = array('info' => 'HTML5 Boilerplate is a professional front-end template for building fast, robust, and adaptable web apps or sites.',
                                'name' => 'HTML5 Boilerplate');

        $awesomeArray[1] = array('info' => 'AngularJS is a toolset for building the framework most suited to your application development.',
                            'name' => 'AngularJS');

        $awesomeArray[2] = array('info' => 'PHP 5',
                            'name' => 'PHP 5');

        $awesomeArray[3] = array('info' => 'Zend 2 Framework for PHP',
                            'name' => 'Zend 2');

        $awesomeArray[4] = array('info' => 'Doctrine is an ODM',
                            'name' => 'Doctrine');

        $awesomeArray[5] = array('info' => 'An excellent document database. Combined with Mongoose to simplify adding validation and business logic.',
                            'name' => 'MongoDB');

        return new JsonModel($awesomeArray);
    }
}
