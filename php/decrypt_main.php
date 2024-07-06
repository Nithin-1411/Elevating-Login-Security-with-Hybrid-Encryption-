<!DOCTYPE html>
<html>
<head>
    <title>QR Code Login</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        background-color: #f4f4f4;
        display: flex; 
        flex-direction: column;
        align-items: center;
        justify-content: center; 
        min-height: 100vh;
    }
    h2, h3, p {
        color: #333;
    }
    
    form {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        width: 400px; 
    }
    /* ... your existing styles ... */

    #decrypt-form-container { /* New styles for the form */
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-top: 20px; /* Space between QR code and form */
    }
    #decrypt-form-container label {
        font-weight: bold;
    }

    #decrypt-form-container textarea {
        width: calc(100% - 22px); /* Adjust for padding */
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    #decrypt-form-container input[type="submit"] {
        background-color: #007bff; /* Blue button */
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease; /* Add hover effect */
    }

    #decrypt-form-container input[type="submit"]:hover {
        background-color: #0069d9; /* Darker blue on hover */
    }
    #qrcode {
    /* ... your existing styles ... */
    max-width: 250px;  /* Set the maximum width */
    max-height: 250px; /* Set the maximum height */
    }
    /* ... (rest of your CSS) ... */
    #te {
  font-family: 'Montserrat', sans-serif; /* Trendy, geometric font */
  font-weight: 800;                     /* Extra bold for impact */
  font-size: 3.5em;                      /* Larger for better visibility */
  text-align: center;                    /* Center align */
  color: #2c3e50;                       /* Deep blue for the main text */
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
}

.rotate {
  animation: rotate 5s linear infinite;
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

    .success-message {  
    color: #4CAF50; /* Green color */
    font-weight: bold;
    padding: 10px;
    background-color: #dff0d8;  /* Light green background */
    border-radius: 5px;
    }


    /* ... (rest of the CSS styles are the same as before) */
    </style>
</head>
<body>
<h1 id="te" >Enter code in <span id="countdown" class="rotate">60</span> seconds...</h1>
<?php


include '../phpqrcode/qrlib.php'; 
require '../vendor/autoload.php';
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Random;

// Database connection parameters (same as before)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login credentials (passed from login.php)
if (isset($_POST['fullname']) && isset($_POST['password'])) {
    // echo "<p>Hello.</p>";
    $fullname = $_POST["fullname"];
    $password = $_POST["password"];

    // Fetch user's private key
    $query = "SELECT fullname,email,publicKey,privateKey FROM user_cred WHERE (fullname = '$fullname'  AND password = '$password')";

    $stmt = $conn->prepare($query);
    // $stmt->bind_param("ss", $fullname, $password);
    $stmt->execute();

    // $stmt->store_result();
    $result = $stmt->get_result();

    if ($result->num_rows >= 1) {

        $row = $result->fetch_assoc();
        $publicKeyData = $row['publicKey']; 
        $privateKeyData = $row['privateKey'];
        $fullname=$row['fullname'];
        $email_id=$row['email'];

        // Load RSA keys
        $rsaPrivate = RSA::loadPrivateKey($privateKeyData);
        $rsaPublic = RSA::loadPublicKey($publicKeyData);

        $dataToEncrypt = $fullname;
        // $dataToEncrypt ="hello";

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





                
        // 1. Image Preparation
        $imagePath = '../images/qrcode.png'; // Replace with your image path
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageType = mime_content_type($imagePath); // Get MIME type 

        // 2. Construct Email Data (JSON)
        $emailData = [
            "From" => "bl.en.u4cse21053@bl.students.amrita.edu",
            // "To" => "teropa3219@facais.com",
            "To" => $email_id,
            // "To" => "bl.en.u4cse21043@bl.students.amrita.edu",
            "Subject" => "Scan The Below QR and Copy and Paste the text in the Website!",
            "HtmlBody" => "<html><body><strong>Hello!</strong><br><img src='cid:image1' alt='Image Description'></body></html>",
            "Attachments" => [
                [
                    "Name" => "image1",
                    "Content" => $imageData,
                    "ContentType" => $imageType,
                    "ContentID" => "image1"
                ]
            ],
            "MessageStream" => "outbound"
        ];

        // 3. Send with cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.postmarkapp.com/email");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/json",
            // "X-Postmark-Server-Token: 6392fd92-87d3-4612-8d20-970b475310ab" // Replace with your token
            "X-Postmark-Server-Token: 80b85e11-b61d-4a1c-a5c6-7570f91df3f8"
            
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));

        $server_output = curl_exec($ch);
        curl_close($ch);

        // 4. Handle Response
        if ($server_output === false) {
            echo "cURL Error: " . curl_error($ch);
        } else {
            $response = json_decode($server_output, true);
            if ($response['ErrorCode'] == 0) {
                // echo "Email sent successfully! Message ID: " . $response['MessageID'];
                echo "<p class='success-message'>Email sent successfully! Message ID: " . $response['MessageID'] . "</p>";
            } else {
                echo "Error sending email: " . $response['Message'];
            }
        }



        // Display QR code
        // echo '<img src="' . $filename . '" /><br>';

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

        // echo "Decrypted: $decryptedData\n";

        // Display QR code decryption form
        echo '<div id="decrypt-form-container">'; 
        echo '<h2>Decrypt QR Code Data</h2>';
        echo '<img id="qrcode" , src="https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExdHMyZWN5d3JpenBscGhyZmF0cjlyZWxncDUwcGtyeWVjNnJqYnF1cCZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/KjWDaVqI6waTtJmEXT/giphy.gif" alt="Loading QR code...">';
        echo '<form method="post" action="">'; 
        echo '    <label for="qrCodeData">Enter QR Code Data:</label><br>';
        echo '    <textarea id="qrCodeData" name="qrCodeData" rows="5" cols="50"></textarea><br><br>';
        echo '    <input type="hidden" name="fullname" value="' . htmlspecialchars($fullname) . '">';
        echo '    <input type="hidden" name="password" value="' . htmlspecialchars($password) . '">';
        echo '    <input type="submit" value="Decrypt">';
        echo '</form>';
        echo '</div>'; 

        
    } else {
        echo "<p>Invalid credentials.</p>";
        $conn->close();
        exit; // Stop further processing if credentials are invalid
    }
} else {
    echo "<p>Error: Login credentials not received.</p>"; 
    exit; // Stop if credentials weren't sent
}

