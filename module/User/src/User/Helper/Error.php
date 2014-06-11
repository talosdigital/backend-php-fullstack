<?php

namespace User\Helper;

class Error {
	const REPEATED_EMAIL = 100;

    protected $errorResponse;
    protected $errorType;

    function __construct($errorType=null){
        $this->errorType = $errorType;
    }

    public function setError(){
        switch ($this->errorType) {

            case 'wrong password':
                $headMessage = "Password is incorrect";
                break;

            case 'wrong credentials':
                $headMessage = "Credentials are incorrect";
                break;

            case 'not email':
                $headMessage = "Email not registered";
                break;

            case 'facebook email':
                $headMessage = "Your facebook email is already registered";
                break;

            case 'user not found':
                $headMessage = "The current user was not found";
                break;

            default:
                $headMessage = "HTTP";
                $message = "";
                $input = "error";
                $name = "Default";
                $path = "default";
                $type = "default";
                break;
        }
        $this->errorResponse = array("message" => $headMessage,
            "name" => $name,
            "errors" => array(
                $input => array(
                    "message" => $message,
                    "name" => $name,
                    "path" => $path,
                    "type" => $type
                    )));
        return $this->errorResponse;
    }

    public function __toString(){
        $this->setError();
        return (string)json_encode($this->errorResponse);
    }
}