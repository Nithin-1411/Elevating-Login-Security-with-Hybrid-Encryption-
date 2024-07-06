<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\DES;
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

$des = new DES('cbc');
$desKey = Random::string(8);  // For DES, 8-byte key
$iv = Random::string($des->getBlockLength() >> 3); // IV size for DES in CBC mode

// Benchmarking: RSA encryption of DES key
$rsaEncryptionStartTime = microtime(true);
$rsaEncryptionMemoryStart = memory_get_usage();
$encryptedDesKey = $publicKey->encrypt($desKey);
$rsaEncryptionMemoryEnd = memory_get_usage();
$rsaEncryptionEndTime = microtime(true);

$des->setIV($iv);
$des->setKey($desKey);

// Benchmarking: DES encryption
$desEncryptionStartTime = microtime(true);
$desEncryptionMemoryStart = memory_get_usage();
$ciphertext = $iv . $des->encrypt($plaintext);
$desEncryptionMemoryEnd = memory_get_usage();
$desEncryptionEndTime = microtime(true);

// Benchmarking: RSA decryption of DES key
$rsaDecryptionStartTime = microtime(true);
$rsaDecryptionMemoryStart = memory_get_usage();
$decryptedDesKey = $privateKey->decrypt($encryptedDesKey);
$rsaDecryptionMemoryEnd = memory_get_usage();
$rsaDecryptionEndTime = microtime(true);

// Decrypt the data using DES
if ($decryptedDesKey !== false) {
    $des = new DES('cbc');
    $des->setKey($decryptedDesKey);
    $desDecryptionStartTime = microtime(true);
    $desDecryptionMemoryStart = memory_get_usage();
    $des->setIV(substr($ciphertext, 0, 8)); // Extract IV

    // Wrap in a try-catch block to catch decryption errors
    $decryptedData = $des->decrypt(substr($ciphertext, 8));
    $desDecryptionMemoryEnd = memory_get_usage();
    $desDecryptionEndTime = microtime(true);

    // Calculate total time
    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + ($desEncryptionEndTime - $desEncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) + ($desDecryptionEndTime - $desDecryptionStartTime);

    echo "Total encryption/decryption time (RSA+DES): " . $totalTime * 1000 . " ms\n";

    // Calculate memory usage
    $rsaEncryptionMemory = $rsaEncryptionMemoryEnd - $rsaEncryptionMemoryStart;
    $desEncryptionMemory = $desEncryptionMemoryEnd - $desEncryptionMemoryStart;
    $rsaDecryptionMemory = $rsaDecryptionMemoryEnd - $rsaDecryptionMemoryStart;
    $desDecryptionMemory = $desDecryptionMemoryEnd - $desDecryptionMemoryStart;

    echo "Memory usage for RSA encryption: " . $rsaEncryptionMemory . " bytes\n";
    echo "Memory usage for DES encryption: " . $desEncryptionMemory . " bytes\n";
    echo "Memory usage for RSA decryption: " . $rsaDecryptionMemory . " bytes\n";
    echo "Memory usage for DES decryption: " . $desDecryptionMemory . " bytes\n";

    // Verify decrypted data
    if ($decryptedData === $plaintext) {
        echo "Decryption successful!\n";
    } else {
        echo "Decryption failed!\n"; // This should ideally never happen with correct decryption.
    }
} else {
    echo "Failed to decrypt DES key.\n"; // Handle RSA decryption errors.
}
?>