// // Display QR code decryption form
// echo '<h2>Decrypt QR Code Data</h2>';
// echo '<form method="post" action="">'; // Submit to the same page
// echo '    <label for="qrCodeData">Enter QR Code Data:</label><br>';
// echo '    <textarea id="qrCodeData" name="qrCodeData" rows="5" cols="50"></textarea><br><br>';
// echo '    <input type="submit" value="Decrypt">';
// echo '</form>';

// QR Decryption Logic (only if QR data is submitted)
if (isset($_POST['qrCodeData'])) {
    try {
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
      // Wrap in a try-catch block to catch decryption errors
        $decryptedData = $aes->decrypt(substr($encryptedData, 16));
    } catch (Exception $e) {
        // Decryption error occurred, redirect to main_page.html
        header("Location: failed_login.html"); // Replace with the actual path
        exit;
    }

    // $decryptedData = $aes->decrypt(substr($encryptedData, 16));

    // Display the decrypted data (if successful)
    if ($decryptedData !== false) {
        echo "<h3>Decrypted Message:</h3>";
        echo "<p>$decryptedData</p>";

        if($decryptedData === $fullname){
            echo "Succesfull";
            header("Location: login_succes.html");
        }
        else{
            header("Location: failed_login.html");
        }
    } else {
        echo "<p>Decryption failed. Please check the QR code data.</p>";
    }
    // ... (rest of the decryption code is the same as before, using $qrCodeData and $rsaPrivate) ...
}

$conn->close(); 
?>
<script>
        let secondsLeft = 60;
        const countdownElement = document.getElementById('countdown');

        function updateCountdown() {
            countdownElement.textContent = secondsLeft;
            if (secondsLeft === 0) {
                window.location.reload();
            } else {
                secondsLeft--;
                setTimeout(updateCountdown, 1000); 
            }
        }
        updateCountdown(); // Start countdown immediately
    </script>