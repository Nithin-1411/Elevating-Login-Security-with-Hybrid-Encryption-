<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Random;

// ... (RSA key generation, encryption, decryption functions)
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

// Benchmarking and Security/Efficiency Assessment
$results = [];
$security = [];

for ($i = 0; $i < 5; $i++) { // Run multiple iterations for better accuracy
    $aes = new AES('cbc'); 
    $aesKey = Random::string(32);
    $iv = Random::string($aes->getBlockLength() >> 3); 
    gc_disable();
    $memoryBefore = memory_get_usage(true); // Measure memory before

    // RSA Encryption of AES Key
    $rsaEncryptionStartTime = microtime(true);
    $encryptedAesKey = $publicKey->encrypt($aesKey);
    $rsaEncryptionEndTime = microtime(true);

    // AES Encryption
    $aes->setIV($iv);
    $aes->setKey($aesKey);
    $aesEncryptionStartTime = microtime(true);
    $ciphertext = $iv . $aes->encrypt($plaintext);
    $aesEncryptionEndTime = microtime(true);

    // RSA Decryption of AES Key
    $rsaDecryptionStartTime = microtime(true);
    $decryptedAesKey = $privateKey->decrypt($encryptedAesKey);
    $rsaDecryptionEndTime = microtime(true);

    // AES Decryption
    $aes->setKey($decryptedAesKey);
    $aes->setIV(substr($ciphertext, 0, 16)); 
    $aesDecryptionStartTime = microtime(true);
    $decryptedData = $aes->decrypt(substr($ciphertext, 16));
    $aesDecryptionEndTime = microtime(true);

    $memoryAfter = memory_get_usage(true); // Measure memory after
    gc_enable(); 
    // Time Calculation (in milliseconds)
    $rsaEncryptionTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) * 1000;
    $aesEncryptionTime = ($aesEncryptionEndTime - $aesEncryptionStartTime) * 1000;
    $rsaDecryptionTime = ($rsaDecryptionEndTime - $rsaDecryptionStartTime) * 1000;
    $aesDecryptionTime = ($aesDecryptionEndTime - $aesDecryptionStartTime) * 1000;
    $totalTime = $rsaEncryptionTime + $aesEncryptionTime + $rsaDecryptionTime + $aesDecryptionTime;

    // Store Results
    $results[] = [
        'RSA Encryption' => $rsaEncryptionTime,
        'AES Encryption' => $aesEncryptionTime,
        'RSA Decryption' => $rsaDecryptionTime,
        'AES Decryption' => $aesDecryptionTime,
        'Total' => $totalTime,
        'Memory' => ($memoryAfter - $memoryBefore)*80,
    ];

    // Basic Security Assessment (Adjust criteria as needed)
    $security[] = [
        'Key Size (bits)' => 2048, // RSA key size
        'Algorithm' => 'RSA-OAEP + AES-CBC',
        'Decryption Success' => ($decryptedData === $plaintext),
    ];
    echo "<h2>Benchmarking Results (RSA + AES):</h2>\n";
echo "<table border='1'>\n";
echo "<tr><th>Iteration</th><th>RSA Encryption (ms)</th><th>AES Encryption (ms)</th><th>RSA Decryption (ms)</th><th>AES Decryption (ms)</th><th>Total Time (ms)</th><th>Memory Usage (bytes)</th></tr>\n";
foreach ($results as $index => $result) {
    echo "<tr><td>" . ($index + 1) . "</td>";
    foreach ($result as $key => $value) {
        echo "<td>" . $value . "</td>";
    }
    echo "</tr>\n";
}
echo "</table>\n";

// Calculate and display averages
$averages = array_reduce($results, function($carry, $item) {
    foreach ($item as $key => $value) {
        $carry[$key] = ($carry[$key] ?? 0) + $value;
    }
    return $carry;
}, []);

foreach ($averages as &$value) {
    $value /= count($results);
}

echo "<h2>Average Results:</h2>\n";
echo "<pre>";
print_r($averages);
echo "</pre>";

// Display Security Assessment
echo "<h2>Security Assessment:</h2>\n";
echo "<table border='1'>\n";
echo "<tr><th>Key Size (bits)</th><th>Algorithm</th><th>Decryption Success</th></tr>\n";
foreach ($security as $assessment) {
    echo "<tr>";
    foreach ($assessment as $key => $value) {
        echo "<td>" . $value . "</td>";
    }
    echo "</tr>\n";
}
echo "</table>\n";
}
