<?php
namespace App\Http\Controllers\Helpers;




class CryptRelated
{
	
	
  public $key;
	public function __construct(){
  $this->key = "Default_Anahtar";
	}



	public function encrypt($string, $defaultKey = null) {
  $key = ($defaultKey !== null) ? $defaultKey : $this->key; //key to encrypt and decrypts.
  $result = '';
  $test = [];
   for($i=0; $i<strlen($string); $i++) {
     $char = substr($string, $i, 1);
     $keychar = substr($key, ($i % strlen($key))-1, 1);
     $char = chr(ord($char)+ord($keychar));

     $test[$char]= ord($char)+ord($keychar);
     $result.=$char;
   }

   return urlencode(base64_encode($result));
}

public function decrypt($string, $defaultKey = null) {
    $key = ($defaultKey !== null) ? $defaultKey : $this->key; //key to encrypt and decrypts.
    $result = '';
    $string = base64_decode(urldecode($string));
   for($i=0; $i<strlen($string); $i++) {
     $char = substr($string, $i, 1);
     $keychar = substr($key, ($i % strlen($key))-1, 1);
     $char = chr(ord($char)-ord($keychar));
     $result.=$char;
   }
   return $result;
}
    

  
}
