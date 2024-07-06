<?php

// 1. Image Preparation
$imagePath = '1.png'; // Replace with your image path
$imageData = base64_encode(file_get_contents($imagePath));
$imageType = mime_content_type($imagePath); // Get MIME type 

// 2. Construct Email Data (JSON)
$emailData = [
    "From" => "dayado9282@facais.com",
    "To" => "teropa3219@facais.com",
    "Subject" => "Check out this image!",
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
    "X-Postmark-Server-Token: 6392fd92-87d3-4612-8d20-970b475310ab" // Replace with your token
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
        echo "Email sent successfully! Message ID: " . $response['MessageID'];
    } else {
        echo "Error sending email: " . $response['Message'];
    }
}
