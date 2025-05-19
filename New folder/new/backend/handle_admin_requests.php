<?php
session_start();
require_once '../frontend/config.php';

// Ensure only admins can access this functionality
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$requestId = $data['requestId'];
$action = $data['action'];

header('Content-Type: application/json');

if ($action === 'accept') {
    // Move the listing to the featured_listings table
    $query = "INSERT INTO featured_listings (title, location, price, bedrooms, bathrooms, area, description, image)
              SELECT title, location, price, bedrooms, bathrooms, area, description, image
              FROM user_listings WHERE id = '$requestId'";
    if (mysqli_query($connect, $query)) {
        // Update the status of the user listing
        $updateQuery = "UPDATE user_listings SET status = 'accepted' WHERE id = '$requestId'";
        mysqli_query($connect, $updateQuery);

        // Fetch user info for notification
        $infoQuery = "SELECT ul.title, u.email, u.name FROM user_listings ul JOIN users u ON ul.user_id = u.id WHERE ul.id = '$requestId'";
        $infoResult = mysqli_query($connect, $infoQuery);
        if ($infoResult && $row = mysqli_fetch_assoc($infoResult)) {
            // Return JSON for frontend to trigger EmailJS
            echo json_encode([
                'success' => true,
                'message' => 'Listing accepted and added to featured listings.',
                'user_email' => $row['email'],
                'user_name' => $row['name'],
                'listing_title' => $row['title']
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Listing accepted, but could not fetch user info for notification.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . mysqli_error($connect)
        ]);
    }
} elseif ($action === 'decline') {
    // Update the status of the user listing
    $updateQuery = "UPDATE user_listings SET status = 'declined' WHERE id = '$requestId'";
    if (mysqli_query($connect, $updateQuery)) {
        echo json_encode([
            'success' => true,
            'message' => 'Listing request declined.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . mysqli_error($connect)
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action.'
    ]);
}
?>
