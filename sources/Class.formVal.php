<?php 
/**
 * @info: form validation stuff
 * @version: 211003
 * @changes:
 * - changed normalize_tel -> normalizeTel, truncate_str -> truncateString, validEMail_simple -> validEmailSimple for psr-1
 * - added namespace Core
 * @history:
 * -- 110126
 *  -made it a class
 *  -added removeXss
 *  -added validTel
 *  -added validUrl
 *  -added truncate_str
 *  -- 111010
  * - added normalize_tel
 * - added a constructor
 * - added normalization to telephone number
 * - added ability to pass you own array of chars for normalizing tels
 * - fixed protected functions to public except truncate_str & normalize_tel
 * - changed is_numeric to ctype_digit in telephone checks
 *  */
namespace BMP\Core;
 
defined( '_w00t_frm' ) or die( 'Restricted access' );

class ValForm {
	var $tel_chars = array();
	
	public function __construct($tel_chr = array()) {
		if (count($tel_chr)) {
			$this->tel_chars = $tel_chr;
		} else {
			$this->tel_chars = array('+','-',' ','/','.',',');
		}
	}
	
	public function truncateString($string,$len) {
		if (!$len) {$len = 22;}
		if (strlen($string) <= $len) { return $string; }
		else {
			$string = substr($string,0,$len);
			return $string;
		}
	}

	protected function normalizeTel($tel) {
		/**
		 * removes some common non-digit chars like
		 * -+/. and spaces from a given telephone number
		 */
		$tel = trim($tel);
		$tel = str_replace($tel_chars,'',$tel);
		return $tel;
	}
	
	public function validEmail($email) {
	   $isValid = true;
	   $atIndex = strrpos($email, "@");
	   if (is_bool($atIndex) && !$atIndex) {
	      $isValid = false;
	   } else {
	      $domain = substr($email, $atIndex+1);
	      $local = substr($email, 0, $atIndex);
	      $localLen = strlen($local);
	      $domainLen = strlen($domain);
	      if ($localLen < 1 || $localLen > 64) {
	         // local part length exceeded
	         $isValid = false;
	      }
	      else if ($domainLen < 1 || $domainLen > 255) {
	         // domain part length exceeded
	         $isValid = false;
	      }
	      else if ($local[0] == '.' || $local[$localLen-1] == '.') {
	         // local part starts or ends with '.'
	         $isValid = false;
	      }
	      else if (preg_match('/\\.\\./', $local)) {
	         // local part has two consecutive dots
	         $isValid = false;
	      }
	      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
	         // character not valid in domain part
	         $isValid = false;
	      }
	      else if (preg_match('/\\.\\./', $domain)) {
	         // domain part has two consecutive dots
	         $isValid = false;
	      }
	      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local))) {
	         // character not valid in local part unless 
	         // local part is quoted
	         if (!preg_match('/^"(\\\\"|[^"])+"$/',
	             str_replace("\\\\","",$local))){
	            $isValid = false;
	         }
	      }
	      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
	         // domain DNS resolving failed
	         $isValid = false;
	      }
	   }
	   return $isValid;
	}
	
	public function validEmailSimple($email)
	{ 
		$regexp='/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
		return preg_match($regexp, trim($email));
	}
	
	public function validName($name) 
	{
		$name = preg_replace('/[\s]+/is', ' ', $name);
		$name = trim($name);

		return preg_match('/^[a-z\s]+$/i', $name);
	}
	
	function removeXss($string) {
	  if (is_array($string)) {
	    $return = array();
	    foreach ($string as $key => $val) {
	      $return[$this->removeXss($key)] = $this->removeXss($val);
	    }
	    return $return;
	  }
	  $string = htmlspecialchars(strip_tags(trim ($string)),ENT_QUOTES);
	  return $string;
	}
	
	public function validUrl($url) {
		if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
			return true;
		} else {
			return false;
		}
	}
	
	public function validTel($tel) {
		$tel = self::normalizeTel($tel);
		if (strlen($tel) == 10) {
			if (ctype_digit($tel)) {
				return $tel;
			} else {
				return -1;
			}
		} else {
			return -2;
		}
	}
	
	function validImage($image) {
		if (is_array($image)) {
			foreach ($image as $image_i) {
				$imageinfo = getimagesize($image_i);
			}
		} else {
			$imageinfo = getimagesize($image);
		}
		if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg' && $imageinfo['mime'] !='image/png' && $imageinfo['mime'] !='image/bmp') {
			return false;
		} else {
			return true;
		}
	}

	function validFileExtension($file_name,$blacklist) {
		foreach ($blacklist as $item) {
			if(preg_match("/$item\$/i", $file_name)) {
				return false;
			}
		}
		return true;
	}
}

?>
