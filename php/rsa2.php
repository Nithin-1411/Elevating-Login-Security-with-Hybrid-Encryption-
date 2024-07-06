<?php

require '../vendor/autoload.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\Hash;

// Generate RSA Key Pair
$rsa = new RSA();
$rsa->setPrivateKeyFormat(RSA::PRIVATE_FORMAT_PKCS1);
$rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_PKCS1);

$privateKey = $rsa->createKey(2048); // Create a 2048-bit RSA key pair
$publicKey = $privateKey->getPublicKey();

// Message to Encrypt
$plaintext = "This is my secret message.";

// Configure OAEP Padding for Encryption
$rsa->setEncryptionMode(RSA::ENCRYPTION_OAEP);
$rsa->setHash(new Hash('sha256')); // You can choose the hash algorithm
$rsa->setMGFHash(new Hash('sha256')); // MGF (Mask Generation Function) hash

// Encryption
$ciphertext = $publicKey->encrypt($plaintext);

// Decryption
$rsa->setEncryptionMode(RSA::ENCRYPTION_OAEP); // Set OAEP for decryption as well
$decryptedText = $privateKey->decrypt($ciphertext);

// Display Results
echo "Original Message: " . $plaintext . "\n";
echo "Ciphertext: " . base64_encode($ciphertext) . "\n"; // Base64 encode for safe transmission
echo "Decrypted Message: " . $decryptedText . "\n";
