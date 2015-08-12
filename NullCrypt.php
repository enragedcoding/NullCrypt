<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);
error_reporting(0);
if ( !function_exists( 'hex2bin' ) ) {
    function hex2bin($M) {
        $B = "";
        $L = strlen( $M );
        for ( $i = 0; $i < $L; $i += 2 ) {
            $B .= pack( "H*", substr( $M, $i, 2 ) );
        }
        return $B;
    }
}


class bC{
  private $rounds;
  public function __construct($rounds = 14) {
    if(CRYPT_BLOWFISH != 1) {
      throw new Exception("crypt not supported in this installation. See http://php.net/crypt");
    }
    $this->rounds = $rounds;
  }
  private function gtS() {
    $S = sprintf('$2a$%02d$', $this->rounds);
    $B = $this->gtRB(16);
    $S .= $this->eB($B);
    return $S;
  }
  public function CK($input) {
    $hash = crypt($input, $this->gtS());
    if(strlen($hash) > 13)
      return $hash;
    return false;
  }
  public static function VF($input, $existingHash) {
    $hash = crypt($input, $existingHash);
    return $hash === $existingHash;
  }
  
  private $rdms;
  private function gtRB($Ct) {
    $B = '';
    if(function_exists('openssl_random_pseudo_bytes') &&
        (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) { // OpenSSL is slow on Windows
      $B = openssl_random_pseudo_bytes($Ct);
    }
    if($B === '' && is_readable('/dev/urandom') &&
       ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE) {
      $B = fread($hRand, $Ct);
      fclose($hRand);
    }
    if(strlen($B) < $Ct) {
      $B = '';
      if($this->rdms === null) {
        $this->rdms = microtime();
        if(function_exists('getmypid')) {
          $this->rdms .= getmypid();
        }
      }
      for($i = 0; $i < $Ct; $i += 16) {
        $this->rdms = md5(microtime() . $this->rdms);
        if (PHP_VERSION >= '5') {
          $B .= md5($this->rdms, true);
        } else {
          $B .= pack('H*', md5($this->rdms));
        }
      }
      $B = substr($B, 0, $Ct);
    }
    return $B;
  }
  private function eB($input) {
    $itoa64 = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $output = '';
    $i = 0;
    do {
      $c1 = ord($input[$i++]);
      $output .= $itoa64[$c1 >> 2];
      $c1 = ($c1 & 0x03) << 4;
      if ($i >= 16) {
        $output .= $itoa64[$c1];
        break;
      }
      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 4;
      $output .= $itoa64[$c1];
      $c1 = ($c2 & 0x0f) << 2;

      $c2 = ord($input[$i++]);
      $c1 |= $c2 >> 6;
      $output .= $itoa64[$c1];
      $output .= $itoa64[$c2 & 0x3f];
    } while (1);
    return $output;
  }
}

class NullCrypt {
  public $CC;
  public $x; 
  function __construct() {
    $CIPHER = openssl_get_cipher_methods();
    $this->CC = array($CIPHER[16]); //CUSTOMIZABLE! (*CBC Ciphers only*)
    $this->x = 2; // Increases intensity by A LOT
  }
  
  static function GetCipher($CM) {
    $CIPHER = openssl_get_cipher_methods();
    return $CIPHER[$CM];
  }
  static function DisplayCipherMethods() {
    $CIPHER = openssl_get_cipher_methods();
    print_r($CIPHER);
  }
  function CheckUpdate($ML) {
    if (file_exists('NullCrypt.version'))
      if (file_get_contents('NullCrypt.version') != "https://raw.githubusercontent.com/NullPatrol/NullCrypt-Password-Encryption/master/NullCrypt.version") {
        //send mail:
        error_log("Newer Version of NullCrypt available at https://github.com/NullPatrol/NullCrypt-Password-Encryption", 1,$ML);
        error_log("Newer Version of NullCrypt available at https://github.com/NullPatrol/NullCrypt-Password-Encryption", 0);
      }
  }
  
  private function H2B($input){
    if (!is_string($input)) return null;
    $value = unpack('H*', $input);
    return base_convert($value[1], 16, 2);
  }
  // Duplicate Key -- Padding for XOR
  private function DK($M,$K) {
    while (strlen($M)>strlen($K)) {
      $K .= $K;
    }
    $K = explode("\r\n", chunk_split($K, strlen($M), "\r\n"));
    return $K;
  }
  private function A2B($M) {
    return str_pad(decbin(ord($M)), 8, "0", STR_PAD_LEFT);
  }
  private function B2H($B) {
    return dechex(bindec($B));
  }
  private function doXOR($B,$K) {
    if ($B xor $K)
      return "1";
    else 
      return "0";
  }
  private function C($M,$S,$V) {
    for ($i=0;$i<$this->x;$i++) {
      foreach($this->CC as $MT) {
      $M=openssl_encrypt($M,$MT,$S,0,$V);
      }
    }
    return $M;
  }
  private function M($C,$S,$V) {
    for($i=0;$i<$this->x;$i++) {
      foreach(array_reverse($this->CC) as $MT)
      $C=openssl_decrypt($C,$MT,$S,0,$V);
    }
    return $C;
  }
  private function doAND($B,$K) {
    if ($B == $K)
      return "1";
    else 
      return "0";
  }
  
