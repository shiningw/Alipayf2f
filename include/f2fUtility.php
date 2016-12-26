<?php


class f2fUtility {
	
	protected $charset = 'UTF-8';
	private $bizParas = array();
	private $apiParas = array();
	protected $gatewayUrl = "https://openapi.alipay.com/gateway.do";
	private $priKey = '';
	private $pubKey = '';

	
	static public function create(){
		
		  return new self();
	}
	
	public function setPrivateKey($key = ''){
		
		        $this->priKey = $key;
	}
	
	
	public function setPubKey($key = ''){
		
		        $this->pubKey = $key;
	}
	
	public function setBizContent($Paras = array()){

        if(!empty($Paras)){
            $this->bizParas['biz_content'] = json_encode($Paras,JSON_UNESCAPED_UNICODE);
        }else{
			
			$this->bizParas['biz_content'] = json_encode($this->bizParas,JSON_UNESCAPED_UNICODE);;
		}

        
	}
	
	public function getBizContent() {
		
		 return $this->bizParas['biz_content'];
	}
	
	protected function defaultApiParas(){
		
		     $this->apiParas = array(
						'version' => '1.0',
						'format' => 'json',
						'sign_type'  => 'RSA',
						'method' => 'alipay.trade.precreate',
						'timestamp' => date("Y-m-d H:i:s"),
						'auth_token' => '',
						'charset' => $this->charset,
						'terminal_type' => '',
						'terminal_info' => '',
						'prod_code' => '',
						'app_auth_token' => '',
				);
		return $this->apiParas;
	}
	

	public function getBizParas() {
		   
		   return $this->bizParas;
	}
	
    public function setBizParas($key,$value) {
		
		   $this->bizParas[$key] = $value;
	}
	
	
	public function setApiParas($key,$value){
		
		  $this->apiParas[$key] = $value;
	}
	
	public function getApiParas(){
		
		   $this->apiParas = $this->apiParas + $this->defaultApiParas();
		   
		   return $this->apiParas;
	}
	
	protected function getToBeSignedParas() {
			$this->setBizContent();

		 return array_merge($this->getApiParas(),array('biz_content' => $this->getBizContent()));

		
	}
	
	public function getUrl(){
		
		  $this->apiParas['sign'] = $this->sign($this->getToBeSignedParas());
		  
          $requestUrl = $this->gatewayUrl . "?";
		  $requestUrl .= $this->urlEncode($this->apiParas);
		  
		  return $requestUrl;
	
	}
	
   public function getPostData(){
	    	return $this->urlEncode(array('biz_content' => $this->getBizContent()));

   }
	
   public function httpHeaders(){
	   
	   return array(
			'content-type' => 'application/x-www-form-urlencoded',
			'charset' => $this->charset,
      
	   );
	   
   }
	public function checkEmpty($value) {
			if (!isset($value))
				return true;
			if ($value === null)
				return true;
			if (trim($value) === "")
				return true;

			return false;
	}

	public function characet($data, $targetCharset = 'UTF-8') {


			if (!empty($data)) {
			
				if (strcasecmp($this->charset, $targetCharset) != 0) {

					$data = mb_convert_encoding($data, $targetCharset);
				}
			}


			return $data;
	}

	protected function sign($paras = array(),$type = 'RSA') {
		
		return $this->rsaSign($this->getSignContent($paras),$this->priKey,$type);
	  
	}
	
	public function rsaSign($data = '',$rsakey, $sign_type = 'RSA') {
		
			try{
				
				$res = $this->keyWrap($rsakey);
			}catch (Exception $e) {
				
				 echo 'exception: ',  $e->getMessage(), "\n";  
			}

			if ("RSA2" == $sign_type) {
				openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
			} else {
				openssl_sign($data, $sign, $res);
			}


			return base64_encode($sign);
	}
	
	
	protected function keyWrap($key = '', $type = 'private') {
		
		  if (empty($key)) {
			  
			  throw new Exception('Key is empty');
		  }
		
		   if($type == 'private') {
			   
			     $res = "-----BEGIN RSA PRIVATE KEY-----\n";
			     $res .= wordwrap($key, 64, "\n", true);
				 $res .= "\n-----END RSA PRIVATE KEY-----";
				
		   }else {
			   
			     $res = "-----BEGIN PUBLIC KEY-----\n";
			     $res .= wordwrap($key, 64, "\n", true);
				 $res .= "\n-----END PUBLIC KEY-----";
			     
		   }
		   
		   return $res;
		   
		   
	}
	
	
	protected function urlEncode($paras = array()) {
		
		
		$str = '';

        foreach($paras as $k => $v) {
			
			   $str .= "$k=" . urlencode($this->characet($v, $this->charset)) . "&";
		}
		  return substr($str,0,-1);

	}


	protected function getSignContent($params) {
			ksort($params);

			$stringToBeSigned = "";
			$i = 0;
			foreach ($params as $k => $v) {
				if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

					$v = $this->characet($v, $this->charset);

					if ($i == 0) {
						$stringToBeSigned .= "$k" . "=" . "$v";
					} else {
						$stringToBeSigned .= "&" . "$k" . "=" . "$v";
					}
					$i++;
				}
			}

			unset ($k, $v);
			return $stringToBeSigned;
	}
	
	public function verify($paras = array(),$notify_sign) {
  
      return (bool) $this->verifySign($this->getSignContent($paras),$notify_sign);
	}
	
	protected function verifySign($data, $sign, $signType = 'RSA') {

		$res = $this->keyWrap($this->pubKey,'public');
		
		if ("RSA2" == $signType) {
			$result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
		} else {
			$result = (bool) openssl_verify($data, base64_decode($sign), $res);
		}


		return $result;
	}
	
	public function curl($url, $postFields = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$postBodyString = "";
		$encodeArray = Array();
		$postMultipart = false;
         $postCharset = 'UTF-8';

		if (is_array($postFields) && 0 < count($postFields)) {

			foreach ($postFields as $k => $v) {
				if ("@" != substr($v, 0, 1)) 
				{

					$postBodyString .= "$k=" . urlencode(characet($v, $this->charset)) . "&";
					$encodeArray[$k] = $this->characet($v, $this->charset);
				} else 
				{
					$postMultipart = true;
					$encodeArray[$k] = new \CURLFile(substr($v, 1));
				}

			}
			
			unset ($k, $v);
			curl_setopt($ch, CURLOPT_POST, true);
			if ($postMultipart) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
			} else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
			}
		}

		if ($postMultipart) {

			$headers = array('content-type: multipart/form-data;charset=' . $this->charset);
		} else {

			$headers = array('content-type: application/x-www-form-urlencoded;charset=' . $this->charset);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);




		$reponse = curl_exec($ch);

		if (curl_errno($ch)) {

			throw new Exception(curl_error($ch), 0);
		} else {
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode) {
				throw new Exception($reponse, $httpStatusCode);
			}
		}

		curl_close($ch);
		return $reponse;
	}

}


