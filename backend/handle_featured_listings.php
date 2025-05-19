<?php
session_start();
require_once '../frontend/config.php';

// Ensure only admins can access this functionality
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
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
            // Save the data to the database
            $query = "INSERT INTO featured_listings (title, location, price, bedrooms, bathrooms, area, description, image) 
                      VALUES ('$title', '$location', '$price', '$bedrooms', '$bathrooms', '$area', '$description', '$imageName')";
            if (mysqli_query($connect, $query)) {
                echo "Listing added successfully.";
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
