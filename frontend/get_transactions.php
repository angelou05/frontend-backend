<?php
require_once 'config.php';

$query = "SELECT t.*, l.title AS listing_title FROM transactions t 
          JOIN featured_listings l ON t.listing_id = l.id 
          ORDER BY t.created_at DESC";
$result = mysqli_query($connect, $query);

if ($result && mysqli_num_rows($result) > 0):
    while ($transaction = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($transaction['listing_title']); ?></td>
            <td><?php echo htmlspecialchars($transaction['name']); ?></td>
            <td><?php echo htmlspecialchars($transaction['email']); ?></td>
            <td><?php echo htmlspecialchars($transaction['phone']); ?></td>
            <td><?php echo htmlspecialchars($transaction['message']); ?></td>
            <td><?php echo ucfirst(htmlspecialchars($transaction['status'])); ?></td>
            <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
        </tr>
    <?php endwhile;
else: ?>
    <tr>
        <td colspan="7">No transactions found.</td>
    </tr>
<?php endif; ?>
