<?php
require_once('Form.class.php');

class QRCode {
	
	function __construct() {
	}
	
	public function decode($imageUrl) {
		//file_get_contents('http://zxing.org/w/decode?u=' + $imageUrl);
		
		$form = new Form();
		$form->addField('f', $imageUrl, true, 'image/jpeg');
		$result = $form->post('http://zxing.org/w/decode');
		
		if($result->header['status'] == 200) {
			if(preg_match('#' . preg_quote('<td>Raw text</td><td><pre style="margin:0">') . '(.*)' . preg_quote('</pre></td>') . '#isU', $result->content, $matches)) {
				return $matches[1];
			} else {
				return $this->decode2($imageUrl);
			}
		} else {
			return $this->decode2($imageUrl);
		}
	}
	
	public function decode2($imageUrl) {
		$form = new Form();
		$form->addField('name', basename($imageUrl));
		$form->addField('file', $imageUrl, true, 'image/jpeg');
		$result = $form->post('http://www.esponce.com/qr-code-decoding');
		
		$json = json_decode($result->content);
		
		if($json->success) {
			return $json->rawMessage;
		} else {
			return null;
		}
	}
}


$QRCode = new QRCode();
//echo $QRCode->decode2('test/test5.jpg'); //http://blog.webometrics.org.uk

?>