<?php
// require '../vendor/autoload.php';

// use phpseclib3\Crypt\EC;


// // Sample Data (Replace with your actual values)
// $message = "This is the message to be signed";


// $private = EC::createKey('Ed25519');
// $public = $private->getPublicKey();


// $signature = $private->sign($message);
// echo $private->getPublicKey()->verify($message, $signature) ?
//     'valid signature' :
//     'invalid signature';



require '../vendor/autoload.php';

use phpseclib3\Crypt\EC;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Random;

// 1. ECC Key Generation (Ed25519)
$private = EC::createKey('Ed25519');
$public = $private->getPublicKey();




// 2. Message to Encrypt (Replace with your actual message)
$plaintext = "This is the confidential message to be encrypted";


$aes = new AES('cbc');  
$aesKey = Random::string(32);  // For AES-256

$iv = Random::string($aes->getBlockLength() >> 3); // For GCM mode




$encryptedKey = $private->sign($aesKey); 



$aes->setIV($iv);

$aes->setKey($aesKey);


$ciphertext = $iv . $aes->encrypt($plaintext);




// 5. Encrypt the Message with the AES Key (Choose a secure mode like GCM)
$cipher = new AES('gcm'); 
$cipher->setKey($aesKey);
$iv = random_bytes(16); // Initialization Vector for GCM mode
$ciphertext = $cipher->encrypt($plaintext, $iv, $tag); // $tag is the authentication tag

// ------------------ Message Transmission ------------------ //
// The encrypted key ($encryptedKey) and the encrypted message ($ciphertext & $iv & $tag)
// are sent to the recipient.
// --------------------------------------------------------- //


// ------------------ Recipient's Side (Decryption) ------------------ //

// 1. Decrypt the AES Key using the Recipient's Private Key
$decryptedKey = $private->decrypt($encryptedKey, 'ECDH');

// 2. Decrypt the Message using the Decrypted AES Key
$cipher->setKey($decryptedKey);
$decryptedMessage = $cipher->decrypt($ciphertext, $iv, $tag); 

echo "Decrypted Message: " . $decryptedMessage; 
// ------------------------------------------------------------------ //
