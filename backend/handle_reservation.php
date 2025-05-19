<?php
session_start();
require_once '../frontend/config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Please login to make a reservation.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $listing_id = $_POST['listing_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];
    $created_at = date('Y-m-d H:i:s');

    $query = "INSERT INTO reservations (listing_id, name, email, phone, message, status, created_at) 
              VALUES ('$listing_id', '$name', '$email', '$phone', '$message', 'pending', '$created_at')";
              
    if (mysqli_query($connect, $query)) {
        echo json_encode([
            'success' => true,
            'message' => 'Reservation successfully submitted!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to submit reservation: ' . mysqli_error($connect)
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>
