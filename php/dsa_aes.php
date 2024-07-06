<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\DSA;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Random;

// Function to generate DSA key pair
function generateDSAKeyPair($bits = 2048) {
    $dsa = new DSA();
    extract($dsa->createKey($bits)); // Extracts keys from the associative array
    return array('public' => $publicKey, 'private' => $privateKey);
}

// Plaintext data
$plaintext = "This is sensitive data to be encrypted.";

// Generate DSA key pair
$keys = generateDSAKeyPair();
$publicKey = $keys['public'];
$privateKey = $keys['private'];

// AES encryption
$aes = new AES('gcm'); // Use GCM for authenticated encryption
$aesKey = Random::string(32);  // Generate a random AES key
$iv = Random::string($aes->getBlockLength() >> 3); // IV for GCM
$tag = ''; // Authentication tag

$aes->setKey($aesKey);
$aesEncryptionStartTime = microtime(true);
$ciphertext = $aes->encrypt($plaintext, $iv, $tag);
$aesEncryptionEndTime = microtime(true);

// DSA signing of the ciphertext
$dsaSignatureStartTime = microtime(true);
$signature = $privateKey->sign($ciphertext); 
$dsaSignatureEndTime = microtime(true);

// Decryption and verification
$aesDecryptionStartTime = microtime(true);
$decryptedData = $aes->decrypt($ciphertext, $iv, $tag);
$aesDecryptionEndTime = microtime(true);

// Verify the signature
$dsaVerificationStartTime = microtime(true);
$isValidSignature = $publicKey->verify($ciphertext, $signature);
$dsaVerificationEndTime = microtime(true);

// Calculate total time (including DSA and AES operations)
$totalTime = ($aesEncryptionEndTime - $aesEncryptionStartTime) + 
             ($dsaSignatureEndTime - $dsaSignatureStartTime) + 
             ($aesDecryptionEndTime - $aesDecryptionStartTime) +
             ($dsaVerificationEndTime - $dsaVerificationStartTime);
echo "Total encryption/decryption time (DSA+AES): " . $totalTime * 1000 . " ms\n"; 

if ($isValidSignature) {
    echo "Signature is valid.\n";

    if ($decryptedData === $plaintext) {
        echo "Decryption successful!\n";
    } else {
        echo "Decryption failed!\n";
    }
} else {
    echo "Signature is invalid!\n";
}
