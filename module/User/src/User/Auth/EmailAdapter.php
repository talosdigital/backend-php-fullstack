<?php

namespace User\Auth;

use User\Entity\User;
use Zend\Http\PhpEnvironment\Request;
use Zend\Stdlib\Parameters;

class EmailAdapter extends AbstractAdapter implements IAdapter{
		
	public function signup($request) {
        $service = $this->getServiceLocator()->get('zfcuser_user_service');
        $post = array(
                "email" => $request->get('email'),
                "password" => $request->get('password'),
                "passwordVerify" => $request->get('password')
		);
		$form = $this->getRegisterForm();
		

        $user  = new User();
        $form  = $this->getRegisterForm();
        $form->setHydrator($this->getFormHydrator());
        $form->bind($user);
        $form->setData($post);
		if(! $form->isValid()) {
			$errors = $form->getMessages();
			if(isset($errors['email']['recordFound'])) {
				throw new \Exception("This email is already taken.", \User\Module::ERROR_DUPLICATED_EMAIL);	
			}
			else {
				throw new \Exception(json_encode($errors), \User\Module::ERROR_UNEXPECTED);	
			}
		}
        $user = $service->register($post);
        if($user){
			$user->setName($request->get('name'));
            $user->setRole('user');
            $service->getUserMapper()->update($user);
			return $user;
        }
		else {
			throw new \Exception(json_encode($errors), \User\Module::ERROR_UNEXPECTED);	
		}
	}
	
	public function login($request) {

		$adapter = $this->getAuthPlugin()->getAuthAdapter();

		$params = new Parameters();
		$params->set('identity', $request->get('email'));
		$params->set('credential', $request->get('password'));
		$emulateRequest = new Request();
		$emulateRequest->setPost($params);
		
        $result = $adapter->prepareForAuthentication($emulateRequest);
        if ($result instanceof Response) {
            return $result;
        }

        $auth = $this->getAuthPlugin()->getAuthService()->authenticate($adapter);
        if(! $auth->isValid()) {
        	$result = $auth->getMessages();
			$message = "Bad request.";
			$errorCode = \User\Module::ERROR_UNEXPECTED;
			if(isset($result[0])) {
				$message = $result[0];
				$errorCode = \User\Module::ERROR_LOGIN_FAILED;
			}
        	throw new \Exception($message, \User\Module::ERROR_LOGIN_FAILED);
        }
        $user = $this->getAuthPlugin()->getIdentity();
		
		return $user;
	}

	public function changePassword($request, $user){
        $form = $this->getChangePasswordForm();
        $prg = array(
            "identity" => $user->getEmail(),
            "credential" => $request->get('currentPassword'),
            "newCredential" => $request->get('newPassword'),
            "newCredentialVerify" => $request->get('newPasswordVerify'),
            "submit" => "Submit"
            );

        $form->setData($prg);

        if (!$form->isValid()) {
        	$errors = $form->getMessages();
            throw new \Exception(json_encode($errors), \User\Module::ERROR_CHANGE_PASSWORD_FAILED);	
        }

        if (!$this->getUserService()->changePassword($form->getData())) {
            $errors = $form->getMessages();
            throw new \Exception(json_encode($errors), \User\Module::ERROR_CHANGE_PASSWORD_FAILED);	
        }

        return true;
	}

	public function changeEmail($request, $user){
        $form = $this->getChangeEmailForm();
        $request->set('identity', $user->getEmail());
        $prg = array(
        	"identity" => $user->getEmail(),
        	"newIdentity" => $request->get('email'),
        	"newIdentityVerify" => $request->get('email'),
        	"credential" => $request->get('password')
        	);

        $form->setData($prg);

        if (!$form->isValid()) {
			$errors = $form->getMessages();
            throw new \Exception(json_encode($errors), \User\Module::ERROR_CHANGE_EMAIL_FAILED);
        }

        $change = $this->getUserService()->changeEmail($prg);

        if (!$change) {
            $errors = $form->getMessages();
            throw new \Exception(json_encode($errors), \User\Module::ERROR_CHANGE_EMAIL_FAILED);
        }

      	return true;
	}
	
	public function logout() {
		parent::logout();
	}
	
    private function getRegisterForm() {
		return $this->getServiceLocator()->get('zfcuser_register_form');
    }

    private function getFormHydrator(){
		return ($this->getServiceLocator()->get('zfcuser_register_form_hydrator'));
    }
	
	private function getLoginForm() {
		return $this->getServiceLocator()->get('zfcuser_login_form');
	}
	
	private function getChangePasswordForm(){
        return $this->getServiceLocator()->get('zfcuser_change_password_form');
    }

    public function getChangeEmailForm(){
        return $this->getServiceLocator()->get('zfcuser_change_email_form');
    }
}
