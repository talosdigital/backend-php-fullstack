<?php

namespace User\Controller;

use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Http\PhpEnvironment\Request;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;
use ZfcUser\Service\User as UserService;
use ZfcUser\Options\UserControllerOptionsInterface;
use ZfcUser\Form\Register;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;
use ZfcUser\Mapper\UserInterface as UserMapperInterface;
use User\Helper\Errors;
use User\Entity\User as User;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;
die("as");
class UserController extends AbstractRestfulController
{
    const ROUTE_CHANGEPASSWD = 'zfcuser/changepassword';
    const ROUTE_LOGIN        = 'zfcuser/login';
    const ROUTE_REGISTER     = 'zfcuser/register';
    const ROUTE_CHANGEEMAIL  = 'zfcuser/changeemail';

    const CONTROLLER_NAME    = 'zfcuser';

    /**
     * @var string
     */
    protected  $fbToken = '324410064378636';

    /**
     * @var string
     */
    protected  $fbSecret = 'c4f122cd43915686cca6c7c4b1eaef6e';

    /**
     * @var string
     */
    protected $document = "User\Entity\User";
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var Form
     */
    protected $loginForm;

    /**
     * @var Form
     */
    protected $registerForm;

    /**
     * @var Form
     */
    protected $changePasswordForm;

    /**
     * @var Form
     */
    protected $changeEmailForm;

    /**
     * @todo Make this dynamic / translation-friendly
     * @var string
     */
    protected $failedLoginMessage = 'Authentication failed. Please try again.';

    /**
     * @var UserControllerOptionsInterface
     */
    protected $options;

    /**
    * Initialize facebook API params
    */

    function __construct(){
        FacebookSession::setDefaultApplication($this->fbToken, $this->fbSecret);
    }
	
    /**
     * Some actions over the User API
     */
    public function indexAction()
    {   
        $service = $this->getUserService();
        $method = $this->getRequest()->getMethod();
        switch ($method) {
            /**
            *   Sign up API
            */
            case "POST":  
            /**
            * Change Password API
            */
            case 'PUT':
                $user = $this->zfcUserAuthentication()->getIdentity();
                $request = $this->getPostData();
                
                $form = $this->getChangePasswordForm();
                $prg = array(
                    "identity" => $user->getEmail(),
                    "credential" => $request->{'oldPassword'},
                    "newCredential" => $request->{'newPassword'},
                    "newCredentialVerify" => $request->{'newPassword'},
                    "submit" => "Submit"
                    );

                $form->setData($prg);

                if (!$form->isValid()) {
                    header('HTTP/1.0 501 Password incorrect');
                    exit();
                }

                if (!$this->getUserService()->changePassword($form->getData())) {
                    header('HTTP/1.0 501 Password incorrect');
                    exit();
                }

                header('HTTP/1.0 200 Password changed');
                exit();
                break;
        }
        return new JsonModel(array('status' => true));
    }

    /*
    * Login, Logout and session verification
    */
    public function sessionAction()
    {
        $service = $this->getUserService();
        $method = $this->getRequest()->getMethod();
        switch ($method) {
            case 'POST':
                $request = $this->getPostData();
                //This is for facebook login
                if($request->{'facebook'}){
                    $facebookUser = $this->getFacebookUser($request->{'password'});
                    $user = $this->getUserByEmail($facebookUser['email']);
                    if(empty($user)){
                        $user = new User();
                        $user->setEmail($facebookUser['email']);
                        $user->setName($facebookUser['name']);
                        $user->setFacebook($facebookUser['facebook']);
                        $user->setPassword(md5(uniqid()));
                        $user->setRole('user');
                        $service->getUserMapper()->insert($user);

                        $response = $this->getUserArray($user);
                        
                        $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
                        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();
                        $this->zfcUserAuthentication()->getAuthService()->getStorage()->write($user);

                        return new JsonModel($response);
                    }
                    else{
                    	$result = $user->getFacebook();
                        if(empty($result)) { 
                            $this->mergeFacebook($user, $request->{'password'});
						}
                        $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
                        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();
                        $this->zfcUserAuthentication()->getAuthService()->getStorage()->write($user);
                        
                        return new JsonModel($this->getUserArray($user));
                   }

                }
                
                break;

            case 'GET':
                $user = $this->zfcUserAuthentication()->getIdentity();
                if(!empty($user)){
                    $responseArray = $this->getUserArray($user);
                    echo json_encode($responseArray);
                    header('HTTP/1.0 200 Success');
                    exit();
                }
                else{
                    echo new Errors('user not found');
                    header('HTTP/1.0 401');
                    exit();
                }
                break;
        }

    }

