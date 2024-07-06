<?php

// Ensure the GMP extension is installed and enabled
if (!extension_loaded('gmp')) {
    die('GMP extension is required for ElGamal.');
}

// ElGamal Key Generation
function generateElGamalKeyPair($prime, $generator) {
    $privateKey = gmp_random_range("1", gmp_sub($prime, "2")); // Secure random number
    $publicKey = gmp_powm($generator, $privateKey, $prime);
    return array('public' => $publicKey, 'private' => $privateKey);
}

// ElGamal Encryption
// ElGamal Encryption
function elgamalEncrypt($plaintext, $publicKey, $prime, $generator) {
    $randomValue = gmp_random_range("1", gmp_sub($prime, "2")); // Secure random number
    $sharedKey = gmp_powm($publicKey, $randomValue, $prime);
    $c1 = gmp_powm($generator, $randomValue, $prime);
    
    // Convert plaintext to numeric value before multiplication
    $ptNumeric = gmp_init(bin2hex($plaintext), 16);
    $c2 = gmp_mul($ptNumeric, $sharedKey) % $prime; // Use GMP for modular multiplication
    
    return array(gmp_strval($c1), gmp_strval($c2)); // Convert to strings
}


// ElGamal Decryption
function elgamalDecrypt($ciphertext, $privateKey, $prime) {
    list($c1, $c2) = $ciphertext;
    $c1 = gmp_init($c1, 10); // Use base 10 as it is a numeric string 
    $c2 = gmp_init($c2, 10); // Use base 10 as it is a numeric string 
    $sharedKey = gmp_powm($c1, $privateKey, $prime);
    $plaintext = gmp_mul($c2, gmp_invert($sharedKey, $prime)) % $prime;

    // Convert decrypted value back to bytes from hex before returning
    return hex2bin(gmp_strval($plaintext, 16)); 
}



// --------------- AES Encryption (using shared key) ---------------
// --------------- AES Encryption (using shared key) ---------------
function aesEncrypt($plaintext, $key) {
    $cipher = "aes-256-cbc";
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    
    // Convert the encrypted key to hex before returning for GMP compatibility
    return base64_encode($iv . $ciphertext_raw); 
}

function aesDecrypt($ciphertext, $key) {
    $cipher = "aes-256-cbc";
    $c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($c, 0, $ivlen);
    $ciphertext_raw = substr($c, $ivlen);
    return openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
}


// ------------- Main Program -------------
// Example prime and generator (replace with secure values)
$prime = gmp_init("17");
$generator = gmp_init("3");

// Generate key pairs for sender and receiver
$senderKeys = generateElGamalKeyPair($prime, $generator);
$receiverKeys = generateElGamalKeyPair($prime, $generator);

// Message to encrypt
$plaintext = "This is my secret message";

// ElGamal to exchange AES key
$aesKey = random_bytes(32);  // Generate a secure random AES key
$encryptedKey = elgamalEncrypt(gmp_init(bin2hex($aesKey)), $receiverKeys['public'], $prime, $generator); 

// AES encryption
$ciphertext = aesEncrypt($plaintext, $aesKey);

// -------- Recipient's Side --------
// ElGamal to get AES key
$decryptedKey = elgamalDecrypt($encryptedKey, $receiverKeys['private'], $prime);
$aesKey = hex2bin($decryptedKey); // Convert back to binary

// AES decryption
$decrypted = aesDecrypt($ciphertext, $aesKey);

echo "Decrypted message: " . $decrypted;
