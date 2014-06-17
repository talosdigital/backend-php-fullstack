<?php

namespace User\Auth;

interface IAdapter {
	
	public function signup($request);
	
	public function login($request);
	
	public function logout();
}
