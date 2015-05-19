<?php
// M -> Message
// K -> Key
// R -> Rounds
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
class NullCrypt {
  function CheckUpdate($ML) {
      if (file_exists('NullCrypt.version'))
      if (file_get_contents('NullCrypt.version') != "https://raw.githubusercontent.com/NullPatrol/Secure-Password-Encryption-Function/master/NullCrypt.version") {
      //send mail:
        error_log("Newer Version of NullCrypt available at https://Github.com/NullPatrol/Secure-Password-Encryption-Function/", 1,$ML);
        error_log("Newer Version of NullCrypt available at https://Github.com/NullPatrol/Secure-Password-Encryption-Function/", 0);
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
  function private C($M,$S,$V) {
    global $CC;
    for ($i=0;$i<2;$i++) {
      foreach($CC as $MT) {
      $M=openssl_encrypt($M,$MT,$S,0,$V);
      }
    }
    return $M;
  }
  function private M($C,$S,$V) {
    global $CC;
    for($i=0;$i<2;$i++) {
      foreach(array_reverse($CC) as $MT)
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
    $H = $this->P_E($M,$K);
    $S = substr(str_replace('+','.',base64_encode(md5(sha1(mt_rand(), true)))),0,16);
    $C = crypt($H, sprintf('$6$rounds=%d$%s$', $R, $S));
    $C = explode("\$6\$rounds={$R}\$",$C);
    $C = explode("$",$C[1]);
    $S = $C[0];
    $C = $C[1];
    $Cn = $Sf = $Sx = $Sn = $Cf = $Cx = "";
    $c = strlen($C);
    $d = strlen($S);
    while ($c--) {
      $Cn = $Cn.$this->A2B($C[$c]);
    }
    $Cx = str_split($Cn,8);
    foreach($Cx as $Cg) {
      $Cf = $Cf.$this->B2H($Cg);
    }
    while ($d--) {
      $Sn = $Sn.$this->A2B($S[$d]);
    }
    $Sx = str_split($Sn,8);
    foreach ($Sx as $Sg) {
      $Sf = $Sf.$this->B2H($Sg);
    }
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
    $MC = "\$6\$rounds=".$R."$".$MCs[1]."$".$MCs[0];
    $MCp= explode('$', $MC);
    $CM = crypt($this->P_E($M, $K), sprintf('$%s$%s$%s$', $MCp[1], $MCp[2], $MCp[3]));
    return var_export($CM === $MC, true);
  }
  
  function Obfuscate($C,$K,$R) {
    $n=substr(mt_rand(),0,1);
    $SX= substr(md5(rand()),0,$n);
    $V=mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC),MCRYPT_RAND);
    for($i=0;$i<=$R;$i++) {
      $C=gzcompress(C($C,$K,$V),9);
    }  
    $C=gzcompress(C($C,$SX,$V),9);
    return utf8_encode($n.$SX.$C.$V);
  }
  
  function DeObfuscate($C,$K,$R) {
    $XA=$C[0];
    $S=substr($C,1,$C[0]);
    $C=substr($C,++$XA);
    $IV=mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $V=substr(utf8_decode($C),-$IV);
    $C=substr($C,0,-$IV);
    
    $C=M(gzuncompress(utf8_decode($C)),$S,$V);
    for($i=0;$i<=$R;$i++) {
      $C=M(gzuncompress($C),$K,$V);
    }
    return $C;
  }
}

// Coded by PilferingGod, Cyberguard & Repentance
// Use contact if you have trouble implementing anything
// Contact: Repentance@exploit.im (XMPP)
// Contact: niels@null.net (Mail)
?>
