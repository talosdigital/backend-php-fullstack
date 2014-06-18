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
    *   basePath = "api/user")
    */

	private function loadAdapter() {
		$data = $this->getRequest()->getPost();
		
		if($data->get('adapter') == 'facebook') {
			$adapter = new \User\Auth\FacebookAdapter($this->getServiceLocator());
		}
          elseif($data->get('adapter') == 'twitter') {
               $adapter = new \User\Auth\TwitterAdapter($this->getServiceLocator());
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
     *      summary="signup action (Email adapter)",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="email",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *				description = "It's not required only if adapter is not null"
     *          ),
     *          @SWG\Parameter(
     *              name="password",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *				description = "It's not required only if adapter is not null"
     *          ),
     *          @SWG\Parameter(
     *              name="name",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *				description = "It's the display name of the user, and it is not required only if adapter is not null"
     *          )
     *      )
     *   
     *  )
     *)
     */

     /**
     *
     * @SWG\Api(
     *   path="/auth/signup",
     *   description="Auth logic (signup, login and logout)",
     *    @SWG\Operation(
     *      nickname="singup",
     *      method = "POST",
     *      summary="signup action (Facebook adapter)",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="adapter",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "facebook"
     *          ),
     *          @SWG\Parameter(
     *              name="facebookId",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          ),
     *          @SWG\Parameter(
     *              name="facebookToken",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          )
     *      )
     *   )
     *  
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
     *      summary="login action - Email Adapter",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="email",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *				description = "It's not required if adapter is not null"
     *          ),
     *          @SWG\Parameter(
     *              name="password",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *				description = "It's not required if adapter is not null"
     *          )
     *      )
     *   )
     *  
     *)
     */

     /**
     *
     * @SWG\Api(
     *   path="/auth/login",
     *    @SWG\Operation(
     *      nickname="login",
     *      method = "POST",
     *      summary="login action (Facebook adapter)",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="adapter",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "facebook"
     *          ),
     *          @SWG\Parameter(
     *              name="facebookId",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          ),
     *          @SWG\Parameter(
     *              name="facebookToken",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          )
     *      )
     *   )
     *  )
     *
     */

     /**
     *
     * @SWG\Api(
     *   path="/auth/login",
     *    @SWG\Operation(
     *      nickname="login",
     *      method = "POST",
     *      summary="login action (twitter adapter)",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="adapter",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "twitter"
     *          ),
     *          @SWG\Parameter(
     *              name="twitterToken",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          ),
     *          @SWG\Parameter(
     *              name="twitterSecret",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          ),
     *          @SWG\Parameter(
     *              name="twitterName",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          )
     *      )
     *   )
     *  )
     *
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
     */
	public function logoutAction() {
		$adapter = $this->loadAdapter();

		$user = $adapter->logout();
		return new JsonModel(array("message" => "Logout completed."));
	}

	/**
     *
     * @SWG\Api(
     *   path="/auth/merge",
     *    @SWG\Operation(
     *      nickname="merge",
     *      method = "POST",
     *      summary="merge action",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="adapter",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          ),
     *          @SWG\Parameter(
     *              name="facebookId",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *              description = "It's required if adapter = 'facebook'"
     *          ),
     *          @SWG\Parameter(
     *              name="facebookToken",
     *              paramType="form",
     *              type="string",
     *              required=false,
     *              description = "It's required if adapter = 'facebook'"
     *          )
     *   )
     *  )
     *)
     */

              /**
     *
     * @SWG\Api(
     *   path="/auth/merge",
     *    @SWG\Operation(
     *      nickname="merge_twitter",
     *      method = "POST",
     *      summary="merge - Twitter",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="adapter",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "twitter"
     *          ),
     *          @SWG\Parameter(
     *              name="twitterName",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          ),
     *          @SWG\Parameter(
     *              name="twitterToken",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          ),
     *          @SWG\Parameter(
     *              name="twitterSecret",
     *              paramType="form",
     *              type="string",
     *              required=true
     *          )
     *      )
     *   )
     *  )
     */
	public function mergeAction(){
          $data = $this->getRequest()->getPost();
          
          $adapter = $this->loadAdapter();
          $adapter->merge($data);

          return new JsonModel(array("message" => "User account merged"));
	}

     /**
     *
     * @SWG\Api(
     *   path="/auth/unmerge",
     *    @SWG\Operation(
     *      nickname="unmerge",
     *      method = "POST",
     *      summary="unmerge action - Facebook",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="adapter",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "facebook"
     *          ),
     *          @SWG\Parameter(
     *              name="facebookToken",
     *              paramType="form",
     *              type="string",
     *              required=false
     *          )
     *   )
     *  )
     *)
     */

     /**
     * @SWG\Api(
     *   path="/auth/unmerge",
     *    @SWG\Operation(
     *      nickname="unmerge",
     *      method = "POST",
     *      summary="unmerge action - Twitter",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="adapter",
     *              paramType="form",
     *              type="string",
     *              required=true,
     *              defaultValue = "twitter"
     *          )
     *  )
     *))
     */
     public function unmergeAction(){
          $data = $this->getRequest()->getPost();
          $adapter = $this->loadAdapter();
          $adapter->unmerge($data);

          return new JsonModel(array("message" => "User account unmerged"));  
     }
}