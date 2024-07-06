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



$rsaPrivate = RSA::loadPrivateKey($privateKeyData);

// Encrypted text (assign it to the variable)
$encrypted = "7\����������/���w�^Fr�c3i��[�����1-F�S9A�\��R���қ�з)|A�m\"���br�y�=X����ko�?�����l�է���{�>t�������e�рr����3���������\�h��
��,�����Q�ѓL+ްl��툸��i����f[�TO���%��\�>ω�Fh��{�}����h������,=�������M[��>�qi���������.�煰�M�!鿧S����	w����I���L��q�F�}"; 

// Determine if the data is Base64 encoded and decode if necessary
if (base64_encode(base64_decode($encrypted, true)) === $encrypted) {
    $encrypted = base64_decode($encrypted);
}

// Decrypt the data
$decrypted = $rsaPrivate->decrypt($encrypted);

if ($decrypted !== false) {
    echo "Decrypted: $decrypted\n";
} else {
    echo "Decryption failed. Please check if the data is properly encoded (e.g., Base64) and if the private key is correct.\n";
}



?>
