<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$loggedIn = false;
if (isset($_POST['fullname']) && isset($_POST['password'])) {
    
    $fullname = $_POST["fullname"];
    $password = $_POST["password"];


    // Prepare and bind
    $query = "SELECT password FROM sign_up_details WHERE (fullname = '$fullname'  AND password = '$password')";

    $stmt = $conn->prepare($query);
    

    
    $stmt->execute();

    // Store result
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows >= 1) {
        echo "Hello, user!";
    } else {
        echo "Invalid credentials";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
