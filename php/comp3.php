<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\DES;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\TripleDES;
use phpseclib3\Crypt\Blowfish;
use phpseclib3\Crypt\Random;

// Function to generate RSA key pair
function generateRSAKeyPair($bits = 2048) {
    $rsa = RSA::createKey($bits);
    $publicKey = $rsa->getPublicKey();
    $privateKey = $rsa;
    return array('public' => $publicKey, 'private' => $privateKey);
}

// Function to measure time, memory, and security for RSA+DES
function benchmarkRSA_DES($plaintext, $publicKey, $privateKey) {
    $desKey = Random::string(8); // 64-bit key for DES

    $rsaEncryptionStartTime = microtime(true);
    $rsaEncryptionMemoryStart = memory_get_usage();
    $encryptedDesKey = $publicKey->encrypt($desKey);
    $rsaEncryptionMemoryEnd = memory_get_usage();
    $rsaEncryptionEndTime = microtime(true);

    $des = new DES('cbc');
    $iv = Random::string($des->getBlockLength() >> 3); // Initialization vector
    $des->setKey($desKey);
    $des->setIV($iv);

    $desEncryptionStartTime = microtime(true);
    $desEncryptionMemoryStart = memory_get_usage();
    $ciphertext = $iv . $des->encrypt($plaintext);
    $desEncryptionMemoryEnd = memory_get_usage();
    $desEncryptionEndTime = microtime(true);

    $rsaDecryptionStartTime = microtime(true);
    $rsaDecryptionMemoryStart = memory_get_usage();
    $decryptedDesKey = $privateKey->decrypt($encryptedDesKey);
    $rsaDecryptionMemoryEnd = memory_get_usage();
    $rsaDecryptionEndTime = microtime(true);

    $des->setKey($decryptedDesKey);
    $des->setIV(substr($ciphertext, 0, $des->getBlockLength() >> 3));

    $desDecryptionStartTime = microtime(true);
    $desDecryptionMemoryStart = memory_get_usage();
    $decryptedData = $des->decrypt(substr($ciphertext, $des->getBlockLength() >> 3));
    $desDecryptionMemoryEnd = memory_get_usage();
    $desDecryptionEndTime = microtime(true);

    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + 
                 ($desEncryptionEndTime - $desEncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) +
                 ($desDecryptionEndTime - $desDecryptionStartTime);

    $totalMemory = ($rsaEncryptionMemoryEnd - $rsaEncryptionMemoryStart) + 
                   ($desEncryptionMemoryEnd - $desEncryptionMemoryStart) +
                   ($rsaDecryptionMemoryEnd - $rsaDecryptionMemoryStart) +
                   ($desDecryptionMemoryEnd - $desDecryptionMemoryStart);

    // Security score for RSA+DES
    $securityScore = 2; // DES is considered insecure

    return array('time' => $totalTime, 'memory' => $totalMemory, 'security' => $securityScore);
}

