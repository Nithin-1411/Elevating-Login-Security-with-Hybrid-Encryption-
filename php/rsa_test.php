<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;

// Create the RSA key pair (default settings: 2048-bit, exponent 65537)
$rsa = RSA::createKey();
$publicKey = $rsa->getPublicKey();
$privateKey = $rsa;

// Message to encrypt
$message = "This is a confidential message.";

// Encrypt using the public key

$ciphertext = $publicKey->encrypt($message);
$encryptedData = base64_encode($ciphertext);

// Decrypt using the private key
$decrypted = $privateKey->decrypt($ciphertext);


// Display the keys in PKCS format (for demonstration purposes)

echo $privateKey->toString('PKCS1') . "\n\n\n\n\n"; // Save this in a secure location


// echo "----- BEGIN PUBLIC KEY -----\n";
echo $publicKey->toString('PKCS8') . "\n"; // This can be shared publicly
// echo "----- END PUBLIC KEY -----\n";

// echo "\n\nOriginal Message: " . $message . "\n";
// echo "Encrypted Message: " . $encryptedData . "\n";
// echo "Decrypted Message: " . $decrypted . "\n";
?>
