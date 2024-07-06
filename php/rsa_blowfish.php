<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\Blowfish;
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

// Generate Blowfish key
$blowfishKey = Random::string(16); // 128-bit key for Blowfish

// Encrypt Blowfish key with RSA
$rsaEncryptionStartTime = microtime(true);
$encryptedBlowfishKey = $publicKey->encrypt($blowfishKey);
$rsaEncryptionEndTime = microtime(true);

// Encrypt data with Blowfish
$blowfish = new Blowfish('cbc');  // Use CBC mode
$iv = Random::string($blowfish->getBlockLength() >> 3); // Initialization vector
$blowfish->setKey($blowfishKey);
$blowfish->setIV($iv);

$blowfishEncryptionStartTime = microtime(true);
$ciphertext = $iv . $blowfish->encrypt($plaintext); 
$blowfishEncryptionEndTime = microtime(true);

// Decryption
$rsaDecryptionStartTime = microtime(true);
$decryptedBlowfishKey = $privateKey->decrypt($encryptedBlowfishKey);
$rsaDecryptionEndTime = microtime(true);

if ($decryptedBlowfishKey !== false) {
    $blowfish->setKey($decryptedBlowfishKey);
    $blowfish->setIV(substr($ciphertext, 0, $blowfish->getBlockLength() >> 3));

    $blowfishDecryptionStartTime = microtime(true);
    $decryptedData = $blowfish->decrypt(substr($ciphertext, $blowfish->getBlockLength() >> 3));
    $blowfishDecryptionEndTime = microtime(true);

    // Calculate total time
    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + 
                 ($blowfishEncryptionEndTime - $blowfishEncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) +
                 ($blowfishDecryptionEndTime - $blowfishDecryptionStartTime);

    echo "Total encryption/decryption time (RSA+Blowfish): " . $totalTime * 1000 . " ms\n"; 

    if ($decryptedData === $plaintext) {
        echo "Decryption successful!\n";
    } else {
        echo "Decryption failed!\n";
    }
} else {
    echo "Failed to decrypt Blowfish key.\n"; 
}