  private function P_E($M,$K) {
  $KK = $this->DK($M,$K);
    $KK = $KK[0];
    $c = strlen($M);
    $result = '';
    $B = $H = $B2 = $KK2 = '';
    
    while ($c--) {
      $B = $B.$this->A2B($M[$c]);
      $KK2 = $KK2.$this->A2B($KK[$c]);
    }
    $c = strlen($B);
  
    while ($c--) {
      $B2 = $B2.$this->doXOR($B[$c],$KK2[$c]);
      $c--;
      $B2 = $B2.$this->doAND($B[$c],$KK2[$c]);
    }
    $c = strlen($B2);
    $Bx = str_split($B2,8);
    
    foreach($Bx as $Bg) {
      $H = $H.$this->B2H($Bg);
    }
  return $H;
  }
  
  function Hash($M,$K,$R) {
    // C -> Character
    if($R<1000)die("ERR_NULLCRYPT::MINIMUM 1000 ROUNDS");
    $Rr=8;
    $i=$R;
    while ($i>=500) {
     $Rr++;
     $i=$i-500;
    }
    echo $M.$R.$K."<br><br>";
    $bC=new bC($Rr);
    $H = $this->P_E($M,$K);
    $S = substr(str_replace('+','.',base64_encode(md5(sha1(mt_rand(), true)))),0,16);
    $C = crypt($H, sprintf('$6$rounds=%d$%s$', $R, $S));
    $C = explode("\$6\$rounds={$R}\$",$C);
    $C = explode("$",$C[1]);
    $S = $C[0];
    $C = $bC->CK($M.$K.$R);
    $Cn = $Sf = $Sx = $Sn = $Cf = $Cx = "";
    $c = strlen($C);
    $d = strlen($S);
    while ($c--)
      $Cn = $Cn.$this->A2B($C[$c]);
    $Cx = str_split($Cn,8);
    foreach($Cx as $Cg)
      $Cf = $Cf.$this->B2H($Cg);
    while ($d--)
      $Sn = $Sn.$this->A2B($S[$d]);
    $Sx = str_split($Sn,8);
    foreach ($Sx as $Sg)
      $Sf = $Sf.$this->B2H($Sg);
    $C = $Cf.":".$Sf;
    return $C;
  }
  
  function Decrypt($C) {
    $H = explode(':',$C);
    $CB = $CS = $CDS = "";
    $CB = strrev(hex2bin($H[0]));
    $CS = strrev(hex2bin($H[1]));
    return $CB.":".$CS;
  }
  
  function HashCompare($C,$M,$K,$R) {
    $MC = $this->Decrypt($C); // Decrypted C
    $MCs = explode(':',$MC);
    $bC=bC::VF($M,$MCs[0].$R.$K);
    $bc = (var_export($MCs[0] === $MC, true)==='true');
    return $bC;
  }
  
  function Obfuscate($C,$K="CyberGuard",$R=2) {
    $n=substr(mt_rand(),0,1);
    $D=substr(md5(mt_rand()),0,5);
    $SX= substr(md5(rand()),0,$n);
    $V=mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC),MCRYPT_RAND);
    for($i=0;$i<=$R;$i++) {
      $C=gzcompress($this->C($C,$K,$V),9);
    }  
    $C=gzcompress($this->C($C,$SX,$V),9);
    return utf8_encode($D.$R."x".$n.$SX.$C.$V);
  }
  function DeObfuscate($C,$K="CyberGuard") {
    if (strlen($C) >= 1 && strpos($C,'x') !== false) {
      $C=substr($C,5);
      $R=explode('x',$C,2);$Rn=strlen($R[0]);$C=$R[1];
      $XA=$C[0];$XA++;$R=$R[0];
      $S=substr($C,1,$C[0]);
      $C=substr($C,$XA);
      $IV=mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
      $V=substr(utf8_decode($C),-$IV);
      $C=substr($C,0,-$IV);
      $C=$this->M(gzuncompress(utf8_decode($C)),$S,$V);
      for($i=0;$i<=$R;$i++) {
        $C=$this->M(gzuncompress($C),$K,$V);
      }
      if (strlen($C)<1)
        return "Invalid Pass";
      else return $C;
    } else return "Invalid Pass";
  }
}
// Coded by PilferingGod, Cyberguard & Repentance
// Use contact if you have trouble implementing anything
// Contact: Repentance@exploit.im (XMPP)
// Contact: niels@null.net (Mail)
?>
