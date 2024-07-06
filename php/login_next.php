<?php
require '../vendor/autoload.php';
use phpseclib3\Crypt\RSA;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['fullname']) && isset($_POST['password'])) {
    $fullname = $_POST["fullname"];
    $password = $_POST["password"];

    // Prepare and execute query to retrieve user details including RSA keys
    $query = "SELECT fullname, publicKey, privateKey FROM user_cred WHERE fullname = '$fullname' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows >= 1) {
        $row = $result->fetch_assoc();

        // Get RSA keys
        $publicKey = $row['publicKey'];
        $privateKey = $row['privateKey'];

        // Create RSA instances
        // $publicKey = new RSA();
        $publicKey = RSA::createKey();
        $publicKey->loadKey($publicKey);

        $privateKey = RSA::createKey();
        // $privateKey = new RSA();
        $privateKey->loadKey($privateKey);

        // Message to encrypt
        $message = "This is a confidential message.";

        // Encrypt message using public key
        $encryptedMessage = $publicKey->encrypt($message);

        // Decrypt message using private key
        $decryptedMessage = $privateKey->decrypt($encryptedMessage);

        echo "Original Message: " . $message . "<br>";
        echo "Encrypted Message: " . base64_encode($encryptedMessage) . "<br>";
        echo "Decrypted Message: " . $decryptedMessage . "<br>";
    } else {
        echo "Invalid credentials.";
    }
}

$conn->close();
?>