    /**
    * Checks if the user has a merged facebook account
    */
    public function checkAction(){
        $user = $this->zfcUserAuthentication()->getIdentity();
		$result = $user->getFacebook(); 
        if(empty($result)){
            echo json_encode(array('valid' => false));
            header('HTTP/1.0 200');
            exit();
        }
        else{
            echo json_encode(array('valid' => true));
            header('HTTP/1.0 200');
            exit();
        }
    }

    /**
    * Merge and Unmerge actions API
    */
    public function mergeAction(){
        $method = $this->getRequest()->getMethod();
        $user = $this->zfcUserAuthentication()->getIdentity();
        switch ($method) {
            case 'DELETE':
                $this->mergeFacebook($user, null);
                header('HTTP/1.0 200');
                exit();
                break;
            case 'PUT':
                $request = $this->getPostData();
                $token = $request->{'authResponse'}->{'accessToken'};
                
                $facebookUser = $this->getFacebookUser($request->{'password'});
                $userFB = $this->getUserByEmail($facebookUser['email']);
                
                $user = $this->getActiveUser();
				$result = $userFB->getEmail();
                if(empty($result)){
                    $this->mergeFacebook($user, $token);
                    header('HTTP/1.0 200');
                }
                else{
                    if($userFB->getEmail()!=$user->getEmail()){
                        echo new Errors('facebook email');
                        header('HTTP/1.0 500');
                    }
                    else{
                        $this->mergeFacebook($user, $token);
                        header('HTTP/1.0 200');
                    }
                }
                exit();
                break;
        }
    }

    /**
     * Getters/setters for DI stuff
     */



    public function getChangePasswordForm()
    {
        if (!$this->changePasswordForm) {
            $this->setChangePasswordForm($this->getServiceLocator()->get('zfcuser_change_password_form'));
        }
        return $this->changePasswordForm;
    }

    public function setChangePasswordForm(Form $changePasswordForm)
    {
        $this->changePasswordForm = $changePasswordForm;
        return $this;
    }

    /**
     * set options
     *
     * @param UserControllerOptionsInterface $options
     * @return UserController
     */
    public function setOptions(UserControllerOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * get options
     *
     * @return UserControllerOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options instanceof UserControllerOptionsInterface) {
            $this->setOptions($this->getServiceLocator()->get('zfcuser_module_options'));
        }
        return $this->options;
    }

    /**
     * Get changeEmailForm.
     *
     * @return changeEmailForm.
     */
    public function getChangeEmailForm()
    {
        if (!$this->changeEmailForm) {
            $this->setChangeEmailForm($this->getServiceLocator()->get('zfcuser_change_email_form'));
        }
        return $this->changeEmailForm;
    }

    /**
     * Set changeEmailForm.
     *
     * @param changeEmailForm the value to set.
     */
    public function setChangeEmailForm($changeEmailForm)
    {
        $this->changeEmailForm = $changeEmailForm;
        return $this;
    }

    public function getPostData(){
        return json_decode(file_get_contents("php://input"));
    }

    public function postToArray($post){
        return array('name' => $post->{'name'}, 'role' => 'user', 'email' => $post->{'email'}, 'provider' => 'local');
    }

    private function getUserByEmail($email){
        $dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $query = $dm->getRepository($this->document);
        $userDocument = $query->findBy(array('email' => $email));
        if(!empty($userDocument))
            return $userDocument[0];
        else
            return null;
    }

    private function getUserArray($user){
        $responseArray = array(
                'name' => $user->getName(),
                'role' => $user->getRole(),
                'email' => $user->getEmail(),
                'provider' => 'local'
                );
        return $responseArray;
    }

    private function getFacebookUser($token){
        $session = new FacebookSession($token);
        $request = new FacebookRequest($session, 'GET', '/me');
        $response = $request->execute();
        $graph = $response->getGraphObject(GraphUser::className());
		$facebookArray = $graph->asArray();
        $userRequest = array('name' => $graph->getName(), 'email' => $facebookArray['email'], 'facebook' => $token);
        return $userRequest;
    }

    private function mergeFacebook($user, $facebook){
        $user->setFacebook($facebook);
        $service->getUserMapper()->update($user);
    }
}