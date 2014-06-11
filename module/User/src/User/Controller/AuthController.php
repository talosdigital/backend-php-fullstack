<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Application\Entity\Response;
use User\Entity\User;
use Zend\View\Model\JsonModel;
use Swagger\Swagger;

/**
 *
 * @SWG\Model(id="auth")
 */
class AuthController extends AbstractRestfulController
{
	/** @SWG\Resource(
    *   resourcePath="auth",
    *   basePath = "/../../user")
    */

	private function loadAdapter() {
		$data = $this->getRequest()->getPost();
		
		if($data->get('adapter') == 'facebook') {
			$adapter = new \User\Auth\FacebookAdapter($this->getServiceLocator());
		}
		else {
			$adapter = new \User\Auth\EmailAdapter($this->getServiceLocator());
		}
		
		return $adapter;
	}

	/**
     *
     * @SWG\Api(
     *   path="/auth/signup",
     *   
     *   description="Auth logic (signup, login and logout)",
     *    @SWG\Operation(
     *      nickname="singup",
     *      method = "POST",
     *      summary="signup action",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="adapter",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *				description="It takes the adapter needed ('facebook' for facebook adapter, null for email adapter)"
     *          ),
     *          @SWG\Parameter(
     *              name="email",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *				description = "It's not required only if adapter is not null"
     *          ),
     *          @SWG\Parameter(
     *              name="password",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *				description = "It's not required only if adapter is not null"
     *          ),
     *          @SWG\Parameter(
     *              name="name",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *				description = "It's the display name of the user, and it is not required only if adapter is not null"
     *          )
     *      )
     *   )
     *  )
     *)
     */
	public function signupAction() {
		$data = $this->getRequest()->getPost();
		$adapter = $this->loadAdapter();

		$user = $adapter->signup($data);
		return new JsonModel(array("message" => "User was created."));	
	}
	

		/**
     *
     * @SWG\Api(
     *   path="/auth/login",
     *    @SWG\Operation(
     *      nickname="login",
     *      method = "POST",
     *      summary="login action",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="adapter",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *				description="It takes the adapter needed ('facebook' for facebook adapter, null for email adapter)"
     *          ),
     *          @SWG\Parameter(
     *              name="email",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *				description = "It's not required if adapter is not null"
     *          ),
     *          @SWG\Parameter(
     *              name="password",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *				description = "It's not required if adapter is not null"
     *          ),
     *          @SWG\Parameter(
     *              name="facebookId",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *				description = "It's only required if adapter is facebook"
     *          )
     *      )
     *   )
     *  )
     *)
     */
	public function loginAction() {
		$data = $this->getRequest()->getPost();
		$adapter = $this->loadAdapter();

		$user = $adapter->login($data);
		return new JsonModel(array("message" => "Welcome back."));
	}
	
	/**
     *
     * @SWG\Api(
     *   path="/auth/logout",
     *    @SWG\Operation(
     *      nickname="logout",
     *      method = "GET",
     *      summary="logout action"
     *   )
     *  )
     *)
     */
	public function logoutAction() {
		$adapter = $this->loadAdapter();

		$user = $adapter->logout();
		return new JsonModel(array("message" => "Logout completed."));
	}
}