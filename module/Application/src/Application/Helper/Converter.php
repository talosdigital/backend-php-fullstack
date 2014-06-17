<?php

namespace Application\Helper;

class Converter {

	public function requestToJson($request){
        if (!empty($request)) {
           $dataString = explode('&', $request);
           $response = array();
           foreach ($dataString as $data) {
           		$keyValue = explode('=', $data);
           		$response = array_merge($response, array(
           			$keyValue[0] => str_replace('%20', ' ', $keyValue[1])
           			));
           }
            if (!empty($response)) {
                return $response;
            }
        }
        return false;
	}
}