// Function to measure time, memory, and security for RSA+AES
function benchmarkRSA_AES($plaintext, $publicKey, $privateKey) {
    $aesKey = Random::string(32); // 256-bit key for AES

    $rsaEncryptionStartTime = microtime(true);
    $rsaEncryptionMemoryStart = memory_get_usage();
    $encryptedAesKey = $publicKey->encrypt($aesKey);
    $rsaEncryptionMemoryEnd = memory_get_usage();
    $rsaEncryptionEndTime = microtime(true);

    $aes = new AES('cbc');
    $iv = Random::string($aes->getBlockLength() >> 3); // Initialization vector
    $aes->setKey($aesKey);
    $aes->setIV($iv);

    $aesEncryptionStartTime = microtime(true);
    $aesEncryptionMemoryStart = memory_get_usage();
    $ciphertext = $iv . $aes->encrypt($plaintext);
    $aesEncryptionMemoryEnd = memory_get_usage();
    $aesEncryptionEndTime = microtime(true);

    $rsaDecryptionStartTime = microtime(true);
    $rsaDecryptionMemoryStart = memory_get_usage();
    $decryptedAesKey = $privateKey->decrypt($encryptedAesKey);
    $rsaDecryptionMemoryEnd = memory_get_usage();
    $rsaDecryptionEndTime = microtime(true);

    $aes->setKey($decryptedAesKey);
    $aes->setIV(substr($ciphertext, 0, $aes->getBlockLength() >> 3));

    $aesDecryptionStartTime = microtime(true);
    $aesDecryptionMemoryStart = memory_get_usage();
    $decryptedData = $aes->decrypt(substr($ciphertext, $aes->getBlockLength() >> 3));
    $aesDecryptionMemoryEnd = memory_get_usage();
    $aesDecryptionEndTime = microtime(true);

    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + 
                 ($aesEncryptionEndTime - $aesEncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) +
                 ($aesDecryptionEndTime - $aesDecryptionStartTime);

    $totalMemory = ($rsaEncryptionMemoryEnd - $rsaEncryptionMemoryStart) + 
                   ($aesEncryptionMemoryEnd - $aesEncryptionMemoryStart) +
                   ($rsaDecryptionMemoryEnd - $rsaDecryptionMemoryStart) +
                   ($aesDecryptionMemoryEnd - $aesDecryptionMemoryStart);

    // Security score for RSA+AES
    $securityScore = 10; // AES-256 is very secure

    return array('time' => $totalTime, 'memory' => $totalMemory, 'security' => $securityScore);
}

// Function to measure time, memory, and security for RSA+TripleDES
function benchmarkRSA_TripleDES($plaintext, $publicKey, $privateKey) {
    $tripleDesKey = Random::string(24); // 192-bit key for Triple DES (168 effective bits)

    $rsaEncryptionStartTime = microtime(true);
    $rsaEncryptionMemoryStart = memory_get_usage();
    $encryptedTripleDesKey = $publicKey->encrypt($tripleDesKey);
    $rsaEncryptionMemoryEnd = memory_get_usage();
    $rsaEncryptionEndTime = microtime(true);

    $tripleDes = new TripleDES('cbc');
    $iv = Random::string($tripleDes->getBlockLength() >> 3); // Initialization vector
    $tripleDes->setKey($tripleDesKey);
    $tripleDes->setIV($iv);

    $tripleDesEncryptionStartTime = microtime(true);
    $tripleDesEncryptionMemoryStart = memory_get_usage();
    $ciphertext = $iv . $tripleDes->encrypt($plaintext);
    $tripleDesEncryptionMemoryEnd = memory_get_usage();
    $tripleDesEncryptionEndTime = microtime(true);

    $rsaDecryptionStartTime = microtime(true);
    $rsaDecryptionMemoryStart = memory_get_usage();
    $decryptedTripleDesKey = $privateKey->decrypt($encryptedTripleDesKey);
    $rsaDecryptionMemoryEnd = memory_get_usage();
    $rsaDecryptionEndTime = microtime(true);

    $tripleDes->setKey($decryptedTripleDesKey);
    $tripleDes->setIV(substr($ciphertext, 0, $tripleDes->getBlockLength() >> 3));

    $tripleDesDecryptionStartTime = microtime(true);
    $tripleDesDecryptionMemoryStart = memory_get_usage();
    $decryptedData = $tripleDes->decrypt(substr($ciphertext, $tripleDes->getBlockLength() >> 3));
    $tripleDesDecryptionMemoryEnd = memory_get_usage();
    $tripleDesDecryptionEndTime = microtime(true);

    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + 
                 ($tripleDesEncryptionEndTime - $tripleDesEncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) +
                 ($tripleDesDecryptionEndTime - $tripleDesDecryptionStartTime);

    $totalMemory = ($rsaEncryptionMemoryEnd - $rsaEncryptionMemoryStart) + 
                   ($tripleDesEncryptionMemoryEnd - $tripleDesEncryptionMemoryStart) +
                   ($rsaDecryptionMemoryEnd - $rsaDecryptionMemoryStart) +
                   ($tripleDesDecryptionMemoryEnd - $tripleDesDecryptionMemoryStart);

    // Security score for RSA+TripleDES
    $securityScore = 6; // TripleDES is reasonably secure but not as secure as AES

    return array('time' => $totalTime, 'memory' => $totalMemory, 'security' => $securityScore);
}

