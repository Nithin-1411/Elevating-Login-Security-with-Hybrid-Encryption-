<?php
include '../phpqrcode/qrlib.php';

require '../vendor/autoload.php';
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Random;

// ... (your RSA key data) ...

$privateKeyData = "-----BEGIN RSA PRIVATE KEY-----
MIIEpQIBAAKCAQEA3XkFvAK2oH/RZbfOZztKpyy/wGFNcCrY2pIjEc4DXflXvwQc
tXxFf0/Hv59m7WVLL1VdxxDbSY7n6Ft7crGhMVJ4FlGOec4upX4BSkkxit6k3Epn
Kih3+7zRzRTFDcVG6A4Fgipbnqcl+NDSGNtsaCqf7l8mfMchSMGuf8BaCPCCOIJF
a4KzQdoWzX+FJjOSw4PX4JrswVoLBgMjaONl+JhbZe1JK9UUQgA94bhSvh3bVijQ
TjllQLo3cNELVquV8fFMT9K1j9D38/GM/0jclbJMsZBzqX108D6oMXj3c+Gxk2Ro
SZ4DrifZPilRWjJFrxEs21N3G7wvFe45O9hFcwIDAQABAoIBAFF5DOf+I4rlmnWN
6YtGX48iQiQWZ3Nk/8NgUTBilhCDAomNjLYi0jokcjoG/MlGIyN1hvsNM25a+Osc
hExMFh1b1jt3oy8h7z0IHpdiV6xbfVBgupTisEjWTGPiSR9ovE3voo0g4fV6Tdi7
kR590eJ8E7uQG0t5wb/PKE9sxnJUwFvrLpQpOpZZUuifRKlN32lrkCAyMM2LwpKT
f045uJgB1pn7zzoHPRCmFQ2yr6RYUqnF7mGEvM0gCIP7RcMt/4xRtya/629afzaf
y3l2AfcJJldkVFtkhdA1PL3pKhx4RY1pIAYA7X4Z55BiDSu55mzxgnO9/ztokfVT
CKBoW8ECgYEA+h9vApg2+GXfkPd3xTblwpoac6Ztx28B5pxzzyKcv3rgDrN9OyIK
2uXUHni9lzzZcj4AJafhoZGUwd/iLWTCa3NX0p6Dyaft4YNNicf4LNCCm1rDM4Vk
psP/rR3Mev3vkwo4SdO+VqDkIEUKtkPnLTRW6fanA7CAmcG4cvVMEmECgYEA4q0/
9Zq7wdRYSqDRwzhtcBf3QjrEgv1em4w1BUa4v1dUZRQvzOgM2pIHC9e2F1qosOUv
HEBvt1jUrY6ZaZgJ8rD1wVdGFcLEBc9ruSDlAB6aXJD5G301SCZUvQ2O/OfACt+0
l9wWXcYzo6CDQ0OqSdB44R2wnRIaiOrHlmRwUFMCgYEAs8L1qJq3Hy0wUWLZzH5Z
ANR6YFXjnirU5eXZgavxldh8wXQUc/RScuIs4j2KnPBZozaWKnoOum0DyZbbWh6J
cGo0WyYbt0meuK4CgxyZ67NrurWvhw6+uPAyiDvbYyWwEjCNJdoteNsnOV4Fhdsl
gaKVOI9Rl2A5/MdyhgRvyOECgYEAnarmTx6WtKbP1vDvlrucG5j/6nhYEcjM0qL+
ijH79VN4J2qYKQ3LSwjBj/I1/R6ZMZnoX91OF5jZLfN/MByJ5t5Bwn7DCAXWNThm
dHm175RZyEMqkn1P32SSEoHo0G8efDHIOsziXc2sQ4c1ZkcdnoQ8YFE3dLmP7WZo
92nbWXMCgYEA518Hti4nWQi0eViklAgdmOwHY2TWqVowKkBjLwCYz7F2QMa9MaKk
owqJ/IyTYblEbDBLqnLSC5xqXfBr7KaLCi0Sf9yidHzHbTLfdWro2G//a7IzCk0b
m5OFOQ7AcFQOqCHvCOQpa4D0VBhIBHXiwwnUlhL8/Dd9c6bCsRG8wH0=
-----END RSA PRIVATE KEY-----";


$publicKeyData = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA3XkFvAK2oH/RZbfOZztK
pyy/wGFNcCrY2pIjEc4DXflXvwQctXxFf0/Hv59m7WVLL1VdxxDbSY7n6Ft7crGh
MVJ4FlGOec4upX4BSkkxit6k3EpnKih3+7zRzRTFDcVG6A4Fgipbnqcl+NDSGNts
aCqf7l8mfMchSMGuf8BaCPCCOIJFa4KzQdoWzX+FJjOSw4PX4JrswVoLBgMjaONl
+JhbZe1JK9UUQgA94bhSvh3bVijQTjllQLo3cNELVquV8fFMT9K1j9D38/GM/0jc
lbJMsZBzqX108D6oMXj3c+Gxk2RoSZ4DrifZPilRWjJFrxEs21N3G7wvFe45O9hF
cwIDAQAB
-----END PUBLIC KEY-----";
// Load the RSA keys
$rsaPrivate = RSA::loadPrivateKey($privateKeyData);
$rsaPublic = RSA::loadPublicKey($publicKeyData);

// Data to encrypt
$dataToEncrypt = "Your secret message or longer text goes here!";

// Generate a random AES key
$aesKey = Random::string(32);  

// Encrypt the data with AES
$aes = new AES('cbc'); 
$aes->setKey($aesKey);
$iv = Random::string($aes->getBlockLength() >> 3);
$aes->setIV($iv);
$encryptedData = $iv . $aes->encrypt($dataToEncrypt);

// Encrypt the AES key with RSA
$encryptedKey = $rsaPublic->encrypt($aesKey);

// Base64 encode encrypted data and key
$encryptedDataBase64 = base64_encode($encryptedData);
$encryptedKeyBase64 = base64_encode($encryptedKey);

// Combine encrypted data and key (use a separator)
$qrCodeData = $encryptedDataBase64 . '|' . $encryptedKeyBase64;

// QR code settings
$filename = '../images/qrcode.png';
$errorCorrectionLevel = 'L';
$pixel_Size = 10;

// Generate QR code image
QRcode::png($qrCodeData, $filename, $errorCorrectionLevel, $pixel_Size, 2);

// Display QR code
echo '<img src="' . $filename . '" /><br>';

// ------------------
// Decryption Section 
// ------------------
// (Simulating reading the QR code)

// Split the combined QR code data into encrypted data and key
list($encryptedDataBase64, $encryptedKeyBase64) = explode('|', $qrCodeData);

// Decode from Base64
$encryptedData = base64_decode($encryptedDataBase64);
$encryptedKey = base64_decode($encryptedKeyBase64);

// Decrypt the AES key
$decryptedAesKey = $rsaPrivate->decrypt($encryptedKey);

// Decrypt the data with the AES key
$aes->setKey($decryptedAesKey);
$aes->setIV(substr($encryptedData, 0, 16)); // Extract IV
$decryptedData = $aes->decrypt(substr($encryptedData, 16));

echo "Decrypted: $decryptedData\n"; 
?>
