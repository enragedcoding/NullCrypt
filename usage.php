<?php
require_once 'NullCrypt.php';
$NullCrypt = new NullCrypt;

// Sends email if NullCrypt.version doesn't match the Github NullCrypt.version.
// Remove below line to not get an email.
$NullCrypt->CheckUpdate("admin@example.com");

$password = "Test Message";
$secretkey= "gGwVt4VbEJf58gYr";
$rounds = 12345;
$encrypted = $NullCrypt->Hash($password,$secretkey,$rounds);

$given1 = "False Message";
$given2 = "Test Message";

echo "Encrypted: {$encrypted}<br>";
echo "Pass Given: ".$given1."<br>";
echo "Pass Given: ".$given2."<br><br>";

echo "Match1: ".$NullCrypt->HashCompare($encrypted,$given1,$secretkey,$rounds)."<br>";
echo "Match2: ".$NullCrypt->HashCompare($encrypted,$given2,$secretkey,$rounds)."<br>";
  
  

// $NullCrypt->Hash()
// Hash Text with a key for a certain amount of rounds. (returns Encrypted String)
//   x1 -> String,  Text to Encrypt
//   x2 -> String,  Secret Key
//   x3 -> Integer, Rounds
// $NullCrypt->Hash(x1,x2,x3);
/*
echo $NullCrypt->Hash("Test Message","amO3nOIdmOql",10184);
*/
  

// $NullCrypt->HashCompare()
// Compare Crypted Text with eachother. (returns Binary True or False)
//   x1 -> String,  Encrypted Password
//   x2 -> String,  Plain Password to compare
//   x3 -> Integer, Key
//   x4 -> Intiger, Rounds
// $NullCrypt->HashCompare(x1,x2,x3,x4);
/*
if ($NullCrypt->HashCompare($Hash,$password,"SecretKey",8480)) 
  echo true;
else
  echo false;
*/




for ($i=0;$i<10;$i++) echo "<br>";
//##############################################################################################\\
//##############################################################################################\\
//##############################################################################################\\
//##############################################################################################\\
//##############################################################################################\\

//$NullCrypt->DisplayCipherMethods(); //Use this to compose your own encryption method - More == bigger output
//Some methods might not work, Please create an issue on our github if this happens and we'll look into it!!
// echo $NullCrypt->GetCipher(16); (will display the name of the Cipher as well)
$NullCrypt->CC = array($NullCrypt->GetCipher(16),
			$NullCrypt->GetCipher(25)
		       );
$NullCrypt->x = 2; // Unless you really know what you're doing. Don't touch this!! ## increasing this increases stringlength /round and loading time /round.

$message     = "Test Message";
$secretkey   = "gGwVt4VbEJf58gYr";
$rounds      = 2; // WARNING, Increments SUPER FAST! - Max Rounds is up to you.
$time_start = microtime(true);
$obfuscated  = $NullCrypt->Obfuscate($message,$secretkey,$rounds);
$time_end = microtime(true);
$time_obf = round(($time_end - $time_start)*1000,2);
$secretkey2  = "mJtNb7XaC36KlCDa";
if (strlen($obfuscated) < 100000) 
$obfuscated_x = $obfuscated;
else
$obfuscated_x = "Obfuscated string not printed because it's over 100,000 characters. This is to prevent excessive loading time";
echo "Obfuscated: <textarea rows=5 cols=200>{$obfuscated_x}</textarea><br><br>";

echo "Length: ".number_format(strlen($obfuscated))."<br>";
echo "Length: ".number_format(strlen(base64_encode($obfuscated)))." (base64)<br>";
echo "Length: ".number_format(strlen(bin2hex($obfuscated)))." (Hex)<br><br>";

echo "Key1: ".$secretkey."<br>";
echo "Key2: ".$secretkey2."<br><br>";

echo "Time: ".$time_obf."ms<br><br>";


echo "Match1: ".$NullCrypt->DeObfuscate($obfuscated,$secretkey)."<br>";
echo "Match2: ".$NullCrypt->DeObfuscate($obfuscated,$secretkey2)."<br>";


/*

// TABLE OF STRINGLENGTHS & Times!!!
// Give this a loading time of 15-30 seconds
for ($i=0;$i<3;$i++) echo "<br>";
$rounds = 14; //Up to how many rounds you want to paste, 1-14 is default

echo "<style>td { width:100px }</style>";
echo "<center><table border=1><tr><td>Rounds</td><td>Length (raw)</td><td>Time</td></tr>";
for($i=1;$i<=$rounds;$i++) {
  $time_start = microtime(true);
  $obfuscated  = $NullCrypt->Obfuscate($message,$secretkey,$i);
  $time_end = microtime(true);
  $time_obf = number_format(($time_end - $time_start)*1000,2);
  $length = number_format(strlen($obfuscated));
  echo "<tr><td>{$i}</td><td>{$length}</td><td>{$time_obf}ms</td></tr>";
}
echo "</table></center><br><br><br><br>";

*/
  
  
// $NullCrypt->Obfuscate()
// Obfuscate Text with a key. (returns Obfuscated String)
//   x1 -> String,  Text to Encrypt
//   x2 -> String,  Secret Key
//   x3 -> Integer, Rounds (1-10)
// $NullCrypt->Obfuscate(x1,x2,x3);
/*
echo $NullCrypt->Obfuscate("Test Message","amO3nOIdmOql",4);
*/


// $NullCrypt->DeObfuscate()
// DeObfuscate string with a key. (returns DeObfuscated String or "error" if key is invalid)
//   x1 -> String,  Text to DeObfuscate
//   x2 -> String,  Secret Key used for Obfuscation
// $NullCrypt->DeObfuscate(x1,x2);
/*
echo $NullCrypt->DeObfuscate($Obfuscated,"amO3nOIdmOql");
*/
  
  
  
  
  
// Coded by Cyberguard & Repentance
// Use contact if you have trouble implementing anything
// Contact: Repentance@exploit.im
// Contact: niels@null.net
?>
