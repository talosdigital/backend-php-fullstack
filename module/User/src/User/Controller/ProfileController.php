<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\Response;
use User\Entity\User;
use Zend\View\Model\JsonModel;

/**
 *
 * @SWG\Model(id="profile")
 */
class ProfileController extends AbstractActionController
{
	/** @SWG\Resource(
    *   resourcePath="profile",
    *   basePath = "/../../user")
    */

	/**
     *
     * @SWG\Api(
     *   path="/profile",
     *   description="Dashboard actions",
     *    @SWG\Operation(
     *      nickname="dashboard",
     *      method = "GET",
     *      summary="dashboard index"
     *   )
     *  )
     *)
     */
	public function indexAction() {
	
		return new JsonModel(array("message" => "This is your dashboard."));
	}


	public function getCurrentUser(){
		return $this->zfcUserAuthentication()->getIdentity();
	}
	/**
     *
     * @SWG\Api(
     *   path="/profile/change-password",
     *    @SWG\Operation(
     *      nickname="change_password",
     *      method = "POST",
     *      summary="change password form",
 	 *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="currentPassword",
     *              paramType="path",
     *              type="string",
     *              required=false,
     *				description = "It's not required if the user signed up with social networks"
     *          ),
     *          @SWG\Parameter(
     *              name="newPassword",
     *              paramType="path",
     *              type="string",
     *              required=true
     *          ),
     *          @SWG\Parameter(
     *              name="newPasswordVerify",
     *              paramType="path",
     *              type="string",
     *              required=true
     *          )
     *      )
     *   )
     *  )
     *)
     */

	/**
     *
     * @SWG\Api(
     *   path="/profile/change-password",
     *    @SWG\Operation(
     *      nickname="change_password_arguments",
     *      method = "GET",
     *		summary = "check if email/social network adapter"
     *   )
     *  )
     *)
     */
	public function changePasswordAction(){
		$isPost = $this->getRequest()->isPost();
		$isGet = $this->getRequest()->isGet();
		$user = $this->getCurrentUser();

		if($isGet){
			if($user->getPassword()){
				$passwordRequired = true;
			}
			else{
				$passwordRequired = false;
			}
			return new JsonModel(array("passwordRequired" => $passwordRequired));	
		}

		if($isPost){
			$data = $this->getRequest()->getPost();
			$adapter = $this->loadAdapter();
			$user = $adapter->signup($data);
			return new JsonModel(array("message" => "User was created."));	
		}
	}
}