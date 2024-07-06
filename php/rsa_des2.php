<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\DES;
use phpseclib3\Crypt\Random;

// Function to generate RSA key pair (no changes needed)
function generateRSAKeyPair($bits = 2048)
{
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
    $des = new DES('cbc');
    $desKey = Random::string(8);  // Generate a 8-byte DES key
    $iv = Random::string($des->getBlockLength() >> 3); // IV size for DES in CBC mode

    $memoryBefore = memory_get_peak_usage(true); 

    // ... (RSA and DES encryption/decryption code as before)
    // RSA Encryption of DES Key
    $rsaEncryptionStartTime = microtime(true);
    $encryptedDesKey = $publicKey->encrypt($desKey);
    $rsaEncryptionEndTime = microtime(true);

    // DES Encryption
    $des->setIV($iv);
    $des->setKey($desKey); 
    $desEncryptionStartTime = microtime(true);
    $ciphertext = $iv . $des->encrypt($plaintext); 
    $desEncryptionEndTime = microtime(true);

    // RSA Decryption of DES Key
    $rsaDecryptionStartTime = microtime(true);
    $decryptedDesKey = $privateKey->decrypt($encryptedDesKey);
    $rsaDecryptionEndTime = microtime(true);

    // DES Decryption
    $des->setKey($decryptedDesKey);
    $des->setIV(substr($ciphertext, 0, 8)); // Extract IV for DES (8 bytes)
    $desDecryptionStartTime = microtime(true);
    $decryptedData = $des->decrypt(substr($ciphertext, 8)); // Remove IV from ciphertext
    $desDecryptionEndTime = microtime(true);

    $memoryAfter = memory_get_peak_usage(true); // Measure memory after

    // Time Calculation (in milliseconds)
    $rsaEncryptionTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) * 1000;
    $desEncryptionTime = ($desEncryptionEndTime - $desEncryptionStartTime) * 1000;
    $rsaDecryptionTime = ($rsaDecryptionEndTime - $rsaDecryptionStartTime) * 1000;
    $desDecryptionTime = ($desDecryptionEndTime - $desDecryptionStartTime) * 1000;
    $totalTime = $rsaEncryptionTime + $desEncryptionTime + $rsaDecryptionTime + $desDecryptionTime;

    // Store Results
    $results[] = [
        'RSA Encryption' => $rsaEncryptionTime,
        'DES Encryption' => $desEncryptionTime,
        'RSA Decryption' => $rsaDecryptionTime,
        'DES Decryption' => $desDecryptionTime,
        'Total' => $totalTime,
        'Memory (bits)' => ($memoryAfter - $memoryBefore) * 8,
    ];

    // Basic Security Assessment
    $security[] = [
        'Key Size (bits)' => 2048, 
        'Algorithm' => 'RSA-OAEP + DES-CBC',
        'Decryption Success' => ($decryptedData === $plaintext),
    ];
}


// Display Results
echo "<h2>Benchmarking Results (RSA + DES):</h2>\n";
echo "<table border='1'>\n";
echo "<tr><th>Iteration</th><th>RSA Encryption (ms)</th><th>DES Encryption (ms)</th><th>RSA Decryption (ms)</th><th>DES Decryption (ms)</th><th>Total Time (ms)</th><th>Memory Usage (bits)</th></tr>\n";
foreach ($results as $index => $result) {
    echo "<tr><td>" . ($index + 1) . "</td>";
    foreach ($result as $value) {
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
    foreach ($assessment as $value) {
        echo "<td>" . $value . "</td>";
    }
    echo "</tr>\n";
}
echo "</table>\n";
