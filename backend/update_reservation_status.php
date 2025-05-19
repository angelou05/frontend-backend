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

// Get JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

$id = mysqli_real_escape_string($connect, $data['id']);
$status = mysqli_real_escape_string($connect, $data['status']);

// Start transaction
mysqli_begin_transaction($connect);

try {
    // Update the reservation status
    $update_query = "UPDATE reservations SET status = '$status' WHERE id = '$id'";
    mysqli_query($connect, $update_query);

    // Get reservation details for transaction
    $res_query = "SELECT r.*, f.title as listing_title, f.id as listing_id 
                  FROM reservations r 
                  JOIN featured_listings f ON r.listing_id = f.id 
                  WHERE r.id = '$id'";
    $res_result = mysqli_query($connect, $res_query);
    $reservation = mysqli_fetch_assoc($res_result);

    // Insert into transactions
    $insert_query = "INSERT INTO transactions (listing_id, name, email, phone, message, status, created_at) 
                     VALUES (
                         '{$reservation['listing_id']}',
                         '{$reservation['name']}',
                         '{$reservation['email']}',
                         '{$reservation['phone']}',
                         '{$reservation['message']}',
                         '$status',
                         NOW()
                     )";
    mysqli_query($connect, $insert_query);

    mysqli_commit($connect);

    echo json_encode([
        'success' => true,
        'message' => 'Status updated successfully',
        'user_email' => $reservation['email'],
        'user_name' => $reservation['name'],
        'listing_title' => $reservation['listing_title']
    ]);

} catch (Exception $e) {
    mysqli_rollback($connect);
    echo json_encode([
        'success' => false,
        'message' => 'Error updating status: ' . $e->getMessage()
    ]);
}

mysqli_close($connect);
?>
