<?php

namespace User\Auth;

class FacebookAdapter extends AbstractAdapter implements IAdapter {
		
	public function signup($request) {
	}
	
	public function login($request) {
		
	}
	
	public function logout() {
		parent::logout();
	}


}
