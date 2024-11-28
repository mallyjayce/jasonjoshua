<?php
// Function to generate a CSRF token
function generateCSRFToken() {
  if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
  }
  return $_SESSION["csrf_token"];
}

// Function to validate the CSRF token
function validateCSRFToken($token) {
  if (!isset($_SESSION["csrf_token"]) || $_SESSION["csrf_token"] !== $token) {
    echo "Invalid CSRF token.";
    exit;
  }
}

// Start the session
session_start();

// Check the CSRF token
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $csrfToken = $_POST["csrf_token"];
  validateCSRFToken($csrfToken);

  // Process the form submission
  $name = trim($_POST["full-name"]);
  $email = trim($_POST["email"]);
  $phone = trim($_POST["phone-number"]);
  $subject = trim($_POST["subject"]);
  $budget = trim($_POST["budget"]);
  $message = trim($_POST["message"]);

  // Validate and sanitize name (only allow letters and spaces)
  if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
    echo "Invalid name.";
    exit;
  }

  // Validate email
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address.";
    exit;
  }

  // Sanitize phone number
  $phone = filter_var($phone, FILTER_SANITIZE_STRING);

  // Sanitize and validate subject
  $subject = filter_var($subject, FILTER_SANITIZE_STRING);
  if (empty($subject)) {
    echo "Please select a subject.";
    exit;
  }

  // Sanitize and validate budget (optional)
  $budget = filter_var($budget, FILTER_SANITIZE_NUMBER_INT);
  if (!empty($budget) && ($budget < 0 || $budget > 1000000)) {
    echo "Invalid budget.";
    exit;
  }

  // Sanitize and validate message
  $message = filter_var($message, FILTER_SANITIZE_STRING);
  if (empty($message)) {
    echo "Please enter a message.";
    exit;
  }

  // Rest of the code to send the email with the sanitized data...
  $to = "donjasonjoshua@yahoo.com"; // Replace with your own email address

  // Prepare the email subject and body
  $subject = "Contact Form Submission - " . $subject;
  $body = "Name: " . $name . "\n";
  $body .= "Email: " . $email . "\n";
  $body .= "Phone: " . $phone . "\n";
  $body .= "Budget: " . $budget . "\n";
  $body .= "Message: " . $message . "\n";

  // Set additional headers
  $headers = "From: " . $email . "\r\n";
  $headers .= "Reply-To: " . $email . "\r\n";

  // Send the email
  if (mail($to, $subject, $body, $headers)) {
    // Email sent successfully
    echo "Thank you for your message. We will get back to you shortly.";
  } else {
    // Failed to send email
    echo "Failed to send email. Please try again later.";
  }
}
?>
