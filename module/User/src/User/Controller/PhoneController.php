<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

use User\Entity\User;
use User\Entity\User\Phonenumber;
use User\Facade\PhoneFacade; 
use User\Helper\User as UserHelper;

/**
 *
 * @SWG\Model(id="phone")
 */
class PhoneController extends AbstractRestfulController
{	

	/** @SWG\Resource(
    *   resourcePath="phone",
    *   basePath = "api/user")
    */

	/*public function indexAction() {
        return new JsonModel();
    }*/


    /**
     *
     * @SWG\Api(
     *   path="/phone",
     *   description="Phonenumbers operations",
     *    @SWG\Operation(
     *      nickname="phone",
     *      method = "GET",
     *      summary="phone numbers list of the current user"
     *   )
     *  )
     *)
     */

 	public function getList() {
 		$user = $this->zfcUserAuthentication()->getIdentity();
        $user = $this->getServiceLocator()->get('userHelper')->getCurrentUser($user);

		$facade = new PhoneFacade($user);
		
		return new JsonModel($facade->getList($user->getPhonenumbers())); 		
    }

	 /**
     *
     * @SWG\Api(
     *   path="/phone",
     *    @SWG\Operation(
     *      nickname="add_phone",
     *      method = "POST",
     *      summary="add a phone number",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="phonenumber",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "123456"
     *          )
     *   )
     *  )
     *)
     */
	
	public function create() {
    	$user = $this->zfcUserAuthentication()->getIdentity();
        $user = $this->getServiceLocator()->get('userHelper')->getCurrentUser($user);

    	$data = $this->getRequest()->getPost();

    	$phone = new Phonenumber();
    	$phone->setPhonenumber($data->get('phonenumber'));

        try{
	    	$user->getPhonenumbers()->add($phone);
	    	$this->getServiceLocator()->get('userHelper')->saveUser($user);
    	}
    	catch (\Exception $ex){
    		throw new \Exception($ex, \User\Module::ERROR_UNEXPECTED);
    	}

    	return new JsonModel(array('message' => 'Phone number added'));
    }

   	 /**
     *
     * @SWG\Api(
     *   path="/phone",
     *    @SWG\Operation(
     *      nickname="update_phone",
     *      method = "PUT",
     *      summary="update an phone number",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="id",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "0"
     *          ),
     *          @SWG\Parameter(
     *              name="phonenumber",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "987654"
     *          )
     *   )
     *  )
     *)
     */
	public function replaceList() {
        $user = $this->zfcUserAuthentication()->getIdentity();
        $user = $this->getServiceLocator()->get('userHelper')->getCurrentUser($user);

    	$data = array();
		parse_str($this->getRequest()->getContent(), $data);

    	$phonenumber = $user->getPhonenumbers()->get($data['id']);
    	$phonenumber->setPhonenumber($data['phonenumber']);

        try{
	    	$this->getServiceLocator()->get('userHelper')->saveUser($user);
    	}
    	catch (\Exception $ex){
    		throw new \Exception($ex, \User\Module::ERROR_UNEXPECTED);
    	}

    	return new JsonModel(array('message' => 'Phone number updated'));
    }

       	 /**
     *
     * @SWG\Api(
     *   path="/phone",
     *    @SWG\Operation(
     *      nickname="delete_phone",
     *      method = "DELETE",
     *      summary="delete an phone number",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="id",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "0"
     *          )
     *   )
     *  )
     *)
     */
	public function deleteList() {
		$user = $this->zfcUserAuthentication()->getIdentity();
        $user = $this->getServiceLocator()->get('userHelper')->getCurrentUser($user);
        
    	$data = array();
		parse_str($this->getRequest()->getContent(), $data);

    	$phone = $user->getPhonenumbers()->get($data['id']);

    	if(!$phone){
    		throw new \Exception("Phone number not found", \User\Module::ERROR_ADDRESS_NOT_FOUND);
    	}

    	$user->getPhonenumbers()->removeElement($phone);

    	$this->getServiceLocator()->get('userHelper')->saveUser($user);
    	return new JsonModel(array('message' => 'Phone number deleted'));
	}

}