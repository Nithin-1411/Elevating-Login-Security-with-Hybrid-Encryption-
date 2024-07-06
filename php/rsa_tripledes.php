<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\TripleDES; // Use TripleDES instead of Blowfish
use phpseclib3\Crypt\Random;

// Function to generate RSA key pair
function generateRSAKeyPair($bits = 2048) {
    $rsa = RSA::createKey($bits);
    $publicKey = $rsa->getPublicKey();
    $privateKey = $rsa;
    return array('public' => $publicKey, 'private' => $privateKey);
}

// Plaintext data
$plaintext = "This is sensitive data to be encrypted.";

// Generate RSA key pair
$keys = generateRSAKeyPair();
$publicKey = $keys['public'];
$privateKey = $keys['private'];

// Generate Triple DES key
$tripleDesKey = Random::string(24); // 192-bit key for Triple DES (168 effective bits)

// Encrypt Triple DES key with RSA
$rsaEncryptionStartTime = microtime(true);
$encryptedTripleDesKey = $publicKey->encrypt($tripleDesKey);
$rsaEncryptionEndTime = microtime(true);

// Encrypt data with Triple DES (using CBC mode)
$tripleDes = new TripleDES('cbc'); 
$iv = Random::string($tripleDes->getBlockLength() >> 3); // Initialization vector
$tripleDes->setKey($tripleDesKey);
$tripleDes->setIV($iv);

$tripleDesEncryptionStartTime = microtime(true);
$ciphertext = $iv . $tripleDes->encrypt($plaintext); 
$tripleDesEncryptionEndTime = microtime(true);

// Decryption
$rsaDecryptionStartTime = microtime(true);
$decryptedTripleDesKey = $privateKey->decrypt($encryptedTripleDesKey);
$rsaDecryptionEndTime = microtime(true);

if ($decryptedTripleDesKey !== false) {
    $tripleDes->setKey($decryptedTripleDesKey);
    $tripleDes->setIV(substr($ciphertext, 0, $tripleDes->getBlockLength() >> 3)); 

    $tripleDesDecryptionStartTime = microtime(true);
    $decryptedData = $tripleDes->decrypt(substr($ciphertext, $tripleDes->getBlockLength() >> 3));
    $tripleDesDecryptionEndTime = microtime(true);

    // Calculate total time
    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + 
                 ($tripleDesEncryptionEndTime - $tripleDesEncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) +
                 ($tripleDesDecryptionEndTime - $tripleDesDecryptionStartTime);

    echo "Total encryption/decryption time (RSA+TripleDES): " . $totalTime * 1000 . " ms\n"; 

    if ($decryptedData === $plaintext) {
        echo "Decryption successful!\n";
    } else {
        echo "Decryption failed!\n";
    }
} else {
    echo "Failed to decrypt Triple DES key.\n"; 
}
