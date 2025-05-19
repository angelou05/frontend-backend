<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_email'])) {
    exit('Unauthorized');
}

$query = "SELECT r.*, l.title AS listing_title, 
         DATE_FORMAT(r.created_at, '%b %d, %Y at %l:%i %p') as formatted_date,
         UNIX_TIMESTAMP(r.created_at) as timestamp
         FROM reservations r 
         JOIN featured_listings l ON r.listing_id = l.id 
         WHERE r.email = '{$_SESSION['user_email']}'
         ORDER BY r.created_at DESC";

$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) > 0):
    while ($application = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($application['listing_title']); ?></td>
            <td><?php echo htmlspecialchars($application['name']); ?></td>
            <td><?php echo htmlspecialchars($application['email']); ?></td>
            <td><?php echo htmlspecialchars($application['phone']); ?></td>
            <td><?php echo htmlspecialchars($application['message']); ?></td>
            <td><?php echo htmlspecialchars($application['status']); ?></td>
            <td><?php echo htmlspecialchars($application['formatted_date']); ?></td>
        </tr>
    <?php endwhile;
else: ?>
    <tr>
        <td colspan="7">No applications found.</td>
    </tr>
<?php endif; ?>
