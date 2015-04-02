<?php
// M -> Message
// K -> Key
// ASCII to Binary
// R -> Rounds
$K = "KEY";
$M = "Test Message";
$R = 10000;



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



// Duplicate Key -- Padding for XOR
function DK($M,$K) {
	while (strlen($M)>strlen($K)) {
		$K .= $K;
	}
	$K = explode("\r\n", chunk_split($K, strlen($M), "\r\n"));
	return $K;
}

// Ascii to Binary
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

// Encrypt
function encrypt($M,$K,$R) {
  // C -> Character
  $KK = DK($M,$K);
  $KK = $KK[0];
  $c = strlen($M);
  $result = '';
  $B = $H = $B2 = $KK2 = '';
  
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
  $C = crypt($H, sprintf('$6$rounds=%d$%s$', $R, $S));
  $C = explode("\$6\$rounds={$R}\$",$C);
  $C = explode("$",$C[1]);
  $S = $C[0];
  $C = $C[1];
  $Cn = $Sf = $Sx = $Sn = $Cf = $Cx = "";
  $c = strlen($C);
  $d = strlen($S);
  echo $C.":".$S."<br>";
  while ($c--) {
    $Cn = $Cn.A2B($C[$c]);
  }
  $Cx = str_split($Cn,8);
  foreach($Cx as $Cg) {
    $Cf = $Cf.B2H($Cg);
  }
  while ($d--) {
    $Sn = $Sn.A2B($S[$d]);
  }

  $Sx = str_split($Sn,8);
  foreach ($Sx as $Sg) {
    $Sf = $Sf.B2H($Sg);
  }
  $C = $Cf.":".$Sf;
  return $C;
}

function decrypt($C) {
  $H = explode(':',$C);
  $CB = $CS = "";
  $c = strlen($H[0]);
  $d = strlen($H[1]);
  while ($c--)
    $CB = $CB.base_convert($H[0][$c], 16, 2);
  while ($d--)
    $CS = $CS.base_convert($H[1][$c], 16, 2);
  return $CB.":".$CS;
}


function compare($C,$M,$K) {
  $CM = decrypt(encrypt($M,$K)); // Encrypted M
  $MC = decrypt($C); // Decrypted C

$test_hash = crypt($test_pw, sprintf('$%s$%s$%s$', $parts[1], $parts[2], $parts[3]));

// compare
// echo $given_hash . "\n" . $test_hash . "\n" . var_export($given_hash === $test_hash, true);

}
$C = encrypt($M,$K,$R);
echo $C;
echo "<br>";
echo decrypt($C);



// Compare Crypted Text with Message. (returns Binary True or False)
//   x1 -> String,  Encrypted Password
//   x2 -> String,  Given Password
//   x3 -> Integer, Key
// compare(x1,x2,x3)
/* if (compare($C,$M,$K)) 
  echo true;
else
  echo false;
*/


// Coded by PilferingGod & Cyberguard & Repentance
// Use contact if you have trouble implementing anything
// Contact: Repentance@exploit.im
// Contact: niels@null.net
?>
