<?php
namespace Utility;

class Security {

    private $symmetric_key;
    private $algorithm;

    public static function buildFromConfig() {
        $cfgObj = cfg('system');

	$ret = new Security();
	if (isset($cfgObj['symmetric_key']) && !empty($cfgObj['symmetric_key'])) {
            $ret->setSymmetricKey($cfgObj['symmetric_key']);
        }
	if (isset($cfgObj['algorithm']) && !empty($cfgObj['algorithm'])) {
            $ret->setAlgorithm($cfgObj['algorithm']);
        }

	return $ret;
    }

    public function __construct($algorithm = "aes-256-cbc", $symmetric_key = "12345678") {
	$this->symmetric_key = $symmetric_key;
	$this->algorithm = $algorithm;
    }

    public function setAlgorithm($algorithm) { $this->algorithm = $algorithm; }
    public function setSymmetricKey($symmetric_key) { $this->symmetric_key = $symmetric_key; }

    public function encrypt($payload)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->algorithm));
        $encrypted = openssl_encrypt($payload, $this->algorithm, $this->symmetric_key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
    
    public function decrypt($payload)
    {
        list($encrypted_data, $iv) = explode('::', base64_decode($payload), 2);
        return openssl_decrypt($encrypted_data, $this->algorithm, $this->symmetric_key, 0, $iv);
    }

}