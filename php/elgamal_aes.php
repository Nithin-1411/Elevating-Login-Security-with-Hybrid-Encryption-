<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\ElGamal;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Random;

// Function to generate ElGamal key pair
function generateElGamalKeyPair()
{
    $elgamal = new ElGamal();
    extract($elgamal->createKey()); // Extract public and private key components
    return array('public' => $elgamal->getPublicKey(), 'private' => $x);
}

// Plaintext data
$plaintext = "This is sensitive data to be encrypted.";

// Generate ElGamal key pair
$keys = generateElGamalKeyPair();
$publicKey = $keys['public'];
$privateKey = $keys['private'];

// Benchmarking and Security/Efficiency Assessment
$results = [];
$security = [];

for ($i = 0; $i < 5; $i++) {
    $aes = new AES('cbc'); 
    $aesKey = Random::string(32);  // For AES-256

    $iv = Random::string($aes->getBlockLength() >> 3); 

    $memoryBefore = memory_get_peak_usage(true); // Measure memory before

    // ElGamal Encryption of AES Key
    $elgamalEncryptionStartTime = microtime(true);
    $elgamal = new ElGamal();
    $elgamal->loadKey($publicKey);
    $encryptedAesKey = $elgamal->encrypt($aesKey);
    $elgamalEncryptionEndTime = microtime(true);

    // AES Encryption
    $aes->setIV($iv);
    $aes->setKey($aesKey);
    $aesEncryptionStartTime = microtime(true);
    $ciphertext = $iv . $aes->encrypt($plaintext);
    $aesEncryptionEndTime = microtime(true);

    // ElGamal Decryption of AES Key
    $elgamalDecryptionStartTime = microtime(true);
    $elgamal->loadKey($privateKey, ElGamal::PRIVATE_KEY_X); // Load private key as 'x' component
    $decryptedAesKey = $elgamal->decrypt($encryptedAesKey);
    $elgamalDecryptionEndTime = microtime(true);

    // AES Decryption
    $aes->setKey($decryptedAesKey);
    $aes->setIV(substr($ciphertext, 0, 16)); 
    $aesDecryptionStartTime = microtime(true);
    $decryptedData = $aes->decrypt(substr($ciphertext, 16));
    $aesDecryptionEndTime = microtime(true);

    $memoryAfter = memory_get_peak_usage(true);

    // Time Calculation (in milliseconds)
    $elgamalEncryptionTime = ($elgamalEncryptionEndTime - $elgamalEncryptionStartTime) * 1000;
    $aesEncryptionTime = ($aesEncryptionEndTime - $aesEncryptionStartTime) * 1000;
    $elgamalDecryptionTime = ($elgamalDecryptionEndTime - $elgamalDecryptionStartTime) * 1000;
    $aesDecryptionTime = ($aesDecryptionEndTime - $aesDecryptionStartTime) * 1000;
    $totalTime = $elgamalEncryptionTime + $aesEncryptionTime + $elgamalDecryptionTime + $aesDecryptionTime;

    // Store Results
    $results[] = [
        'ElGamal Encryption' => $elgamalEncryptionTime,
        'AES Encryption' => $aesEncryptionTime,
        'ElGamal Decryption' => $elgamalDecryptionTime,
        'AES Decryption' => $aesDecryptionTime,
        'Total' => $totalTime,
        'Memory (bits)' => ($memoryAfter - $memoryBefore) * 8,
    ];

    // Basic Security Assessment (Adjust criteria as needed)
    $security[] = [
        'Key Size (bits)' => $elgamal->getKeyLength(),  
        'Algorithm' => 'ElGamal + AES-CBC',
        'Decryption Success' => ($decryptedData === $plaintext),
    ];
}
// Display results (as shown in previous responses)
// ...
