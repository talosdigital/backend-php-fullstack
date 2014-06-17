<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

use User\Entity\User;
use User\Entity\User\Address;
use User\Facade\AddressFacade; 
use User\Helper\User as UserHelper;
use User\Helper\Address as AddressHelper;

/**
 *
 * @SWG\Model(id="address")
 */
class AddressController extends AbstractRestfulController
{	

	/** @SWG\Resource(
    *   resourcePath="address",
    *   basePath = "api/user")
    */

	/*public function indexAction() {
        return new JsonModel();
    }*/


    /**
     *
     * @SWG\Api(
     *   path="/address",
     *   description="Addresses operations",
     *    @SWG\Operation(
     *      nickname="address",
     *      method = "GET",
     *      summary="addresses list of the current user"
     *   )
     *  )
     *)
     */

 	public function getList() {
 		$user = UserHelper::getCurrentUser();
		$facade = new AddressFacade($user);
		
		return new JsonModel($facade->getList($user->getAddresses())); 		
    }	
 	/*
	public function get($id) {
		$user = UserHelper::getCurrentUser();
		$facade = new AddressFacade($user);
		$address = $user->getAddresses()->get($id);
		
		return new JsonModel($facade->get($address)); 		
	}*/

	 /**
     *
     * @SWG\Api(
     *   path="/address",
     *    @SWG\Operation(
     *      nickname="add_address",
     *      method = "POST",
     *      summary="add an address",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="label",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "test address"
     *          ),
     *          @SWG\Parameter(
     *              name="companyName",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *              defaultValue = "Talos Digital"
     *          ),
     *          @SWG\Parameter(
     *              name="firstname",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "Javier"
     *          ),
     *          @SWG\Parameter(
     *              name="lastname",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "De la Hoz"
     *          ),     
     *          @SWG\Parameter(
     *              name="street",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "Cll 8 # 8 - 8"
     *          ),
     *          @SWG\Parameter(
     *              name="postCode",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *              defaultValue = "12345"
     *          )     
     *   )
     *  )
     *)
     */
	
	public function create() {
    	$user = UserHelper::getCurrentUser();
    	$data = $this->getRequest()->getPost();

    	$address = new Address();
    	$address = AddressHelper::setAddressByRequest($address, $data);

        try{
	    	$user->getAddresses()->add($address);
	    	UserHelper::getUserMapper()->update($user);
    	}
    	catch (\Exception $ex){
    		throw new \Exception($ex, \User\Module::ERROR_UNEXPECTED);
    	}

    	return new JsonModel(array('message' => 'Address added'));
    }

   	 /**
     *
     * @SWG\Api(
     *   path="/address",
     *    @SWG\Operation(
     *      nickname="update_address",
     *      method = "PUT",
     *      summary="update an address",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="id",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "0"
     *          ),
     *          @SWG\Parameter(
     *              name="label",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "test address"
     *          ),
     *          @SWG\Parameter(
     *              name="companyName",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *              defaultValue = "Talos Digital"
     *          ),
     *          @SWG\Parameter(
     *              name="firstname",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "Javier"
     *          ),
     *          @SWG\Parameter(
     *              name="lastname",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "De la Hoz"
     *          ),     
     *          @SWG\Parameter(
     *              name="street",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "Cll 8 # 8 - 8"
     *          ),
     *          @SWG\Parameter(
     *              name="postCode",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *              defaultValue = "12345"
     *          )     
     *   )
     *  )
     *)
     */
	public function replaceList() {
        $user = UserHelper::getCurrentUser();

    	$data = array();
		parse_str($this->getRequest()->getContent(), $data);

    	$address = $user->getAddresses()->get($data['id']);
    	$address = AddressHelper::setAddressByRequest($address, $data);

        try{
	    	UserHelper::getUserMapper()->update($user);
    	}
    	catch (\Exception $ex){
    		throw new \Exception($ex, \User\Module::ERROR_UNEXPECTED);
    	}

    	return new JsonModel(array('message' => 'Address updated'));
    }

       	 /**
     *
     * @SWG\Api(
     *   path="/address",
     *    @SWG\Operation(
     *      nickname="delete_address",
     *      method = "DELETE",
     *      summary="delete an address",
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
		$user = UserHelper::getCurrentUser();
    	$data = array();
		parse_str($this->getRequest()->getContent(), $data);

    	$address = $user->getAddresses()->get($data['id']);

    	if(!$address){
    		throw new \Exception("Address not found", \User\Module::ERROR_ADDRESS_NOT_FOUND);
    	}

    	$user->getAddresses()->removeElement($address);

    	UserHelper::getUserMapper()->update($user);
    	return new JsonModel(array('message' => 'Address deleted'));
	}

}