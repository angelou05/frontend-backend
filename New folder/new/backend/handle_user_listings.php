<?php
session_start();
require_once '../frontend/config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $area = $_POST['area'];
    $description = $_POST['description'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $uploadDir = '../uploads/';

        // Ensure the uploads directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imagePath = $uploadDir . $imageName;

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($imageTmpPath, $imagePath)) {
            // Save the data to the user_listings table
            $query = "INSERT INTO user_listings (user_id, title, location, price, bedrooms, bathrooms, area, description, image, status) 
                      VALUES ('$user_id', '$title', '$location', '$price', '$bedrooms', '$bathrooms', '$area', '$description', '$imageName', 'pending')";
            if (mysqli_query($connect, $query)) {
                echo "Listing request submitted successfully.";
            } else {
                echo "Database error: " . mysqli_error($connect);
            }
        } else {
            echo "Failed to upload the image.";
        }
    } else {
        echo "No image uploaded or an error occurred.";
    }
} else {
    echo "Invalid request method.";
}
?>
