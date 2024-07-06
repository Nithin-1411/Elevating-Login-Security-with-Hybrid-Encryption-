<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\DES;
use phpseclib3\Crypt\Random;

// Function to generate RSA key pair (no changes needed)
function generateRSAKeyPair($bits = 2048) {
    $rsa = RSA::createKey($bits);
    $publicKey = $rsa->getPublicKey();
    $privateKey = $rsa;
    return array('public' => $publicKey, 'private' => $privateKey);
}

$plaintext = "This is sensitive data to be encrypted.";

// Generate RSA key pair
$keys = generateRSAKeyPair();
$publicKey = $keys['public'];
$privateKey = $keys['private'];

// Benchmarking and Security/Efficiency Assessment
$results = [];
$security = [];

for ($i = 0; $i < 5; $i++) {
    $des = new DES('cbc');
    $desKey = Random::string(8);  // Generate a 8-byte DES key 
    $iv = Random::string($des->getBlockLength() >> 3); // IV size for DES in CBC mode

    // RSA Encryption of DES Key
    $rsaEncryptionStartTime = microtime(true);
    $rsaEncryptionMemoryStart = memory_get_usage();
    $encryptedDesKey = $publicKey->encrypt($desKey);
    $rsaEncryptionMemoryEnd = memory_get_usage();
    $rsaEncryptionEndTime = microtime(true);

    // DES Encryption
    $des->setIV($iv);
    $des->setKey($desKey);
    $desEncryptionStartTime = microtime(true);
    $desEncryptionMemoryStart = memory_get_usage();
    $ciphertext = $iv . $des->encrypt($plaintext);
    $desEncryptionMemoryEnd = memory_get_usage();
    $desEncryptionEndTime = microtime(true);

    // RSA Decryption of DES Key
    $rsaDecryptionStartTime = microtime(true);
    $rsaDecryptionMemoryStart = memory_get_usage();
    $decryptedDesKey = $privateKey->decrypt($encryptedDesKey);
    $rsaDecryptionMemoryEnd = memory_get_usage();
    $rsaDecryptionEndTime = microtime(true);

    // DES Decryption
    $des->setKey($decryptedDesKey);
    $des->setIV(substr($ciphertext, 0, 8)); // Extract IV for DES (8 bytes)
    $desDecryptionStartTime = microtime(true);
    $desDecryptionMemoryStart = memory_get_usage();
    $decryptedData = $des->decrypt(substr($ciphertext, 8)); // Remove IV from ciphertext
    $desDecryptionMemoryEnd = memory_get_usage();
    $desDecryptionEndTime = microtime(true);

    // Calculate total time
    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + 
                 ($desEncryptionEndTime - $desEncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) + 
                 ($desDecryptionEndTime - $desDecryptionStartTime);

    // Calculate memory usage
    $rsaEncryptionMemory = $rsaEncryptionMemoryEnd - $rsaEncryptionMemoryStart;
    $desEncryptionMemory = $desEncryptionMemoryEnd - $desEncryptionMemoryStart;
    $rsaDecryptionMemory = $rsaDecryptionMemoryEnd - $rsaDecryptionMemoryStart;
    $desDecryptionMemory = $desDecryptionMemoryEnd - $desDecryptionMemoryStart;

    $results[] = [
        'total_time_ms' => $totalTime * 1000,
        'rsa_encryption_memory' => $rsaEncryptionMemory,
        'des_encryption_memory' => $desEncryptionMemory,
        'rsa_decryption_memory' => $rsaDecryptionMemory,
        'des_decryption_memory' => $desDecryptionMemory,
        'decryption_success' => $decryptedData === $plaintext
    ];
}

// Display results
foreach ($results as $index => $result) {
    echo "Iteration " . ($index + 1) . ":\n";
    echo "Total encryption/decryption time (RSA+DES): " . $result['total_time_ms'] . " ms\n";
    echo "Memory usage for RSA encryption: " . $result['rsa_encryption_memory'] . " bytes\n";
    echo "Memory usage for DES encryption: " . $result['des_encryption_memory'] . " bytes\n";
    echo "Memory usage for RSA decryption: " . $result['rsa_decryption_memory'] . " bytes\n";
    echo "Memory usage for DES decryption: " . $result['des_decryption_memory'] . " bytes\n";
    echo "Decryption " . ($result['decryption_success'] ? "successful" : "failed") . "!\n\n";
}
?>
