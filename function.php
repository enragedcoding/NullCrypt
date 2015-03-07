<?php
// M -> Message
// ASCII to Binary
$K = "KEY";
$M = "Test Message";

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

// Binary to Hex
function B2H($B) {
  return dechex(bindec($B));
}

// XOR function
function doXOR($B,$K) {
  if ($B xor $K)
    return "1";
  else 
    return "0";
}

function encrypt($M,$K) {
  // C -> Character
  $KK = DK($M,$K);
  $KK = $KK[0];
  $c = strlen($M);
  $result = '';
  $B = '';
  $H = '';
  $B2 = '';
  $KK2 = '';
  
  while ($c--) {
    $B = $B.A2B($M[$c]);
    $KK2 = $KK2.A2B($KK[$c]);
  }
  $c = strlen($B);
  
  while ($c--) {
    $B2 = $B2.doXOR($B[$c],$KK2[$c]);
  }
  $c = strlen($B2);
  $Bx = str_split($B2,8);
  
  foreach($Bx as $Bg) {
    $H = $H.B2H($Bg);
  }
  $S = substr(str_replace('+','.',base64_encode(md5(mt_rand(), true))),0,16);
  $R = 10000;
  $C = crypt($H, sprintf('$6$rounds=%d$%s$', $R, $S));
  return $C;
}

echo encrypt($M,$K);
?>
