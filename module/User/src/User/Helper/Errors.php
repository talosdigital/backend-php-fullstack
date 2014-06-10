<?php

namespace User\Helper;

class Errors{

    protected $errorResponse;
    protected $errorType;

    function __construct($errorType=null){
        $this->errorType = $errorType;
    }

    public function setError(){
        switch ($this->errorType) {
            case 'repeated email':
                $headMessage = "Email error";
                $message = "This email is already taken";
                $input = "email";
                $name = "Email error";
                $path = "email";
                $type = "Email is not unique";
                break;

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
                $message = "This is the default error message";
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