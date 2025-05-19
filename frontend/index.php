<?php
session_start(); 
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CasaBlanca - Find Your Perfect Home</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
  <script>
    (function() {
      emailjs.init({
        publicKey: "WlE7MJuVyIGIo1SRI",
        blockHeadless: false,
        limitRate: {
          throttle: 1000,
        }
      });
    })();

    function sendEmail(e) {
      e.preventDefault();
      const btn = e.target.querySelector('button[type="submit"]');
      btn.disabled = true;

      const userName = document.getElementById("name").value;
      const userEmail = document.getElementById("email").value;
      const userMessage = document.getElementById("message").value;

      const templateParams = {
        to_name: "Admin",
        name: userName,
        email: userEmail,
        message: userMessage,
        from_name: userName,
        from_email: userEmail,
        to_email: "angelouausan90@gmail.com",
        reply_to: userEmail,
        subject: "New Contact Form Message",
        company: "CasaBlanca",
        notes: "This message was sent from the contact form"
      };

      console.log('Form Values:', templateParams); // Debug log

      emailjs.send("service_wn8muwd", "template_j8pgoca", templateParams)
        .then((response) => {
          console.log('SUCCESS!', response.status, response.text);
          showNotification("Message sent successfully!", "success");
          document.getElementById("contact-form").reset();
        })
        .catch((err) => {
          console.error("EmailJS Error:", err);
          showNotification("Failed to send message. Please try again.", "error");
        })
        .finally(() => {
          btn.disabled = false;
        });
    }
  </script>
</head>

<body>
  <header>
    <div class="container">
      <div class="logo">
        <h1>CasaBlanca</h1>
      </div>
      <nav>
        <ul>
          <li><a href="index.php" class="active">Home</a></li>
          <li><a href="#featured-listings">Featured</a></li>
          <li><a href="#about-section">About</a></li>
          <li><a href="#contact-section">Contact</a></li>
        </ul>
      </nav>
      <div class="auth-buttons" id="auth-buttons">
        <?php if (isset($_SESSION['user_id'])): ?>
          <div class="profile-container">
            <div class="profile-logo">
              <?= strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
            </div>
            <div class="profile-name"><?= $_SESSION['user_name']; ?></div>
            <div class="profile-dropdown">
              <div class="profile-dropdown-item">
                <a href="<?= ($_SESSION['user_role'] === 'admin') ? 'admin.php' : 'user-dashboard.php'; ?>">
                  <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
              </div>
              <div class="profile-dropdown-item">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
              </div>
            </div>
          </div>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary">Login</a>
          <a href="signup.php" class="btn btn-secondary">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <section class="hero">
    <div class="container">
      <div class="hero-content">
        <h1>Find Your Perfect Home</h1>
        <p>Browse thousands of listings to find your next home</p>
        <div class="search-container">
          <div class="search-box">
            <input type="text" id="search-input" placeholder="Search for properties...">
            <button type="button" class="search-button">
              <i class="fas fa-search"></i>
            </button>
          </div>
          <select id="sort-select">
            <option value="default">Sort by</option>
            <option value="name-asc">Name (A-Z)</option>
            <option value="name-desc">Name (Z-A)</option>
            <option value="price-asc">Price (Low-High)</option>
            <option value="price-desc">Price (High-Low)</option>
          </select>
        </div>
      </div>
    </div>
  </section>

  <section id="featured-listings" class="listings">
    <div class="container">
        <h2>Featured Properties</h2>
        
        <div class="listings-grid" id="featured-listings-grid">
            <?php
            $query = "SELECT * FROM featured_listings";
            $result = mysqli_query($connect, $query);

            if ($result && mysqli_num_rows($result) > 0):
                while ($listing = mysqli_fetch_assoc($result)): ?>
                    <div class="listing-card" onclick="<?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'showListingDetails' : 'openReservationModal'; ?>(<?php echo htmlspecialchars(json_encode($listing)); ?>)">
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
</section>

