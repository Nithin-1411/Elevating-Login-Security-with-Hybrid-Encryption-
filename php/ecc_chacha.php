<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\EC;
use phpseclib3\Crypt\ChaCha20;
use phpseclib3\Crypt\Random;

// Plaintext data
$plaintext = "This is sensitive data to be encrypted.";

// Benchmarking and Security/Efficiency Assessment
$results = [];
$security = [];

for ($i = 0; $i < 5; $i++) {
    // Generate ECC key pairs for Ed25519
    $private1 = EC::createKey('Ed25519');
    $public1 = $private1->getPublicKey();

    $private2 = EC::createKey('Ed25519');
    $public2 = $private2->getPublicKey();

    $memoryBefore = memory_get_peak_usage(true); 

    // ECDH Key Exchange and ChaCha20 Encryption/Decryption
    $startTime = microtime(true);

    // Calculate shared secret using ECDH 
    $sharedSecret1 = $private1->deriveSharedSecret($public2);
    $sharedSecret2 = $private2->deriveSharedSecret($public1);

    if ($sharedSecret1 !== $sharedSecret2){
        throw new \RuntimeException("Shared secrets don't match!");
    }

    // Derive ChaCha20 key and nonce from shared secret (use a KDF)
    $chachaKey = hash_hkdf('sha256', $sharedSecret1, 32); 
    $nonce = hash_hkdf('sha256', $sharedSecret1, 12);   

    // Encryption with ChaCha20
    $chacha20 = new ChaCha20();
    $chacha20->setKey($chachaKey);
    $chacha20->setNonce($nonce);
    $ciphertext = $chacha20->encrypt($plaintext);

    // Decryption with ChaCha20
    $chacha20->setKey($chachaKey);
    $chacha20->setNonce($nonce);
    $decryptedData = $chacha20->decrypt($ciphertext);


    $endTime = microtime(true);
    $memoryAfter = memory_get_peak_usage(true);

    // Time Calculation (in milliseconds)
    $totalTime = ($endTime - $startTime) * 1000; 
    

    // Store Results
    $results[] = [
        'Total' => $totalTime,
        'Memory (bits)' => ($memoryAfter - $memoryBefore) * 8,
    ];

    // Basic Security Assessment (Adjust criteria as needed)
    $security[] = [
        'Key Size (bits)' => $privateKey->getLength(),
        'Algorithm' => 'ECDH (Ed25519) + ChaCha20',
        'Decryption Success' => ($decryptedData === $plaintext),
    ];
}
// Display results

echo "<h2>Benchmarking Results (ECC + ChaCha20):</h2>\n";
echo "<table border='1'>\n";
echo "<tr><th>Iteration</th><th>Total Time (ms)</th><th>Memory Usage (bits)</th></tr>\n";
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
