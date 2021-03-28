<?php
/* WARNING WARNING *
* THIS IS NOT A SECURE WAY OF PROTECTING DATA. DO NOT USE IT FOR YOUR OWN APPS, USE PROVEN ALTERNATIVES INSTEAD
* THE PURPOSE HERE IS TO HIDE PLAINTEXT ONLY FROM THE RANDOM PERSON WHO COULD LOOK AT YOUR DATABASE, OR AMATURE GUYS, NOTHING MORE.
*/

Class crypto {
	
	// the random key to use
	var $token;
	
	// the data to encrypt or decrypt
	var $data;
	
	// plaintext data
	var $plaintext;
	
	// where the key is stored
	private $data_path;

	// error flag, either true, false or latest error string
	var $error;
	
	// holds errors
	var $errors;
	
	// data type
	var $type;
	
	// data id
	var $id;
	
	 function __construct($token='',$type='',$id=0) {
		//reset errors
		$this->errors = array();
		$this->error = false;
		 
		//always provide a token
		if (!$token) {
			$this->token = $this->tokenRand();
		} else {
			$this->token = $token;
		}

		$this->data = '';
		$this->plaintext = '';
		$this->type = $type;
		$this->id = $id;
		
		$this->data_path = 'content/hdata/';
		
		if (count($this->errors)) $this->error = true;
		
	}
	
	public function construct_data_path() {
		if ($this->type == 'client') {
			$this->data_path .= 'cldata/'.$this->id;
		} elseif ($this->type == 'v') {
			$this->data_path .= 'vdata';
		} elseif ($this->type == 'case') {
			$this->data_path .= 'cdata/'.$this->id;
		} else {
			$this->errors[] = 'Type unknown';
		}
		
		if ($this->type && $this->type != 'v' && $this->id == 0) {
			$this->errors[] = 'Resource id cannot be zero';
		}
		
		if ($this->id && $this->type == '') {
			$this->errors[] = 'No type , but id provided.';
		}
		
		echo $this->data_path;
	}

	//custom encryption algorithm like vigerene, do not use this!
	private function str_hide($str,$key){
		if ($key == '') return $str;
		if ($str == '') return false;

		$key=str_replace(chr(32),'',$key);
		
		if(strlen($key) < 8) return false;
		
		//make it 32 chars len anyway
		$kl=strlen($key)<32?strlen($key):32;
		
		$k=array();
		for($i=0;$i<$kl;$i++) {
			$k[$i]=ord($key{$i})&0x1F;
		}
		
		$j=0;
		
		for($i=0;$i<strlen($str);$i++) {
			$e=ord($str{$i});
			$str{$i}=$e&0xE0?chr($e^$k[$j]):chr($e);
			$j++;
			$j=$j==$kl?0:$j;
		}
		
		return $str;
	}

	//RC4A encryption algorithm
	private function rc4a(){
		if ($this->plaintext) {
			$string = $this->plaintext;
		} elseif ($this->data) {
			$string = $this->data;
		} else {
			$this->errors[] = 'No data at all to process';
		}
		
		$key = $this->token;
		
		$s = array();
		for ($i = 0; $i < 256; $i++) {
			$s[$i] = $i;
		}
		$j = 0;
		for ($i = 0; $i < 256; $i++) {
			$j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
			$x = $s[$i];
			$s[$i] = $s[$j];
			$s[$j] = $x;
		}
		$i = 0;
		$j = 0;
		$res = '';
		for ($y = 0; $y < strlen($string); $y++) {
			$i = ($i + 1) % 256;
			$j = ($j + $s[$i]) % 256;
			$x = $s[$i];
			$s[$i] = $s[$j];
			$s[$j] = $x;
			$res .= $string[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
		}
		
		return $res;
	 }

	//generate a safe token
	public function TokenRand($length = 32){
		if(!isset($length) || intval($length) <= 32 ){
		  $length = 32;
		}

		if (function_exists('random_bytes')) {
			return bin2hex(random_bytes($length));
		}

		if (function_exists('openssl_random_pseudo_bytes')) {
			return bin2hex(openssl_random_pseudo_bytes($length));
		}
	}
	
	//select appropriate key data file
	private function get_key_data() {
		
		if ($this->error == false) {
			echo $this->data_path;
			if (!is_file($this->data_path)) {
				$this->errors[] = 'Key file not found!';
			} else {
				//get the key
				$keydata = fopen($this->data_path,"r");
				if ($keydata == false) {
					$this->errors[] = 'Cannot read key file!';
				} else {
					$this->token = fread($keydata,filesize($this->data_path));
					fclose($keydata);
					return $this->token;
				}
			}
		} 
		
		return false;
	
	}
	
	//write key data to appropriate file
	private function write_key_data() {
	
		if ($this->error == false) {
			
			if (is_file($this->data_path)) {
				//make a backup
				if (!copy($this->data_path, $this->data_path.'.old')) {
					//backup of key file failed but we continue, and only registering error
					$this->errors[] = "Failed to copy key file.";
				}
				//$dkf = unlink($this->data_path);
			}
			
			//write key to file
			$keydata = fopen($this->data_path,"w");
			
			if ($keydata == false) {
				$this->errors[] = 'Cannot open key file for write';
			} else {
				if ($this->token) {
					fwrite($keydata, $this->token);
					fclose($keydata);
					return true;
				} else {
					$this->errors[] = 'No token found to store';
				}

				fclose($keydata);
			}
		} 
		
		return false;
	}
	
	// encrypt and write the key file
	public function encrypt() {
		
		if ($this->plaintext) {
			if ($this->token == '') $this->token = $this->TokenRand();
			$this->data = $this->rc4a();
			//write key to file
			$res = $this->write_key_data();
			if ($res == false) {
				$this->errors[] = 'Write data failed. Unable to encrypt';;
			} else {
				//reset plaintext
				$this->plaintext = '';
				//reset token
				$this->token = '';
				return true;
			}
		} else {
			$this->error = 'No data to encrypt';
			$this->errors[] = $this->error;
		}
		
		return false;
	}

	// get the key data from file and decrypt
	public function decrypt() {
		if ($this->data) {
			//if ($this->token == '') $this->token = $this->TokenRand();
			//get the key from file
			$token = $this->get_key_data();
			
			if ($token) {
				$this->token = $token;
				
				$this->plaintext = $this->rc4a();
				//reset encrypted data
				$this->data = '';
			} else {
				$this->errors[] = 'No token for decryption';
			}
		} else {
			$this->errors[] = 'No data to decrypt';
		}	
	}

}
/*
function test_crypto() {
	$crypto = new Crypto;
	//test data
	$crypto->type = 'client';
	$crypto->id = 15;
	//make data path
	$crypto->construct_data_path();
	// String to encrypt
	$plaintext='To be or not to be, that is the question';
	$crypto->plaintext = $plaintext;
	$crypto->encrypt();
	$key=$crypto->token;
	$data = $crypto->data;
	$crypto->decrypt();
	$decrypted=$crypto->plaintext;

	if ($crypto->error) echo implode(',',$crypto->errors);

	// Test output
	echo '<br><span style="font-family:Courier">'."\n";
	echo 'Key: '.$key.'<br>'."\n";
	echo 'original: '.$plaintext.'<br>'."\n";
	echo 'encrypted: '.$data.'<br>'."\n";
	echo 'decrypted: '.$decrypted.'<br>'."\n";
	echo '</span>'."\n";
}

test_crypto(); */
?> 