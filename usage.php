require_once 'NullCrypt.php';
$NullCrypt = new NullCrypt;


$password = "Test Message";
$secretKey= "gGwVt4VbEJf58gYr";
$rounds = 12345;
$encrypted = $NullCrypt->Encrypt($password,$secretKey,$rounds);

$given1 = "False Message";
$given2 = "Test Message";

echo "Encrypted: {$encrypted}<br>";
echo "Pass Given: ".$given1."<br>";
echo "Pass Given: ".$given2."<br><br>";

echo "Match1: ".$NullCrypt->Compare($encrypted,$given1,$secretKey,$rounds)."<br>";
echo "Match2: ".$NullCrypt->Compare($encrypted,$given2,$secretKey,$rounds)."<br>";
  
  

// $NullCrypt->Encrypt()
// Compare Crypted Text with Message. (returns Binary True or False)
//   x1 -> String,  Text to Encrypt
//   x2 -> String,  Secret Key
//   x3 -> Integer, Rounds
// $NullCrypt->Encrypt(x1,x2,x3);
/*
echo Encrypt("Test Message","amO3nOIdmOql",10184));
*/
  

// $NullCrypt->Compare()
// Compare Crypted Text with Message. (returns Binary True or False)
//   x1 -> String,  Encrypted Password
//   x2 -> String,  Given Password
//   x3 -> Integer, Key
//   x4 -> Intiger, Rounds
// Compare(x1,x2,x3,x4);
/*
if (Compare($Hash,$_POST['password'],"SecretKey",8480)) 
  echo true;
else
  echo false;
*/
  
  
// Coded by PilferingGod, Cyberguard & Repentance
// Use contact if you have trouble implementing anything
// Contact: Repentance@exploit.im
// Contact: niels@null.net
