<?php

namespace myUser\Controller;

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
use myUser\Helper\Errors;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;

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
    protected $document = "myUser\Entity\User";
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
        /**
        * This is the Facebook SDK Token
        */
        FacebookSession::setDefaultApplication($this->fbToken, $this->fbSecret);
    }

    /**
     * Some actions over the User API
     */
    public function indexAction()
    {   
        $method = $this->getRequest()->getMethod();
        switch ($method) {
            /**
            *   Sign up API
            */
            case "POST":  
                $service = $this->getUserService();
                $request = $this->getPostData();
                /*
                *   request is a JSON object and it's structured 
                    {
                     name: 'user_name', 
                     email: 'user_email',
                     password: 'user_password',
                     role: 'role'
                     }
                */
                $post = array(
                        "email" => $request->{'email'},
                        "password" => $request->{'password'},
                        "passwordVerify" => $request->{'password'},
                        "submit" => ""
                        );
                $user = $service->register($post);
                if(empty($user)){
                    echo new Errors('repeated email');
                    header('HTTP/1.1 400 Incorrect Email', true, 400);
                    exit();
                }
                else{
                    $user->setName($request->{'name'});
                    $user->setRole('user');
                    $service->getUserMapper()->update($user);
                }
                return new JsonModel($this->postToArray($request));
                break;
        }
        return new JsonModel(array('status' => true));
    }

    /*
    * Login, Logout and session verification
    */
    public function sessionAction(){
        $method = $this->getRequest()->getMethod();
        switch ($method) {
            /*
            * Login API
            */
            case 'POST':
                $request = $this->getPostData();
                /*
                *   request is a JSON object and it's structured 
                    {                
                     email: 'user_email',
                     password: 'user_password',
                     facebook: boolean
                     }
                */
                $params = new Parameters();
                $params->set('identity', $request->{'email'});
                $params->set('credential', $request->{'password'});
                $params->set('submit', '');

                $form = $this->getLoginForm();
                $form->setData($params);

                if (!$form->isValid()) {
                    echo new Errors('wrong credentials');
                    header('HTTP/1.0 401 Not Found');
                    exit();
                }
                else{
                    $user = $this->getUserByEmail($request->{'email'});
                }

                $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
                $this->zfcUserAuthentication()->getAuthService()->clearIdentity();
                $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate'));
                
                $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
                
                $emulateRequest = new Request();
                $emulateRequest->setPost($params);

                $result = $adapter->prepareForAuthentication($emulateRequest);
                $adapter->prepareForAuthentication($this->getRequest());
                $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);
                
                if (!$auth->isValid()) {
                    echo new Errors('wrong credentials');
                    header('HTTP/1.0 401 Not Found');
                    exit();
                }
                $user = $this->zfcUserAuthentication()->getIdentity();
                $responseArray = array(
                    'name' => $user->getName(),
                    'role' => $user->getRole(),
                    'email' => $user->getEmail(),
                    'provider' => 'local'
                    );
                return new JsonModel($responseArray);
                die();
                break;
        }
    }

    /**
     * Login form
     */
    public function loginAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $request = $this->getRequest();
        $form    = $this->getLoginForm();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        if (!$request->isPost()) {
            return array(
                'loginForm' => $form,
                'redirect'  => $redirect,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
            );
        }

        $form->setData($request->getPost());

        if (!$form->isValid()) {
            $this->flashMessenger()->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
            return $this->redirect()->toUrl($this->url()->fromRoute(static::ROUTE_LOGIN).($redirect ? '?redirect='. rawurlencode($redirect) : ''));
        }

        // clear adapters
        $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();

        return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate'));
    }

    /**
     * Logout and clear the identity
     */
    public function logoutAction()
    {
        $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->zfcUserAuthentication()->getAuthAdapter()->logoutAdapters();
        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();

        $redirect = $this->params()->fromPost('redirect', $this->params()->fromQuery('redirect', false));

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        return $this->redirect()->toRoute($this->getOptions()->getLogoutRedirectRoute());
    }

    /**
     * General-purpose authentication action
     */
    public function authenticateAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        $redirect = $this->params()->fromPost('redirect', $this->params()->fromQuery('redirect', false));

        $result = $adapter->prepareForAuthentication($this->getRequest());

        // Return early if an adapter returned a response
        if ($result instanceof Response) {
            return $result;
        }

        $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);

        if (!$auth->isValid()) {
            $this->flashMessenger()->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
            $adapter->resetAdapters();
            return $this->redirect()->toUrl(
                $this->url()->fromRoute(static::ROUTE_LOGIN) .
                ($redirect ? '?redirect='. rawurlencode($redirect) : '')
            );
        }

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
    }

    /**
     * Register new user
     */
    public function registerAction()
    {
        // if the user is logged in, we don't need to register
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }
        // if registration is disabled
        if (!$this->getOptions()->getEnableRegistration()) {
            return array('enableRegistration' => false);
        }

        $request = $this->getRequest();
        $service = $this->getUserService();
        $form = $this->getRegisterForm();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        $redirectUrl = $this->url()->fromRoute(static::ROUTE_REGISTER)
            . ($redirect ? '?redirect=' . rawurlencode($redirect) : '');
        $prg = $this->prg($redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'registerForm' => $form,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                'redirect' => $redirect,
            );
        }

        $post = $prg;
        $user = $service->register($post);

        $redirect = isset($prg['redirect']) ? $prg['redirect'] : null;

        if (!$user) {
            return array(
                'registerForm' => $form,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                'redirect' => $redirect,
            );
        }

        if ($service->getOptions()->getLoginAfterRegistration()) {
            $identityFields = $service->getOptions()->getAuthIdentityFields();
            if (in_array('email', $identityFields)) {
                $post['identity'] = $user->getEmail();
            } elseif (in_array('username', $identityFields)) {
                $post['identity'] = $user->getUsername();
            }
            $post['credential'] = $post['password'];
            $request->setPost(new Parameters($post));
            return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate'));
        }

        // TODO: Add the redirect parameter here...
        return $this->redirect()->toUrl($this->url()->fromRoute(static::ROUTE_LOGIN) . ($redirect ? '?redirect='. rawurlencode($redirect) : ''));
    }

    /**
     * Change the users password
     */
    public function changepasswordAction()
    {
        // if the user isn't logged in, we can't change password
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $form = $this->getChangePasswordForm();
        $prg = $this->prg(static::ROUTE_CHANGEPASSWD);

        $fm = $this->flashMessenger()->setNamespace('change-password')->getMessages();
        if (isset($fm[0])) {
            $status = $fm[0];
        } else {
            $status = null;
        }

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'status' => $status,
                'changePasswordForm' => $form,
            );
        }

        $form->setData($prg);

        if (!$form->isValid()) {
            return array(
                'status' => false,
                'changePasswordForm' => $form,
            );
        }

        if (!$this->getUserService()->changePassword($form->getData())) {
            return array(
                'status' => false,
                'changePasswordForm' => $form,
            );
        }

        $this->flashMessenger()->setNamespace('change-password')->addMessage(true);
        return $this->redirect()->toRoute(static::ROUTE_CHANGEPASSWD);
    }

    public function changeEmailAction()
    {
        // if the user isn't logged in, we can't change email
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $form = $this->getChangeEmailForm();
        $request = $this->getRequest();
        $request->getPost()->set('identity', $this->getUserService()->getAuthService()->getIdentity()->getEmail());

        $fm = $this->flashMessenger()->setNamespace('change-email')->getMessages();
        if (isset($fm[0])) {
            $status = $fm[0];
        } else {
            $status = null;
        }

        $prg = $this->prg(static::ROUTE_CHANGEEMAIL);
        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'status' => $status,
                'changeEmailForm' => $form,
            );
        }

        $form->setData($prg);

        if (!$form->isValid()) {
            return array(
                'status' => false,
                'changeEmailForm' => $form,
            );
        }

        $change = $this->getUserService()->changeEmail($prg);

        if (!$change) {
            $this->flashMessenger()->setNamespace('change-email')->addMessage(false);
            return array(
                'status' => false,
                'changeEmailForm' => $form,
            );
        }

        $this->flashMessenger()->setNamespace('change-email')->addMessage(true);
        return $this->redirect()->toRoute(static::ROUTE_CHANGEEMAIL);
    }

    /**
     * Getters/setters for DI stuff
     */

    public function getUserService()
    {
        if (!$this->userService) {
            $this->userService = $this->getServiceLocator()->get('zfcuser_user_service');
        }
        return $this->userService;
    }

    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
        return $this;
    }

    public function getRegisterForm()
    {
        if (!$this->registerForm) {
            $this->setRegisterForm($this->getServiceLocator()->get('zfcuser_register_form'));
        }
        return $this->registerForm;
    }

    public function setRegisterForm(Form $registerForm)
    {
        $this->registerForm = $registerForm;
    }

    public function getLoginForm()
    {
        if (!$this->loginForm) {
            $this->setLoginForm($this->getServiceLocator()->get('zfcuser_login_form'));
        }
        return $this->loginForm;
    }

    public function setLoginForm(Form $loginForm)
    {
        $this->loginForm = $loginForm;
        $fm = $this->flashMessenger()->setNamespace('zfcuser-login-form')->getMessages();
        if (isset($fm[0])) {
            $this->loginForm->setMessages(
                array('identity' => array($fm[0]))
            );
        }
        return $this;
    }

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
}