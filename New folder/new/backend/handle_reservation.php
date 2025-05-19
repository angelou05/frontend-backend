<?php
session_start();
require_once '../frontend/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $listing_id = $_POST['listing_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    $query = "INSERT INTO reservations (listing_id, name, email, phone, message, status) 
              VALUES ('$listing_id', '$name', '$email', '$phone', '$message', 'pending')";
    if (mysqli_query($connect, $query)) {
        echo "Reservation submitted successfully.";
    } else {
        echo "Database error: " . mysqli_error($connect);
    }
} else {
    echo "Invalid request method.";
}
?>
