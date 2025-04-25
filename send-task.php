<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the task from the form and sanitize it
    $task = htmlspecialchars($_POST['task']);
    
    // Email settings
    $to = "nyamaokaiser79@gmail.com";
    $subject = "New Task Submission";
    $message = "You have received a new task:\n\n" . $task;
    
    // Additional headers
    $headers = "From: noreply@nyamao.xyz";
    $headers .= "\r\nReply-To: noreply@nyamao.xyz";
    $headers .= "\r\nX-Mailer: PHP/" . phpversion();
    
    // Attempt to send the email
    if (mail($to, $subject, $message, $headers)) {
        // Redirect back to the contact page with success message
        header("Location: index.php?status=success#contact");
        exit;
    } else {
        // Redirect back to the contact page with error message
        header("Location: index.php?status=error#contact");
        exit;
    }
} else {
    // If not a POST request, redirect to the main page
    header("Location: index.php");
    exit;
}
?>