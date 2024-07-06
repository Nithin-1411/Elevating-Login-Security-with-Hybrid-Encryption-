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

// Benchmarking: RSA encryption of Blowfish key
$rsaEncryptionStartTime = microtime(true);
$rsaEncryptionMemoryStart = memory_get_usage();
$encryptedBlowfishKey = $publicKey->encrypt($blowfishKey);
$rsaEncryptionMemoryEnd = memory_get_usage();
$rsaEncryptionEndTime = microtime(true);

// Encrypt data with Blowfish
$blowfish = new Blowfish('cbc');  // Use CBC mode
$iv = Random::string($blowfish->getBlockLength() >> 3); // Initialization vector
$blowfish->setKey($blowfishKey);
$blowfish->setIV($iv);

// Benchmarking: Blowfish encryption
$blowfishEncryptionStartTime = microtime(true);
$blowfishEncryptionMemoryStart = memory_get_usage();
$ciphertext = $iv . $blowfish->encrypt($plaintext); 
$blowfishEncryptionMemoryEnd = memory_get_usage();
$blowfishEncryptionEndTime = microtime(true);

// Benchmarking: RSA decryption of Blowfish key
$rsaDecryptionStartTime = microtime(true);
$rsaDecryptionMemoryStart = memory_get_usage();
$decryptedBlowfishKey = $privateKey->decrypt($encryptedBlowfishKey);
$rsaDecryptionMemoryEnd = memory_get_usage();
$rsaDecryptionEndTime = microtime(true);

// Decrypt the data using Blowfish
if ($decryptedBlowfishKey !== false) {
    $blowfish->setKey($decryptedBlowfishKey);
    $blowfish->setIV(substr($ciphertext, 0, $blowfish->getBlockLength() >> 3));

    // Benchmarking: Blowfish decryption
    $blowfishDecryptionStartTime = microtime(true);
    $blowfishDecryptionMemoryStart = memory_get_usage();
    $decryptedData = $blowfish->decrypt(substr($ciphertext, $blowfish->getBlockLength() >> 3));
    $blowfishDecryptionMemoryEnd = memory_get_usage();
    $blowfishDecryptionEndTime = microtime(true);

    // Calculate total time
    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + 
                 ($blowfishEncryptionEndTime - $blowfishEncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) +
                 ($blowfishDecryptionEndTime - $blowfishDecryptionStartTime);

    echo "Total encryption/decryption time (RSA+Blowfish): " . $totalTime * 1000 . " ms\n";

    // Calculate memory usage
    $rsaEncryptionMemory = $rsaEncryptionMemoryEnd - $rsaEncryptionMemoryStart;
    $blowfishEncryptionMemory = $blowfishEncryptionMemoryEnd - $blowfishEncryptionMemoryStart;
    $rsaDecryptionMemory = $rsaDecryptionMemoryEnd - $rsaDecryptionMemoryStart;
    $blowfishDecryptionMemory = $blowfishDecryptionMemoryEnd - $blowfishDecryptionMemoryStart;

    echo "Memory usage for RSA encryption: " . $rsaEncryptionMemory . " bytes\n";
    echo "Memory usage for Blowfish encryption: " . $blowfishEncryptionMemory . " bytes\n";
    echo "Memory usage for RSA decryption: " . $rsaDecryptionMemory . " bytes\n";
    echo "Memory usage for Blowfish decryption: " . $blowfishDecryptionMemory . " bytes\n";

    if ($decryptedData === $plaintext) {
        echo "Decryption successful!\n";
    } else {
        echo "Decryption failed!\n";
    }
} else {
    echo "Failed to decrypt Blowfish key.\n"; 
}
?>
