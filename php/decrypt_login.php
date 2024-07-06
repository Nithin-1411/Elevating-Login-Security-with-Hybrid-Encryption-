<!DOCTYPE html>
<html>
<head>
    <title>QR Code Decryption</title>
</head>
<body>
    <h2>Decrypt QR Code Data</h2>
    <form method="post" action="">  
        <label for="qrCodeData">Enter QR Code Data:</label><br>
        <textarea id="qrCodeData" name="qrCodeData" rows="5" cols="50"></textarea><br><br>
        <input type="submit" value="Decrypt">
    </form>

<?php
include '../phpqrcode/qrlib.php'; // Include if needed for other QR code operations

require '../vendor/autoload.php';
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\AES;

// Your RSA private key (keep it secure!)
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
// Load the RSA private key
$rsaPrivate = RSA::loadPrivateKey($privateKeyData);


// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Retrieve the submitted QR code data
    $qrCodeData = $_POST['qrCodeData'];

    // Split the combined QR code data into encrypted data and key
    list($encryptedDataBase64, $encryptedKeyBase64) = explode('|', $qrCodeData);

    // Decode from Base64
    $encryptedData = base64_decode($encryptedDataBase64);
    $encryptedKey = base64_decode($encryptedKeyBase64);

    // Decrypt the AES key
    $decryptedAesKey = $rsaPrivate->decrypt($encryptedKey);

    // Decrypt the data with the AES key (assuming CBC mode)
    $aes = new AES('cbc');
    $aes->setKey($decryptedAesKey);
    $aes->setIV(substr($encryptedData, 0, 16)); // Extract IV
    $decryptedData = $aes->decrypt(substr($encryptedData, 16));

    // Display the decrypted data (if successful)
    if ($decryptedData !== false) {
        echo "<h3>Decrypted Message:</h3>";
        echo "<p>$decryptedData</p>";
    } else {
        echo "<p>Decryption failed. Please check the QR code data.</p>";
    }
}
?>
</body>
</html>
