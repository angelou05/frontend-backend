<?php
    session_start();
    require_once 'config.php';

    // Redirect to login if the user is not logged in
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Fetch user data from session
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Apartment Finder</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Add EmailJS script if not already present -->
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script>
      // Initialize EmailJS (only once on the page)
      document.addEventListener('DOMContentLoaded', function() {
        if (window.emailjs && !window.emailjs.__inited) {
          emailjs.init({
            publicKey: "WlE7MJuVyIGIo1SRI"
          });
          window.emailjs.__inited = true;
        }
      });
    </script>
    <style>
        /* Add this CSS to make the sidebar fixed */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            overflow-y: auto;
        }

        .admin-content {
            margin-left: 250px; /* Adjust this to match the sidebar width */
        }
        
        .notification {
            position: fixed;
            top: 30px;
            right: 30px;
            z-index: 9999;
            background: #333;
            color: #fff;
            padding: 16px 32px;
            border-radius: 4px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s, top 0.3s;
        }
        .notification.show {
            opacity: 1;
            pointer-events: auto;
        }
        .notification.info { background: #007bff; }
        .notification.error { background: #dc3545; }
        .notification.success { background: #28a745; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>Apartment Finder</h1>
            </div>
            <div class="auth-buttons">
                <div class="profile-container">
                    <div class="profile-logo">
                        <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                    </div>
                    <div class="profile-name"><?php echo $user_name; ?></div>
                    <div class="profile-dropdown">
                        <div class="profile-dropdown-item">
                            <i class="fas fa-user"></i> Profile
                        </div>
                        <div class="profile-dropdown-item">
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="admin-container">
        <div class="admin-sidebar">
            <div class="admin-menu-item active" data-section="dashboard">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </div>
            <div class="admin-menu-item" data-section="saved">
                <i class="fas fa-heart"></i> Saved Properties
            </div>
            <div class="admin-menu-item" data-section="applications">
                <i class="fas fa-file-alt"></i> My Applications
            </div>
            <div class="admin-menu-item" data-section="profile">
                <i class="fas fa-user"></i> Profile Settings
            </div>
            <div class="admin-menu-item" data-section="add-featured">
                <i class="fas fa-plus"></i> Add Featured Listing
            </div>
            <div class="admin-menu-item" data-section="user-requests">
                <i class="fas fa-user-check"></i> User Listing Requests
            </div>
            <div class="admin-menu-item" data-section="transactions">
                <i class="fas fa-exchange-alt"></i> Transactions
            </div>
            <div class="admin-menu-item">
                <a href="index.php" style="color: inherit; text-decoration: none;">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
            <div class="admin-menu-item">
                <a href="logout.php" style="color: inherit; text-decoration: none;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="admin-content">
            <!-- Dashboard Section -->
            <div class="admin-section active" id="dashboard-section">
                <h2>Welcome, <?php echo $user_name; ?></h2>
                <div class="admin-stats">
                    <div class="stat-card">
                        <div class="stat-icon"> 
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Saved Properties</h3>
                            <div class="stat-number" id="saved-count">0</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Active Applications</h3>
                            <div class="stat-number" id="applications-count">0</div>
                        </div>
                    </div>
                </div>

                <div class="recent-activity">
                    <h3>Recent Activity</h3>
                    <div id="recent-activity-list">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Saved Properties Section -->
            <div class="admin-section" id="saved-section">
                <h2>Featured Listings</h2>
                <div class="listings-grid">
                    <?php
                    $query = "SELECT * FROM featured_listings";
                    $result = mysqli_query($connect, $query);

                    if ($result && mysqli_num_rows($result) > 0):
                        while ($listing = mysqli_fetch_assoc($result)): ?>
                            <div class="listing-card">
                                <div class="listing-image">
                                    <img src="../uploads/<?php echo htmlspecialchars($listing['image']); ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>">
                                    <div class="listing-price">$<?php echo number_format($listing['price'], 2); ?>/month</div>
                                </div>
                                <div class="listing-details">
                                    <h3 class="listing-title"><?php echo htmlspecialchars($listing['title']); ?></h3>
                                    <div class="listing-location">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($listing['location']); ?>
                                    </div>
                                    <div class="listing-features">
                                        <div class="listing-feature">
                                            <i class="fas fa-bed"></i> <?php echo htmlspecialchars($listing['bedrooms']); ?> Beds
                                        </div>
                                        <div class="listing-feature">
                                            <i class="fas fa-bath"></i> <?php echo htmlspecialchars($listing['bathrooms']); ?> Baths
                                        </div>
                                        <div class="listing-feature">
                                            <i class="fas fa-vector-square"></i> <?php echo htmlspecialchars($listing['area']); ?> sq ft
                                        </div>
                                    </div>
                                    <p><?php echo htmlspecialchars($listing['description']); ?></p>
                                </div>
                            </div>
                        <?php endwhile;
                    else: ?>
                        <p class="no-data">No featured listings available at the moment.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Applications Section -->
            <div class="admin-section" id="applications-section">
                <h2>My Applications</h2>
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Property</th>
                                <th>Applicant</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
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
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Profile Settings Section -->
            <div class="admin-section" id="profile-section">
                <h2>Profile Settings</h2>
                <div class="profile-form">
                    <form id="profile-form" method="POST" action="update_profile.php">
                        <div class="form-group">
                            <label for="profile-name">Full Name</label>
                            <input type="text" id="profile-name" name="name" value="<?php echo $user_name; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="profile-email">Email</label>
                            <input type="email" id="profile-email" name="email" value="<?php echo $_SESSION['user_email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="profile-phone">Phone Number</label>
                            <input type="tel" id="profile-phone" name="phone" value="<?php echo $_SESSION['user_phone'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Add Featured Apartment Section -->
            <div class="admin-section" id="add-featured-section">
                <h2>Add Featured Apartment</h2>
                <form method="POST" action="../backend/handle_featured_listings.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="bedrooms">Bedrooms</label>
                        <input type="number" id="bedrooms" name="bedrooms" required>
                    </div>
                    <div class="form-group">
                        <label for="bathrooms">Bathrooms</label>
                        <input type="number" id="bathrooms" name="bathrooms" required>
                    </div>
                    <div class="form-group">
                        <label for="area">Area (sq ft)</label>
                        <input type="number" id="area" name="area" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add Listing</button>
                    </div>
                </form>
            </div>

            <!-- User Listing Requests Section -->
            <div class="admin-section" id="user-requests-section">
                <h2>User Listing Requests</h2>
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>User</th>
                                <th>Date Requested</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="user-requests-table-body">
                            <?php
                            $query = "SELECT ul.*, u.name AS user_name FROM user_listings ul 
                                      JOIN users u ON ul.user_id = u.id 
                                      WHERE ul.status = 'pending'";
                            $result = mysqli_query($connect, $query);

                            if ($result && mysqli_num_rows($result) > 0):
                                while ($request = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($request['title']); ?></td>
                                        <td><?php echo htmlspecialchars($request['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($request['created_at']); ?></td>
                                        <td><?php echo htmlspecialchars($request['status']); ?></td>
                                        <td>
                                            <button class="btn btn-success" onclick="handleRequest(<?php echo $request['id']; ?>, 'accept')">Accept</button>
                                            <button class="btn btn-danger" onclick="handleRequest(<?php echo $request['id']; ?>, 'decline')">Decline</button>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="5">No pending requests.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Transactions Section -->
            <div class="admin-section" id="transactions-section">
                <h2>Transactions</h2>
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Listing</th>
                                <th>Applicant</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT t.*, l.title AS listing_title FROM transactions t 
                                      JOIN featured_listings l ON t.listing_id = l.id";
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
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="auth.js"></script>
    <script src="script.js"></script>
    <script>
        // Notification function (same as user-dashboard)
        function showNotification(message, type = 'default') {
            let notification = document.querySelector('.notification');
            if (!notification) {
                notification = document.createElement('div');
                notification.className = 'notification';
                document.body.appendChild(notification);
            }
            notification.className = `notification ${type}`;
            notification.innerHTML = message;
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // EmailJS notification for user listing requests
        function notifyUserListingRequest(email, name, listing) {
            const templateParams = {
                email: email,
                name: name,
                listing: listing
            };
            // Use your EmailJS service/template for listing requests
            emailjs.send("service_wn8muwd", "template_1gueejj", templateParams)
                .then(() => {
                    showNotification('User notified by email (listing request).', 'success');
                })
                .catch((err) => {
                    showNotification('Failed to send email notification to user.', 'error');
                    console.error('EmailJS error:', err);
                });
        }

        // EmailJS notification for application/reservation acceptance
        function notifyUserApplication(email, name, application) {
            const templateParams = {
                email: email,
                name: name,
                application: application
            };
            // Use your EmailJS service/template for application acceptance
            emailjs.send("service_wn8muwd", "template_j8pgoca", templateParams)
                .then(() => {
                    showNotification('Applicant notified by email.', 'success');
                })
                .catch((err) => {
                    showNotification('Failed to send email notification to applicant.', 'error');
                    console.error('EmailJS error:', err);
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Set up navigation
            const menuItems = document.querySelectorAll('.admin-menu-item');
            const sections = document.querySelectorAll('.admin-section');

            menuItems.forEach(item => {
                if (!item.hasAttribute('data-section')) return;

                item.addEventListener('click', function () {
                    const sectionName = this.getAttribute('data-section');
                    
                    // Remove 'active' class from all menu items and sections
                    menuItems.forEach(mi => mi.classList.remove('active'));
                    sections.forEach(section => section.classList.remove('active'));

                    // Add 'active' class to the clicked menu item and corresponding section
                    this.classList.add('active');
                    document.getElementById(`${sectionName}-section`).classList.add('active');
                });
            });

            // Attach event listeners to action buttons
            const updateButtons = document.querySelectorAll('.update-status');
            updateButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const reservationId = this.getAttribute('data-id');
                    const status = this.getAttribute('data-status');
                    updateReservationStatus(reservationId, status);
                });
            });

            function updateReservationStatus(reservationId, status) {
                fetch('../backend/update_reservation_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: reservationId, status })
                })
                .then(response => response.json())
                .then(data => {
                    showNotification(data.message, data.success ? 'info' : 'error');
                    // Notify applicant if accepted and info present
                    if (data.success && status === 'accepted' && data.user_email && data.user_name && data.listing_title) {
                        notifyUserApplication(data.user_email, data.user_name, data.listing_title);
                    }
                    setTimeout(() => location.reload(), 1200);
                })
                .catch(error => {
                    showNotification('Error updating reservation status.', 'error');
                    console.error('Error updating reservation status:', error);
                });
            }

            // Intercept add featured listing form to show notification
            const addFeaturedForm = document.querySelector('#add-featured-section form');
            if (addFeaturedForm) {
                addFeaturedForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(addFeaturedForm);
                    fetch(addFeaturedForm.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(result => {
                        showNotification('Featured listing added successfully!', 'info');
                        addFeaturedForm.reset();
                        // Optionally reload after a short delay to show the new listing
                        setTimeout(() => location.reload(), 1200);
                    })
                    .catch(() => {
                        showNotification('Failed to add featured listing.', 'error');
                    });
                });
            }
        });

        // For user listing requests
        function handleRequest(requestId, action) {
            fetch(`../backend/handle_admin_requests.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ requestId, action })
            })
            .then(response => response.json())
            .then(data => {
                showNotification(data.message, data.success ? 'info' : 'error');
                // Notify user if accepted and info present
                if (data.success && action === 'accept' && data.user_email && data.user_name && data.listing_title) {
                    notifyUserListingRequest(data.user_email, data.user_name, data.listing_title);
                }
                setTimeout(() => location.reload(), 1200);
            })
            .catch(error => {
                showNotification('Error processing request.', 'error');
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>