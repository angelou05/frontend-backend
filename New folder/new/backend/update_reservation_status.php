<?php
session_start();
require_once '../frontend/config.php';

// Ensure only admins can access this functionality
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $reservation_id = $data['id'];
    $status = $data['status'];

    // Validate input
    if (empty($reservation_id) || empty($status)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid input.'
        ]);
        exit();
    }

    $updateQuery = "UPDATE reservations SET status = '$status' WHERE id = '$reservation_id'";
    if (mysqli_query($connect, $updateQuery)) {
        if ($status === 'accepted') {
            // Fetch applicant info for notification
            $infoQuery = "SELECT r.name, r.email, l.title FROM reservations r JOIN featured_listings l ON r.listing_id = l.id WHERE r.id = '$reservation_id'";
            $infoResult = mysqli_query($connect, $infoQuery);
            if ($infoResult && $row = mysqli_fetch_assoc($infoResult)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Application accepted.',
                    'user_email' => $row['email'],
                    'user_name' => $row['name'],
                    'listing_title' => $row['title']
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Application accepted, but could not fetch applicant info for notification.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Application status updated.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . mysqli_error($connect)
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>
