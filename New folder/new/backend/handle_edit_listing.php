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
$listingId = $data['id'];
$newTitle = $data['title'];
$newPrice = $data['price'];

$query = "UPDATE featured_listings SET title = '$newTitle', price = '$newPrice' WHERE id = '$listingId'";
if (mysqli_query($connect, $query)) {
    echo json_encode(['success' => true, 'message' => 'Listing updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($connect)]);
}
?>
