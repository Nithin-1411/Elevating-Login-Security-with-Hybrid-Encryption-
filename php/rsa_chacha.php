<?php
require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\Random;

// Note: We're using a separate library for ChaCha20-Poly1305. Install it:
// composer require paragonie/sodium_compat

use ParagonIE\Sodium\Compat;

// Function to generate RSA key pair (no changes needed)
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

// Generate ChaCha20 key and nonce
$chachaKey = random_bytes(SODIUM_CRYPTO_STREAM_KEYBYTES); 
$nonce = random_bytes(SODIUM_CRYPTO_STREAM_NONCEBYTES);

// Encrypt ChaCha20 key with RSA
$rsaEncryptionStartTime = microtime(true);
$encryptedChachaKey = $publicKey->encrypt($chachaKey);
$rsaEncryptionEndTime = microtime(true);

// Encrypt data with ChaCha20-Poly1305 (authenticated encryption)
$chacha20EncryptionStartTime = microtime(true);
$ciphertext = Compat::crypto_aead_chacha20poly1305_encrypt($plaintext, '', $nonce, $chachaKey);
$chacha20EncryptionEndTime = microtime(true);

// Decryption
$rsaDecryptionStartTime = microtime(true);
$decryptedChachaKey = $privateKey->decrypt($encryptedChachaKey);
$rsaDecryptionEndTime = microtime(true);

if ($decryptedChachaKey !== false) {
    $chacha20DecryptionStartTime = microtime(true);
    $decryptedData = Compat::crypto_aead_chacha20poly1305_decrypt($ciphertext, '', $nonce, $decryptedChachaKey);
    $chacha20DecryptionEndTime = microtime(true);

    // Calculate total time
    $totalTime = ($rsaEncryptionEndTime - $rsaEncryptionStartTime) +
                 ($chacha20EncryptionEndTime - $chacha20EncryptionStartTime) +
                 ($rsaDecryptionEndTime - $rsaDecryptionStartTime) +
                 ($chacha20DecryptionEndTime - $chacha20DecryptionStartTime);

    echo "Total encryption/decryption time (RSA+ChaCha20): " . $totalTime * 1000 . " ms\n";

    if ($decryptedData !== false) {
        echo "Decryption successful!\n";
        echo "Decrypted Message: $decryptedData\n";
    } else {
        echo "Decryption failed!\n";
    }
} else {
    echo "Failed to decrypt ChaCha20 key.\n";
}
