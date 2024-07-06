<?php
include '../phpqrcode/qrlib.php'; // Include the provided qrlib.php

// Data to be encoded in the QR code
$data = "https://www.example.com"; 

// QR code image filename
$filename = '../images/qrcode.png';

// Error correction level (L, M, Q, H)
$errorCorrectionLevel = 'L';

// Pixel size of each module in the QR code
$pixel_Size = 10;

// Generate QR code image
QRcode::png($data, $filename, $errorCorrectionLevel, $pixel_Size, 2); // Save to file

// Display QR code image
echo '<img src="' . $filename . '" /><br>';

// Get QR code matrix data as a string 
$qrCodeText = QRcode::text($data, false, $errorCorrectionLevel);

// Display QR code text representation
echo '<pre>';
echo $qrCodeText; // Directly echo the string representation of the matrix
echo '</pre>';
?>
