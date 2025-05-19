<?php
    session_start();
    require_once 'config.php';

    // Redirect to login if the user is not logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Fetch user data from session
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];

    // Count accepted listings submitted by this user
    $saved_count_query = "SELECT COUNT(*) AS saved_count FROM user_listings WHERE user_id = '$user_id' AND status = 'accepted'";
    $saved_count_result = mysqli_query($connect, $saved_count_query);
    $saved_count = 0;
    if ($saved_count_result && $row = mysqli_fetch_assoc($saved_count_result)) {
        $saved_count = (int)$row['saved_count'];
    }

    // Fetch user's own application actions (accept/decline) for their listings from user_transactions
    $my_applications = [];
    $my_applications_query = "SELECT ut.*, ul.title AS listing_title 
                             FROM user_transactions ut 
                             JOIN user_listings ul ON ut.listing_id = ul.id 
                             WHERE ut.user_id = '$user_id'
                             ORDER BY ut.created_at DESC";
    $my_applications_result = mysqli_query($connect, $my_applications_query);
    if ($my_applications_result && mysqli_num_rows($my_applications_result) > 0) {
        while ($row = mysqli_fetch_assoc($my_applications_result)) {
            $my_applications[] = $row;
        }
    }
    $applications_count = count($my_applications);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Apartment Finder</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <div class="user-menu-item active" data-section="dashboard">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </div>
            <div class="user-menu-item" data-section="saved">
                <i class="fas fa-heart"></i> Saved Properties
            </div>
            <div class="user-menu-item" data-section="applications">
                <i class="fas fa-file-alt"></i> My Applications
            </div>
            
            <div class="user-menu-item" data-section="submit-listing">
                <i class="fas fa-plus"></i> Submit Listing
            </div>
            <div class="user-menu-item">
                <a href="index.php" style="color: inherit; text-decoration: none;">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
            <div class="user-menu-item">
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
                            <div class="stat-number" id="saved-count"><?php echo $saved_count; ?></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Active Applications</h3>
                            <div class="stat-number" id="applications-count"><?php echo $applications_count; ?></div>
                        </div>
                    </div>
                </div>

               
            </div>

            <!-- Saved Properties Section -->
            <div class="admin-section" id="saved-section">
                <h2>Saved Properties</h2>
                <div class="listings-grid" id="saved-properties-grid">
                    <?php
                    // Fetch listings submitted by this user and accepted by admin
                    $query = "SELECT * FROM user_listings WHERE user_id = '$user_id' AND status = 'accepted' ORDER BY created_at DESC";
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
                        <p class="no-data">No saved properties found. When your listing request is accepted by the admin, it will appear here.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Applications Section -->
            <div class="admin-section" id="applications-section">
                <h2>My Applications</h2>
                <div class="data-table-container" id="applications-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Property</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="applications-tbody">
                            <?php
                            // Modified query with more precise date format and explicit sorting
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

            <!-- Submit Listing Section -->
            <div class="admin-section" id="submit-listing-section">
                <h2>Submit a Listing Request</h2>
                <form method="POST" action="../backend/handle_user_listings.php" enctype="multipart/form-data">
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
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="auth.js"></script>
    <script src="script.js"></script>
    <script>
        // Notification function (same as in script.js)
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

        document.addEventListener('DOMContentLoaded', function() {
            // Set up navigation
            const menuItems = document.querySelectorAll('.user-menu-item');
            const sections = document.querySelectorAll('.admin-section');

            menuItems.forEach(item => {
                if (!item.hasAttribute('data-section')) return;

                item.addEventListener('click', function() {
                    const sectionName = this.getAttribute('data-section');
                    
                    menuItems.forEach(mi => mi.classList.remove('active'));
                    this.classList.add('active');

                    sections.forEach(section => {
                        if (section.id === `${sectionName}-section`) {
                            section.classList.add('active');
                        } else {
                            section.classList.remove('active');
                        }
                    });
                });
            });

            // Intercept submit listing form to show notification
            const submitListingForm = document.querySelector('#submit-listing-section form');
            if (submitListingForm) {
                submitListingForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(submitListingForm);
                    fetch(submitListingForm.action, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(result => {
                        // Show notification regardless of backend response
                        showNotification('Listing request submitted successfully!', 'info');
                        submitListingForm.reset();
                    })
                    .catch(() => {
                        showNotification('Failed to submit listing request.', 'error');
                    });
                });
            }

            // Function to refresh applications table
            function refreshApplicationsTable() {
                fetch('get_user_applications.php')
                    .then(response => response.text())
                    .then(html => {
                        const tbody = document.getElementById('applications-tbody');
                        if (tbody) {
                            tbody.innerHTML = html;
                        }
                    });
            }

            // Refresh every 30 seconds
            setInterval(refreshApplicationsTable, 30000);

            // Also refresh when the applications section becomes visible
            document.querySelector('[data-section="applications"]').addEventListener('click', refreshApplicationsTable);
        });
    </script>
    <style>
        /* Minimal notification style if not present */
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
</body>
</html>