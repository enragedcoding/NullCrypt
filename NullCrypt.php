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
  function H2B($input){
    if (!is_string($input)) return null;
    $value = unpack('H*', $input);
    return base_convert($value[1], 16, 2);
  }
  // Duplicate Key -- Padding for XOR
  function DK($M,$K) {
    while (strlen($M)>strlen($K)) {
      $K .= $K;
    }
    $K = explode("\r\n", chunk_split($K, strlen($M), "\r\n"));
    return $K;
  }
  
  function A2B($M) {
    return str_pad(decbin(ord($M)), 8, "0", STR_PAD_LEFT);
  }
  
  function B2H($B) {
    return dechex(bindec($B));
  }
  
  function doXOR($B,$K) {
    if ($B xor $K)
      return "1";
    else 
      return "0";
  }
  function doAND($B,$K) {
    if ($B == $K)
      return "1";
    else 
      return "0";
  }
  
  function P_E($M,$K) {
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
  
  function Encrypt($M,$K,$R) {
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
  
  function CheckUpdate($ML) {
      if (file_get_contents('NullCrypt.version') != "https://raw.githubusercontent.com/NullPatrol/Secure-Password-Encryption-Function/master/version.php")
      //send mail:
      error_log("Newer Version of NullCrypt available at https://Github.com/NullPatrol/Secure-Password-Encryption-Function/", 1,$ML);
  }
  
  
  function Decrypt($C) {
    $H = explode(':',$C);
    $CB = $CS = $CDS = "";
    $CB = strrev(hex2bin($H[0]));
    $CS = strrev(hex2bin($H[1]));
    return $CB.":".$CS;
  }
  
  function Compare($C,$M,$K,$R) {
    $MC = $this->Decrypt($C); // Decrypted C
    $MCs = explode(':',$MC);
    $MC = "\$6\$rounds=".$R."$".$MCs[1]."$".$MCs[0];
    $MCp= explode('$', $MC);
    $CM = crypt($this->P_E($M, $K), sprintf('$%s$%s$%s$', $MCp[1], $MCp[2], $MCp[3]));
    return var_export($CM === $MC, true);
  }
}
// Coded by PilferingGod, Cyberguard & Repentance
// Use contact if you have trouble implementing anything
// Contact: Repentance@exploit.im
// Contact: niels@null.net
?>
