<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Application\Entity\Response;
use User\Entity\User;
use Zend\View\Model\JsonModel;
use User\Helper\User as UserHelper;

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
		$user = UserHelper::getCurrentUser();

		if($isGet){
			$passwordRequired = \User\Facade\ProfileFacade::getPasswordRequired($this->getCurrentUser());
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
		$user = UserHelper::getCurrentUser();
		$data = $this->getRequest()->getPost();
		
          $adapter = $this->loadEmailAdapter();
		$adapter->changeEmail($data, $user);

		return new JsonModel(array("message" => "Email changed."));
	}

     /**
     *
     * @SWG\Api(
     *   path="/profile",
     *    @SWG\Operation(
     *      nickname="get_user_list",
     *      method = "GET",
     *         summary = "user information"
     *   )
     *  )
     *)
     */
     public function getList(){
          return new JsonModel($this->loadEmailAdapter()->getList());    
     }
}