<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\AES;
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

$aes = new AES('cbc');
$aesKey = Random::string(32);  // For AES-256
$iv = Random::string($aes->getBlockLength() >> 3); // For GCM mode

// Benchmarking: RSA encryption of AES key
$rsaEncryptionStartTime = microtime(true);
$rsaEncryptionMemoryStart = memory_get_usage();
$encryptedAesKey = $publicKey->encrypt($aesKey);
$rsaEncryptionMemoryEnd = memory_get_usage();
$rsaEncryptionEndTime = microtime(true);

$aes->setIV($iv);
$aes->setKey($aesKey);

// Benchmarking: AES encryption
$aesEncryptionStartTime = microtime(true);
$aesEncryptionMemoryStart = memory_get_usage();
$ciphertext = $iv . $aes->encrypt($plaintext);
$aesEncryptionMemoryEnd = memory_get_usage();
$aesEncryptionEndTime = microtime(true);

// Benchmarking: RSA decryption of AES key
$rsaDecryptionStartTime = microtime(true);
$rsaDecryptionMemoryStart = memory_get_usage();
$decryptedAesKey = $privateKey->decrypt($encryptedAesKey);
$rsaDecryptionMemoryEnd = memory_get_usage();
$rsaDecryptionEndTime = microtime(true);

// Decrypt the data using AES-GCM
if ($decryptedAesKey !== false) {
    $aes = new AES('cbc');
    $aes->setKey($decryptedAesKey);
    $aesDecryptionStartTime = microtime(true);
    $aesDecryptionMemoryStart = memory_get_usage();
    $aes->setIV(substr($ciphertext, 0, 16)); // Extract IV

    // Wrap in a try-catch block to catch decryption errors
    $decryptedData = $aes->decrypt(substr($ciphertext, 16));
    $aesDecryptionMemoryEnd = memory_get_usage();
    $aesDecryptionEndTime = microtime(true);

    // Calculate total time
    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + ($aesEncryptionEndTime - $aesEncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) + ($aesDecryptionEndTime - $aesDecryptionStartTime);

    echo "Total encryption/decryption time (RSA+AES): " . $totalTime * 1000 . " ms\n";

    // Calculate memory usage
    $rsaEncryptionMemory = $rsaEncryptionMemoryEnd - $rsaEncryptionMemoryStart;
    $aesEncryptionMemory = $aesEncryptionMemoryEnd - $aesEncryptionMemoryStart;
    $rsaDecryptionMemory = $rsaDecryptionMemoryEnd - $rsaDecryptionMemoryStart;
    $aesDecryptionMemory = $aesDecryptionMemoryEnd - $aesDecryptionMemoryStart;

    echo "Memory usage for RSA encryption: " . $rsaEncryptionMemory . " bytes\n";
    echo "Memory usage for AES encryption: " . $aesEncryptionMemory . " bytes\n";
    echo "Memory usage for RSA decryption: " . $rsaDecryptionMemory . " bytes\n";
    echo "Memory usage for AES decryption: " . $aesDecryptionMemory . " bytes\n";

    // Verify decrypted data
    if ($decryptedData === $plaintext) {
        echo "Decryption successful!\n";
    } else {
        echo "Decryption failed!\n"; // This should ideally never happen with correct decryption.
    }
} else {
    echo "Failed to decrypt AES key.\n"; // Handle RSA decryption errors.
}
?>
