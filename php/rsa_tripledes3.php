<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\TripleDES;
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

// Benchmarking: RSA encryption of Triple DES key
$rsaEncryptionStartTime = microtime(true);
$rsaEncryptionMemoryStart = memory_get_usage();
$encryptedTripleDesKey = $publicKey->encrypt($tripleDesKey);
$rsaEncryptionMemoryEnd = memory_get_usage();
$rsaEncryptionEndTime = microtime(true);

// Encrypt data with Triple DES (using CBC mode)
$tripleDes = new TripleDES('cbc'); 
$iv = Random::string($tripleDes->getBlockLength() >> 3); // Initialization vector
$tripleDes->setKey($tripleDesKey);
$tripleDes->setIV($iv);

// Benchmarking: Triple DES encryption
$tripleDesEncryptionStartTime = microtime(true);
$tripleDesEncryptionMemoryStart = memory_get_usage();
$ciphertext = $iv . $tripleDes->encrypt($plaintext);
$tripleDesEncryptionMemoryEnd = memory_get_usage();
$tripleDesEncryptionEndTime = microtime(true);

// Benchmarking: RSA decryption of Triple DES key
$rsaDecryptionStartTime = microtime(true);
$rsaDecryptionMemoryStart = memory_get_usage();
$decryptedTripleDesKey = $privateKey->decrypt($encryptedTripleDesKey);
$rsaDecryptionMemoryEnd = memory_get_usage();
$rsaDecryptionEndTime = microtime(true);

// Decrypt the data using Triple DES
if ($decryptedTripleDesKey !== false) {
    $tripleDes->setKey($decryptedTripleDesKey);
    $tripleDes->setIV(substr($ciphertext, 0, $tripleDes->getBlockLength() >> 3));

    // Benchmarking: Triple DES decryption
    $tripleDesDecryptionStartTime = microtime(true);
    $tripleDesDecryptionMemoryStart = memory_get_usage();
    $decryptedData = $tripleDes->decrypt(substr($ciphertext, $tripleDes->getBlockLength() >> 3));
    $tripleDesDecryptionMemoryEnd = memory_get_usage();
    $tripleDesDecryptionEndTime = microtime(true);

    // Calculate total time
    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + 
                 ($tripleDesEncryptionEndTime - $tripleDesEncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) +
                 ($tripleDesDecryptionEndTime - $tripleDesDecryptionStartTime);

    echo "Total encryption/decryption time (RSA+TripleDES): " . $totalTime * 1000 . " ms\n";

    // Calculate memory usage
    $rsaEncryptionMemory = $rsaEncryptionMemoryEnd - $rsaEncryptionMemoryStart;
    $tripleDesEncryptionMemory = $tripleDesEncryptionMemoryEnd - $tripleDesEncryptionMemoryStart;
    $rsaDecryptionMemory = $rsaDecryptionMemoryEnd - $rsaDecryptionMemoryStart;
    $tripleDesDecryptionMemory = $tripleDesDecryptionMemoryEnd - $tripleDesDecryptionMemoryStart;

    echo "Memory usage for RSA encryption: " . $rsaEncryptionMemory . " bytes\n";
    echo "Memory usage for Triple DES encryption: " . $tripleDesEncryptionMemory . " bytes\n";
    echo "Memory usage for RSA decryption: " . $rsaDecryptionMemory . " bytes\n";
    echo "Memory usage for Triple DES decryption: " . $tripleDesDecryptionMemory . " bytes\n";

    if ($decryptedData === $plaintext) {
        echo "Decryption successful!\n";
    } else {
        echo "Decryption failed!\n";
    }
} else {
    echo "Failed to decrypt Triple DES key.\n"; 
}
?>
