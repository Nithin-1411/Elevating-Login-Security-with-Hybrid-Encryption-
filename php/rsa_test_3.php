<?php
include '../phpqrcode/qrlib.php'; // Include the provided qrlib.php

require '../vendor/autoload.php';
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;

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

// Load the RSA private key
$rsaPrivate = PublicKeyLoader::loadPrivateKey($privateKeyData);

// Load the RSA public key
$rsaPublic = PublicKeyLoader::loadPublicKey($publicKeyData);

// Data to encrypt
$data = "Hello, world!";

// Encrypt with public key
$encrypted = $rsaPublic->encrypt($data);
echo "Encrypted: $encrypted\n";

// Decrypt with private key
$decrypted = $rsaPrivate->decrypt($encrypted);
echo "Decrypted: $decrypted\n";

 $data2 = $encrypted;

// QR code image filename
$filename = '../images/qrcode.png';

// Error correction level (L, M, Q, H)
$errorCorrectionLevel = 'L';

// Pixel size of each module in the QR code
$pixel_Size = 10;

// Generate QR code image
QRcode::png($data2, $filename, $errorCorrectionLevel, $pixel_Size, 2); // Save to file

// Display QR code image
echo '<img src="' . $filename . '" /><br>';

// Get QR code matrix data as a string 
$qrCodeText = QRcode::text($data, false, $errorCorrectionLevel);

// Display QR code text representation
echo '<pre>';
echo $qrCodeText; // Directly echo the string representation of the matrix
echo '</pre>';
?>
