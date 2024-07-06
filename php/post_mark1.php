<?php

// Import the Postmark Client Class:
require_once('../vendor/autoload.php');
use Postmark\PostmarkClient;

$client = new PostmarkClient("6392fd92-87d3-4612-8d20-970b475310ab");

// Send an email:
$sendResult = $client->sendEmail(
  "dayado9282@facais.com",
  "teropa3219@facais.com",
  "Hello from Postmark!",
  "This is just a friendly 'hello' from your friends at Postmark."
);

?>

