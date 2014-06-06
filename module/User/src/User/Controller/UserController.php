<?php


namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use User\Document\User;
use Zend\Http\Request;
use Zend\Session\Container;
use User\Helper\Errors;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;


class UserController extends AbstractRestfulController{
    protected $document = "User\Document\User";
    protected $password;
    protected $facebook;
    protected $dm;

    function __construct(){
        FacebookSession::setDefaultApplication('324410064378636', 'c4f122cd43915686cca6c7c4b1eaef6e');
    }

    //Main actions of the API

    public function indexAction() {
        $method = $this->getRequest()->getMethod();
        switch ($method) {
            case 'POST':
                $userRequest = $this->getPostData();
                $userDocument = $this->getUserByEmail($userRequest->{'email'});
                if(empty($userDocument)){
                    return new JsonModel($this->saveUser($userRequest));
                }
                else{
                    echo new Errors('repeated email');
                    header('HTTP/1.1 400 Incorrect Email', true, 400);
                    exit();
                }
                break;
            case 'PUT':
                $userRequest = $this->getPostData();
                $userDocument = $this->getUserByEmail($this->getSession()->email);
                if($userDocument->getPassword()==md5($userRequest->{'oldPassword'})){
                    $this->changePassword($userDocument, $userRequest->{'newPassword'});
                    header('HTTP/1.0 200 Password changed');
                    exit();
                }
                else{
                    header('HTTP/1.0 501 Password incorrect');
                    exit();
                }
                break;
            }
    }

    //This for the login, logout, and verification
    public function sessionAction(){
        $method = $this->getRequest()->getMethod();
        switch ($method) {
            case 'POST':
                $request = $this->getPostData();
                //This is for facebook login
                if($request->{'facebook'}){
                    $userDocument = $this->getFacebookUser($request->{'password'});
                    if(empty($userDocument)){
                        $userRequest['password'] = uniqid();
                        $userRequest = $this->toJsonArray($userRequest);
                        $response = $this->saveUser($userRequest);
                        $this->mergeFacebook($userDocument, $request->{'password'});
                        return new JsonModel($response);
                    }
                    else{
                        if(empty($userDocument->getFacebook()))
                            $this->mergeFacebook($userDocument, $request->{'password'});
                        return new JsonModel($this->getUserArray($userDocument));
                    }
                }
                else{
                    $userDocument = $this->getUserByEmail($request->{'email'});
                    //If the email is not registered
                    if(empty($userDocument)){
                        echo new Errors('not email');
                        header('HTTP/1.0 401 Not Found');
                        exit();
                    }
                    $response = $this->getUserArray($userDocument);
                    //Here we check if the password is correct
                    if($userDocument->getPassword()==md5($request->{'password'}))
                        return new JsonModel($response);
                    else{
                    //send an error message
                        echo new Errors('wrong password');
                        header('HTTP/1.0 401 Not Found');
                        exit();
                    }
                }
                break;

            case "DELETE":
                $this->destroySession();
                header('HTTP/1.0 200');
                exit();
                break;

            case "GET":
                $userSession = $this->getActiveUser();
                if(!empty($userSession)){
                    return new JsonModel($this->getUserArray($userSession));
                }
                else{
                    echo new Errors('user not found');
                    header('HTTP/1.0 401');
                    exit();
                }
                break;

            default:
                header('HTTP/1.0 401');
                exit();
                break;
        }
    }

    public function checkAction(){
        $userDocument = $this->getActiveUser();
        if(empty($userDocument->getFacebook())){
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

    public function mergeAction(){
        $method = $this->getRequest()->getMethod();
        switch ($method) {
            case 'DELETE':
                $this->mergeFacebook($this->getActiveUser(), null);
                header('HTTP/1.0 200');
                exit();
                break;
            case 'PUT':
                $request = $this->getPostData();
                $token = $request->{'authResponse'}->{'accessToken'};
                $userDocument = $this->getFacebookUser($token);
                $activeUser = $this->getActiveUser();
                if(empty($userDocument->getEmail())){
                    $this->mergeFacebook($activeUser, $token);
                    header('HTTP/1.0 200');
                }
                else{
                    if($userDocument->getEmail()!=$activeUser->getEmail()){
                        echo new Errors('facebook email');
                        header('HTTP/1.0 500');
                    }
                    else{
                        $this->mergeFacebook($activeUser, $token);
                        header('HTTP/1.0 200');
                    }
                }
                exit();
                break;

        }
    }


    //These are the complementary functions
    public function getPostData(){
        return json_decode(file_get_contents("php://input"));
    }

    private function saveSession($user){
        $userSession = new Container('user');
        $userSession->name = $user['name'];
        $userSession->role = $user['role'];
        $userSession->email = $user['email'];
    }

    private function getSession(){
        $userSession = new Container('user');
        return $userSession;
    }

    private function getActiveUser(){
        $userSession = $this->getSession();
        $userDocument = $this->getUserByEmail($userSession->email);
        return $userDocument;
    }

    private function destroySession(){
        $userSession = new Container('user');
        $userSession->getManager()->getStorage()->clear('user');
    }

    private function getUserByEmail($email){
        $this->dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $query = $this->dm->getRepository($this->document);
        $userDocument = $query->findBy(array('email' => $email));
        if(!empty($userDocument))
            return $userDocument[0];
        else
            return null;
    }

    private function getUserArray($userDocument){
        if(!empty($userDocument)){
            $response = array('name' => $userDocument->getName(),
                            'role' => $userDocument->getRole(),
                            'email' => $userDocument->getEmail(),
                            'provider' => 'local');
            $this->saveSession($response);
            return $response;
        }
    }

    public function verifyAction(){
        $userSession = new Container('user');
        $response = array('name' => $userSession->name,
                        'role' => $userSession->role,
                        'email' => $userSession->email,
                        'provider' => 'local');
        return new JsonModel($response);
    }

    private function toJsonArray($array){
        return json_decode(json_encode($array));
    }

    private function saveUser($userRequest){
        $this->dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $user = new User();
        $user->setName($userRequest->{'name'});
        $user->setEmail($userRequest->{'email'});
        $user->setPassword($userRequest->{'password'});
        $user->setFacebook($userRequest->{'facebook'});
        $user->setRole('user');
        $this->dm->persist($user);
        $this->dm->flush();
        $user_array = array('name' => $user->getName(), 'role' => $user->getRole(), 'email' => $user->getEmail(), 'provider' => 'local');
        $this->saveSession($user_array);
        return $user_array;
    }

    private function changePassword($userDocument, $newPassword){
        $this->dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $userDocument->setPassword($newPassword);
        $this->dm->persist($userDocument);
        $this->dm->flush();
    }

    private function mergeFacebook($userDocument, $facebook){
        $this->dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $userDocument->setFacebook($facebook);
        $this->saveSession($this->getUserArray($userDocument));
        $this->dm->persist($userDocument);
        $this->dm->flush();
    }

    private function getFacebookUser($token){
        $session = new FacebookSession($token);
        $request = new FacebookRequest($session, 'GET', '/me');
        $response = $request->execute();
        $graph = $response->getGraphObject(GraphUser::className());
        $userRequest = array('name' => $graph->getName(), 'email' => $graph->asArray()['email'], 'facebook' => $token);
        $userDocument = $this->getUserByEmail($userRequest['email']);
        return $userDocument;
    }

}