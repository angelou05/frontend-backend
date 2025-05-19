<?php
require_once 'config.php';

$query = "SELECT r.*, l.title AS listing_title FROM reservations r 
          JOIN featured_listings l ON r.listing_id = l.id 
          WHERE r.status = 'pending'";
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
            <td>
                <button class="btn btn-success update-status" data-id="<?php echo $application['id']; ?>" data-status="accepted">Accept</button>
                <button class="btn btn-danger update-status" data-id="<?php echo $application['id']; ?>" data-status="declined">Decline</button>
            </td>
        </tr>
    <?php endwhile;
else: ?>
    <tr>
        <td colspan="7">No applications found.</td>
    </tr>
<?php endif; ?>
