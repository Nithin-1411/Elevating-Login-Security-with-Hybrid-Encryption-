<?php
require '../vendor/autoload.php';
use phpseclib3\Crypt\RSA;

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate RSA keys
function generateRSAKeys() {

    $rsa = RSA::createKey();
    $publicKey = $rsa->getPublicKey();
    $privateKey = $rsa;
    return array('public' => $publicKey, 'private' => $privateKey);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch values from the form
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Generate RSA keys
    $keys = generateRSAKeys();
    $publicKey = $keys['public'];
    $privateKey = $keys['private'];

    // Insert data into the database
    $sql = "INSERT INTO user_cred (fullname, email, password, publicKey, privateKey) VALUES ('$fullname', '$email', '$password', '$publicKey', '$privateKey')";

    if ($conn->query($sql) === TRUE) {
        echo "Record inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