<!-- Reservation Modal -->
<div id="reservation-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeReservationModal()">&times;</span>
        <h2>Reservation Form</h2>
        <form id="reservation-form" method="POST" action="../backend/handle_reservation.php">
            <input type="hidden" id="listing-id" name="listing_id">
            <div class="form-group">
                <label for="applicant-name">Name</label>
                <input type="text" id="applicant-name" name="name" value="<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="applicant-email">Email</label>
                <input type="email" id="applicant-email" name="email" value="<?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="applicant-phone">Phone</label>
                <input 
                    type="tel" 
                    id="applicant-phone" 
                    name="phone" 
                    pattern="[0-9]{11}"
                    minlength="11"
                    maxlength="11"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    value="<?php echo isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : ''; ?>" 
                    required
                >
                <small class="phone-hint">Phone number must be exactly 11 digits</small>
            </div>  
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit Reservation</button>
            </div>
        </form>
    </div>
</div>

<!-- Add this new modal for admin viewing details -->
<div id="admin-details-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeAdminDetailsModal()">&times;</span>
        <h2>Property Details</h2>
        <div id="admin-property-details">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>
</div>

<script>
    function openReservationModal(listing) {
        const modal = document.getElementById('reservation-modal');
        document.getElementById('listing-id').value = listing.id;
        modal.style.display = 'block';
    }

    function closeReservationModal() {
        const modal = document.getElementById('reservation-modal');
        modal.style.display = 'none';
    }

    function showNotification(message, type) {
        let notification = document.querySelector('.notification');
        if (!notification) {
            notification = document.createElement('div');
            notification.className = 'notification';
            document.body.appendChild(notification);
        }
        notification.className = `notification ${type}`;
        notification.textContent = message;
        notification.classList.add('show');
        setTimeout(() => notification.classList.remove('show'), 3000);
    }

    function showListingDetails(listing) {
        const modal = document.getElementById('admin-details-modal');
        const detailsContainer = document.getElementById('admin-property-details');
        
        const details = `
            <div class="admin-property-info">
                <h3>${listing.title}</h3>
                <p><strong>Location:</strong> ${listing.location}</p>
                <p><strong>Price:</strong> $${listing.price}/month</p>
                <p><strong>Bedrooms:</strong> ${listing.bedrooms}</p>
                <p><strong>Bathrooms:</strong> ${listing.bathrooms}</p>
                <p><strong>Area:</strong> ${listing.area} sq ft</p>
                <p><strong>Description:</strong> ${listing.description}</p>
            </div>
            <div class="admin-property-image">
                <img src="../uploads/${listing.image}" alt="${listing.title}">
            </div>
        `;
        
        detailsContainer.innerHTML = details;
        modal.style.display = 'block';
    }

    function closeAdminDetailsModal() {
        const modal = document.getElementById('admin-details-modal');
        modal.style.display = 'none';
    }

    document.getElementById('reservation-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check if user is admin
        if (<?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'true' : 'false'; ?>) {
            showNotification('Administrators cannot make reservations', 'error');
            return;
        }

        // Check if user is logged in
        if (!<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
            showNotification('Please login to submit a reservation', 'error');
            return;
        }

        const formData = new FormData(this);
        fetch('../backend/handle_reservation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                this.reset();
                setTimeout(() => closeReservationModal(), 1500);
            }
        })
        .catch(error => {
            showNotification('Failed to submit reservation', 'error');
        });
    });

    // Search and sort functionality
    document.getElementById('search-input').addEventListener('input', filterListings);
    document.getElementById('sort-select').addEventListener('change', sortListings);

    function filterListings() {
        const query = document.getElementById('search-input').value.toLowerCase();
        const cards = document.querySelectorAll('.listing-card');
        
        cards.forEach(card => {
            const title = card.querySelector('.listing-title').textContent.toLowerCase();
            if (title.includes(query)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function sortListings() {
        const sortBy = document.getElementById('sort-select').value;
        const grid = document.getElementById('featured-listings-grid');
        const cards = Array.from(grid.children);

        cards.sort((a, b) => {
            let aValue, bValue;

            switch (sortBy) {
                case 'name-asc':
                    aValue = a.querySelector('.listing-title').textContent;
                    bValue = b.querySelector('.listing-title').textContent;
                    return aValue.localeCompare(bValue);
                case 'name-desc':
                    aValue = a.querySelector('.listing-title').textContent;
                    bValue = b.querySelector('.listing-title').textContent;
                    return bValue.localeCompare(aValue);
                case 'price-asc':
                    aValue = parseFloat(a.querySelector('.listing-price').textContent.replace(/[$,/]/g, ''));
                    bValue = parseFloat(b.querySelector('.listing-price').textContent.replace(/[$,/]/g, ''));
                    return aValue - bValue;
                case 'price-desc':
                    aValue = parseFloat(a.querySelector('.listing-price').textContent.replace(/[$,/]/g, ''));
                    bValue = parseFloat(b.querySelector('.listing-price').textContent.replace(/[$,/]/g, ''));
                    return bValue - aValue;
                default:
                    return 0;
            }
        });

        cards.forEach(card => grid.appendChild(card));
    }
</script>

  <section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>List Your Property With Us</h2>
            <p>Reach thousands of potential tenants and find the perfect match for your property.</p>
            <button class="btn btn-secondary" id="list-property-btn">List Your Property</button>
        </div>
    </div>
  </section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const listPropertyBtn = document.getElementById('list-property-btn');

        listPropertyBtn.addEventListener('click', function () {
            <?php if (isset($_SESSION['user_id'])): ?>
                // Redirect based on user role
                const redirectPage = <?= json_encode($_SESSION['user_role'] === 'admin' ? 'admin.php' : 'user-dashboard.php'); ?>;
                window.location.href = redirectPage;
            <?php else: ?>
                // Redirect to login if not logged in
                window.location.href = 'login.php';
            <?php endif; ?>
        });
    });
</script>

  <section id="about-section" class="about">
    <div class="container">
      <h2>About CasaBlanca</h2>
      <div class="about-content">
        <div class="about-text">
          <p>CasaBlanca is your trusted partner in finding the perfect home. We connect property owners with potential tenants, making the rental process smooth and efficient for everyone involved.</p>
          <p>Our platform offers a wide range of listings across various neighborhoods, ensuring you find a place that meets your needs and budget.</p>
        </div>
        <div class="about-stats">
          <div class="stat">
            <h3>1000+</h3>
            <p>Active Listings</p>
          </div>
          <div class="stat">
            <h3>5000+</h3>
            <p>Happy Tenants</p>
          </div>
          <div class="stat">
            <h3>500+</h3>
            <p>Property Owners</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Contact Form Section -->
  <section id="contact-section" class="contact">
    <div class="container">
      <h2>Contact Us</h2>
      <div class="contact-content">
        <div class="contact-form">
          <form id="contact-form" onsubmit="sendEmail(event)">
            <div class="form-group">
              <label for="name">Name</label>
              <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
              <label for="message">Message</label>
              <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Send Message</button>
            </div>
          </form>
        </div>
        <div class="contact-info">
          <div class="info-item">
            <i class="fas fa-map-marker-alt"></i>
            <p>123 Main Street, City, State 12345</p>
          </div>
          <div class="info-item">
            <i class="fas fa-phone"></i>
            <p>(123) 456-7890</p>
          </div>
          <div class="info-item">
            <i class="fas fa-envelope"></i>
            <p>angelouausan05mail.com</p>
          </div>
          <div class="social-links">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3>CasaBlanca</h3>
          <p>Find your perfect home with our easy-to-use property rental service.</p>
        </div>
        <div class="footer-section">
          <h3>Quick Links</h3>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="#featured-listings">Featured</a></li>
            <li><a href="#about-section">About</a></li>
            <li><a href="#contact-section">Contact</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h3>Contact Us</h3>
          <p>Email: adminmanager@gmail.com</p>
          <p>Phone: (123) 456-7890</p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 CasaBlanca. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Modal for property details -->
  <div class="modal" id="property-modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <div id="property-details">
        <!-- Will be populated by JavaScript -->
      </div>
    </div>
  </div>

  <!-- Modal for login/signup prompt -->
  <div class="modal" id="auth-modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <div class="auth-prompt">
        <h2>Login Required</h2>
        <p>You need to be logged in to perform this action.</p>
        <div class="auth-buttons">
          <a href="login.php" class="btn btn-primary">Login</a>
          <a href="signup.php" class="btn btn-secondary">Sign Up</a>
        </div>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const profileLogo = document.querySelector('.profile-logo');
      const profileDropdown = document.querySelector('.profile-dropdown');

      if (profileLogo) {
        profileLogo.addEventListener('click', function (e) {
          e.stopPropagation();
          profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function () {
          profileDropdown.classList.remove('show');
        });
      }
    });
  </script>
</body>
</html>