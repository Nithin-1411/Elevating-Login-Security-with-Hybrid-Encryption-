<?php
require '../vendor/autoload.php';  // Assuming phpseclib is installed

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\DES;
use phpseclib3\Math\BigInteger;

// Function to generate RSA key pair


// Function to securely encrypt data using RSA
function rsaEncrypt($data, $publicKey)
{
    $rsa = new RSA();
    $rsa->loadKey($publicKey);
    $rsa->setPadding(RSA::PADDING_OAEP); // Optimal Asymmetric Encryption Padding

    return $rsa->encrypt($data);
}

// Function to securely decrypt data using RSA
function rsaDecrypt($data, $privateKey)
{
    $rsa = new RSA();
    $rsa->loadKey($privateKey);
    $rsa->setPadding(RSA::PADDING_OAEP);

    return $rsa->decrypt($data);
}

// Function to encrypt data using DES
function desEncrypt($data, $key, $iv)
{
    $des = new DES('cbc');
    $des->setKey($key);
    $des->setIV($iv);

    return $des->encrypt($data);
}

// Function to decrypt data using DES
function desDecrypt($data, $key, $iv)
{
    $des = new DES('cbc');
    $des->setKey($key);
    $des->setIV($iv);

    return $des->decrypt($data);
}

// Generate RSA key pair
$rsa = RSA::createKey();
$publicKey = $rsa->getPublicKey();
$privateKey = $rsa;


// Plaintext data
$plaintext = "This is sensitive data to be encrypted.";

// Generate random DES key and IV (replace with cryptographically secure random bytes)
$desKey = random_bytes(8); // For DES, key size is 8 bytes
$iv = random_bytes(8);     // IV size for DES in CBC mode is also 8 bytes

// Benchmarking: RSA encryption of DES key
$rsaEncryptionStartTime = microtime(true);


$encryptedDesKey = rsaEncrypt($desKey, $publicKey);



$rsaEncryptionEndTime = microtime(true);

// Benchmarking: DES encryption
$desEncryptionStartTime = microtime(true);
$encryptedData = desEncrypt($plaintext, $desKey, $iv);
$desEncryptionEndTime = microtime(true);

// Benchmarking: RSA decryption of DES key
$rsaDecryptionStartTime = microtime(true);
$decryptedDesKey = rsaDecrypt($encryptedDesKey, $privateKey);
$rsaDecryptionEndTime = microtime(true);

// Benchmarking: DES decryption
$desDecryptionStartTime = microtime(true);
$decryptedData = desDecrypt($encryptedData, $decryptedDesKey, $iv);
$desDecryptionEndTime = microtime(true);

// Calculate total time
$totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + ($desEncryptionEndTime - $desEncryptionStartTime) + 
            ($rsaDecryptionEndTime - $rsaDecryptionStartTime) + ($desDecryptionEndTime - $desDecryptionStartTime);

echo "Total encryption/decryption time (RSA+DES): " . $totalTime * 1000 . " ms\n";

// Verify decrypted data
if ($decryptedData === $plaintext) {
    echo "Decryption successful!\n";
} else {
    echo "Decryption failed!\n";
}
