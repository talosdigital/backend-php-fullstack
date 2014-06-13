<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Application\Entity\Response;
use User\Entity\User;
use Zend\View\Model\JsonModel;

/**
 *
 * @SWG\Model(id="profile")
 */
class ProfileController extends AbstractRestfulController
{
	/** @SWG\Resource(
    *   resourcePath="profile",
    *   basePath = "api/user")
    */

	private function loadEmailAdapter() {
		$adapter = new \User\Auth\EmailAdapter($this->getServiceLocator());
		return $adapter;
	}
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
     *              paramType="form",
     *              type="string",
     *              required=false,
     *				description = "It's not required if the user signed up with social networks"
     *          ),
     *          @SWG\Parameter(
     *              name="newPassword",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          ),
     *          @SWG\Parameter(
     *              name="newPasswordVerify",
     *              paramType="form",
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
		$request = $this->getRequest();
		$isPost = $request->isPost();
		$isGet = $request->isGet();
		$user = $this->getCurrentUser();

		if($isGet){
			$passwordRequired = $this->getPasswordRequired();
			return new JsonModel(array("passwordRequired" => $passwordRequired));	
		}

		if($isPost){
			$data = $request->getPost();
			$adapter = $this->loadEmailAdapter();
			$adapter->changePassword($data, $user);
			return new JsonModel(array("message" => "Password changed."));
		}
	}

	/**
     *
     * @SWG\Api(
     *   path="/profile/change-email",
     *    @SWG\Operation(
     *      nickname="change_email",
     *      method = "POST",
     *      summary="change email form",
 	 *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="email",
     *              name="email",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          ),
     *          @SWG\Parameter(
     *              name="password",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          )
     *      )
     *   )
     *  )
     *)
     */
	public function changeEmailAction(){
		$user = $this->getCurrentUser();
		$data = $this->getRequest()->getPost();
		
          $adapter = $this->loadEmailAdapter();
		$adapter->changeEmail($data, $user);

		return new JsonModel(array("message" => "Email changed."));
	}

     /**
     *
     * @SWG\Api(
     *   path="/profile/social",
     *    @SWG\Operation(
     *      nickname="social",
     *      method = "GET",
     *         summary = "check merged social networks"
     *   )
     *  )
     *)
     */
     public function socialAction(){
          $social = $this->getSocial();
          return new JsonModel($social);
     }

     //Support functions

     private function getSocial(){
          $user = $this->getCurrentUser();
          $social = array();

          if($user->getFacebook()->getFacebookId()){
               $social['facebook'] = true;
          }
          else{
               $social['facebook'] = false;
          }

          return $social;
     }

     private function getCurrentUser(){
          return $this->zfcUserAuthentication()->getIdentity();
     }

     private function getPasswordRequired(){
          $user = $this->getCurrentUser();
          if($user->getPassword()){
               return true;
          }
          else{
               return false;
          }
     }
}