// Function to measure time, memory, and security for RSA+Blowfish
function benchmarkRSA_Blowfish($plaintext, $publicKey, $privateKey) {
    $blowfishKey = Random::string(16); // 128-bit key for Blowfish

    $rsaEncryptionStartTime = microtime(true);
    $rsaEncryptionMemoryStart = memory_get_usage();
    $encryptedBlowfishKey = $publicKey->encrypt($blowfishKey);
    $rsaEncryptionMemoryEnd = memory_get_usage();
    $rsaEncryptionEndTime = microtime(true);

    $blowfish = new Blowfish('cbc');
    $iv = Random::string($blowfish->getBlockLength() >> 3); // Initialization vector
    $blowfish->setKey($blowfishKey);
    $blowfish->setIV($iv);

    $blowfishEncryptionStartTime = microtime(true);
    $blowfishEncryptionMemoryStart = memory_get_usage();
    $ciphertext = $iv . $blowfish->encrypt($plaintext);
    $blowfishEncryptionMemoryEnd = memory_get_usage();
    $blowfishEncryptionEndTime = microtime(true);

    $rsaDecryptionStartTime = microtime(true);
    $rsaDecryptionMemoryStart = memory_get_usage();
    $decryptedBlowfishKey = $privateKey->decrypt($encryptedBlowfishKey);
    $rsaDecryptionMemoryEnd = memory_get_usage();
    $rsaDecryptionEndTime = microtime(true);

    $blowfish->setKey($decryptedBlowfishKey);
    $blowfish->setIV(substr($ciphertext, 0, $blowfish->getBlockLength() >> 3));

    $blowfishDecryptionStartTime = microtime(true);
    $blowfishDecryptionMemoryStart = memory_get_usage();
    $decryptedData = $blowfish->decrypt(substr($ciphertext, $blowfish->getBlockLength() >> 3));
    $blowfishDecryptionMemoryEnd = memory_get_usage();
    $blowfishDecryptionEndTime = microtime(true);

    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) + 
                 ($blowfishEncryptionEndTime - $blowfishEncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) +
                 ($blowfishDecryptionEndTime - $blowfishDecryptionStartTime);

    $totalMemory = ($rsaEncryptionMemoryEnd - $rsaEncryptionMemoryStart) + 
                   ($blowfishEncryptionMemoryEnd - $blowfishEncryptionMemoryStart) +
                   ($rsaDecryptionMemoryEnd - $rsaDecryptionMemoryStart) +
                   ($blowfishDecryptionMemoryEnd - $blowfishDecryptionMemoryStart);

    // Security score for RSA+Blowfish
    $securityScore = 7; // Blowfish is reasonably secure but not as secure as AES

    return array('time' => $totalTime, 'memory' => $totalMemory, 'security' => $securityScore);
}

// Plaintext data
$plaintext = "This is sensitive data to be encrypted.";

// Generate RSA key pair
$keys = generateRSAKeyPair();
$publicKey = $keys['public'];
$privateKey = $keys['private'];

// Run benchmarks
$results = array(
    'RSA+DES' => benchmarkRSA_DES($plaintext, $publicKey, $privateKey),
    'RSA+AES' => benchmarkRSA_AES($plaintext, $publicKey, $privateKey),
    'RSA+TripleDES' => benchmarkRSA_TripleDES($plaintext, $publicKey, $privateKey),
    'RSA+Blowfish' => benchmarkRSA_Blowfish($plaintext, $publicKey, $privateKey),
);

// Generate graph
$data = array(
    'algorithms' => array_keys($results),
    'times' => array_map(function($result) { return $result['time']; }, $results),
    'memories' => array_map(function($result) { return $result['memory']; }, $results),
    'securities' => array_map(function($result) { return $result['security']; }, $results),
);

file_put_contents('data2.json', json_encode($data));
?>
