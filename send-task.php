<?php
// Enable error reporting for debugging (but don't display to users)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Function to send clean JSON responses
function sendJsonResponse($success, $message) {
    // Clear any output buffers to ensure clean JSON
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

// Check if autoloader exists
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    sendJsonResponse(false, "Server configuration error. Please contact the administrator.");
}

// Use the autoloader with absolute path for reliability
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Start output buffering to prevent any debug output
ob_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize it
    $task = isset($_POST['task']) ? htmlspecialchars($_POST['task']) : '';
    $contactMethod = isset($_POST['contact-method']) ? htmlspecialchars($_POST['contact-method']) : '';
    
    // Validate required fields
    if (empty($task) || empty($contactMethod)) {
        sendJsonResponse(false, "Please fill in all required fields.");
    }
    
    // Contact information based on method
    $contactInfo = "";
    if ($contactMethod === 'email') {
        $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : false;
        if (!$email) {
            sendJsonResponse(false, "Please provide a valid email address.");
        }
        $contactInfo = "Email: $email";
    } else if ($contactMethod === 'phone') {
        $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
        // Basic phone validation
        if (empty($phone) || !preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
            sendJsonResponse(false, "Please provide a valid phone number.");
        }
        $contactInfo = "Phone: $phone";
    }
    
    // Set up email
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'p9wampered@gmail.com'; // Your Gmail address
        $mail->Password = 'zrfr icnu dvql yujt'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Timeout = 60; // Timeout in seconds
        
        // IMPORTANT: Disable debug output
        $mail->SMTPDebug = 0; // Turn off debugging
        
        // Recipients
        $mail->setFrom('p9wampered@gmail.com', 'Task Submission');
        $mail->addAddress('nyamaokaiser79@gmail.com', 'Gregory Nyamao');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Task Submission - ' . date('Y-m-d H:i:s');
        
        // Email body
        $emailBody = "
        <h2>New Task Submission</h2>
        <p><strong>Contact Method:</strong> $contactMethod</p>
        <p><strong>$contactInfo</strong></p>
        <h3>Task Details:</h3>
        <p>$task</p>
        <p><small>Submitted on: " . date('Y-m-d H:i:s') . "</small></p>
        ";
        
        $mail->Body = $emailBody;
        $mail->AltBody = strip_tags($emailBody);
        
        // Send email
        if($mail->send()) {
            // Success
            sendJsonResponse(true, "Your task has been sent successfully! I'll get in touch with you soon.");
        } else {
            // Failed but no exception was thrown
            error_log("Mailer Error (no exception): " . $mail->ErrorInfo);
            sendJsonResponse(false, "There was a problem sending your task. Please try again later.");
        }
        
    } catch (Exception $e) {
        // Log the error for admin
        error_log("Mailer Exception: " . $e->getMessage());
        sendJsonResponse(false, "Sorry, we couldn't send your message. Please try again later or contact me directly.");
    }
} else {
    // If not a POST request
    sendJsonResponse(false, "Invalid request method.");
}

// Clean up any output buffer before ending
if (ob_get_level()) {
    ob_end_clean();
}
?>