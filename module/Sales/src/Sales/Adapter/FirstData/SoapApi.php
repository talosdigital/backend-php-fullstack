<?php
namespace Sales\Adapter\FirstData;

class SoapApi extends \SoapClient {

	private $context;
	private $hmacKey;
	private $keyId;

	public function __construct($config) {
		$this->context = stream_context_create();
		$options['stream_context'] = $this->context;
		$this->hmackey = $config["hmac_key"];
		$this->keyId = $config["key_id"];
		parent::__construct($config["wsdl"], $options);
	}

	public function __doRequest($request, $location, $action, $version, $one_way = NULL) {
		$hashtime = date("c");
		$hashstr = "POST\ntext/xml; charset=utf-8\n" . sha1($request) . "\n" . $hashtime . "\n" . parse_url($location,PHP_URL_PATH);
		$authstr = base64_encode(hash_hmac("sha1",$hashstr,$this->hmacKey,TRUE));
		stream_context_set_option($this->context,array("http" => array("header" => "authorization: GGE4_API " . $this->keyId . ":" . $authstr . "\r\nx-gge4-date: " . $hashtime . "\r\nx-gge4-content-sha1: " . sha1($request))));
		return parent::__doRequest($request, $location, $action, $version, $one_way);
	}


}