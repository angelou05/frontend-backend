<?php
session_start();
require_once '../frontend/config.php';

// Ensure only admins can access this functionality
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request. Listing ID is missing.']);
    exit();
}

$listingId = $data['id'];

$query = "DELETE FROM featured_listings WHERE id = '$listingId'";
if (mysqli_query($connect, $query)) {
    echo json_encode(['success' => true, 'message' => 'Listing removed successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($connect)]);
}
?>
