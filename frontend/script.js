// DOM Elements
document.addEventListener('DOMContentLoaded', function() {
    // Initialize auth UI
    updateAuthUI();
    
    // Initialize featured listings
    initFeaturedListings();
    
    // Set up event listeners
    setupEventListeners();
});

function updateAuthUI() {
    const authButtonsContainer = document.getElementById('auth-buttons');

    if (Auth.isLoggedIn()) {
        const user = Auth.getCurrentUser();
        const initials = user.name.charAt(0).toUpperCase();

        authButtonsContainer.innerHTML = `
            <div class="profile-container">
                <div class="profile-logo">${initials}</div>
                <div class="profile-name">${user.name}</div>
                <div class="profile-dropdown">
                    <div class="profile-dropdown-item">
                        <a href="${user.role === 'admin' ? 'admin.php' : 'user-dashboard.php'}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </div>
                    <div class="profile-dropdown-item">
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        `;

        // Add dropdown toggle functionality
        const profileLogo = authButtonsContainer.querySelector('.profile-logo');
        const profileDropdown = authButtonsContainer.querySelector('.profile-dropdown');

        profileLogo.addEventListener('click', function (e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function () {
            profileDropdown.classList.remove('show');
        });
    } else {
        authButtonsContainer.innerHTML = `
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="signup.php" class="btn btn-secondary">Sign Up</a>
        `;
    }
}

// Add this function to show the edit profile modal
function showEditProfileModal() {
    const modal = document.getElementById('edit-profile-modal');
    if (modal) {
        modal.style.display = 'block';
    }
}
  
function setupEventListeners() {
    // Property listing button
    const listPropertyBtn = document.getElementById('list-property-btn');
    if (listPropertyBtn) {
      listPropertyBtn.addEventListener('click', function() {
        if (Auth.isLoggedIn()) {
          // Redirect to property listing form or show modal
          alert('Property listing form will be implemented in the next phase.');
        } else {
          // Show login prompt
          showAuthModal();
        }
      });
    }
    
    // Modal close buttons
    const closeButtons = document.querySelectorAll('.close-modal');
    closeButtons.forEach(button => {
      button.addEventListener('click', function() {
        const modal = this.closest('.modal');
        modal.style.display = 'none';
      });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
      const modals = document.querySelectorAll('.modal');
      modals.forEach(modal => {
        if (event.target === modal) {
          modal.style.display = 'none';
        }
      });
    });
    
    // Contact form submission
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
      contactForm.addEventListener('submit', sendMail);
    }
    
    // Reservation form notification (AJAX, no redirect, no page change)
    const reservationForm = document.getElementById('reservation-form');
    if (reservationForm) {
        reservationForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(reservationForm);
            fetch(reservationForm.action, {
                method: 'POST',
                body: formData
            })
            .then(async res => {
                // Try to parse as JSON, fallback to text
                let data;
                try {
                    data = await res.json();
                } catch {
                    data = { success: res.ok, message: await res.text() };
                }
                showNotification(
                    data.message && data.message.toLowerCase().includes('success')
                        ? 'Reservation submitted successfully!'
                        : (data.message || 'Failed to submit reservation.'),
                    data.success || (data.message && data.message.toLowerCase().includes('success')) ? 'success' : 'error'
                );
                if (data.success || (data.message && data.message.toLowerCase().includes('success'))) {
                    reservationForm.reset();
                    if (typeof closeReservationModal === 'function') closeReservationModal();
                }
            })
            .catch(() => {
                showNotification('Failed to submit reservation.', 'error');
            });
        });
    }
    
    // Search and sort functionality
    const searchInput = document.getElementById('search-input');
    const sortSelect = document.getElementById('sort-select');
    let listings = []; // Store all listings

    // Fetch listings once and store them
    fetch('fetch_featured_listings.php')
        .then(response => response.json())
        .then(data => {
            listings = data;
            displayListings(listings); // Initial display
        })
        .catch(error => console.error('Error:', error));

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            filterAndSortListings(listings, searchInput.value, sortSelect.value);
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            filterAndSortListings(listings, searchInput.value, sortSelect.value);
        });
    }
}

function filterAndSortListings(listings, searchTerm, sortOption) {
    // Filter listings
    let filteredListings = listings.filter(listing => 
        listing.title.toLowerCase().includes(searchTerm.toLowerCase())
    );

    // Sort listings
    switch (sortOption) {
        case 'name-asc':
            filteredListings.sort((a, b) => a.title.localeCompare(b.title));
            break;
        case 'name-desc':
            filteredListings.sort((a, b) => b.title.localeCompare(a.title));
            break;
        case 'price-asc':
            filteredListings.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
            break;
        case 'price-desc':
            filteredListings.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
            break;
    }

    displayListings(filteredListings);
}

function displayListings(listings) {
    const grid = document.getElementById('featured-listings-grid');
    if (!grid) return;

    if (listings.length === 0) {
        grid.innerHTML = '<p class="no-data">No properties found matching your search.</p>';
        return;
    }

    let listingsHTML = '';
    listings.forEach(listing => {
        listingsHTML += `
            <div class="listing-card">
                <div class="listing-image">
                    <img src="../uploads/${listing.image}" alt="${listing.title}">
                    <div class="listing-price">$${parseFloat(listing.price).toLocaleString()}/month</div>
                </div>
                <div class="listing-details">
                    <h3 class="listing-title">${listing.title}</h3>
                    <div class="listing-location">
                        <i class="fas fa-map-marker-alt"></i> ${listing.location}
                    </div>
                    <div class="listing-features">
                        <div class="listing-feature">
                            <i class="fas fa-bed"></i> ${listing.bedrooms} Beds
                        </div>
                        <div class="listing-feature">
                            <i class="fas fa-bath"></i> ${listing.bathrooms} Baths
                        </div>
                        <div class="listing-feature">
                            <i class="fas fa-vector-square"></i> ${listing.area} sq ft
                        </div>
                    </div>
                    <p>${listing.description}</p>
                </div>
            </div>
        `;
    });
    grid.innerHTML = listingsHTML;
}
  
function showAuthModal() {
    const authModal = document.getElementById('auth-modal');
    authModal.style.display = 'block';
}
  
function initFeaturedListings() {
    const featuredListingsGrid = document.getElementById('featured-listings-grid');
    if (!featuredListingsGrid) return;

    fetch('fetch_featured_listings.php')
        .then(response => response.json())
        .then(listings => {
            let listingsHTML = '';
            listings.forEach(listing => {
                listingsHTML += `
                    <div class="listing-card">
                        <div class="listing-image">
                            <img src="uploads/${listing.image}" alt="${listing.title}">
                            <div class="listing-price">$${listing.price}/month</div>
                        </div>
                        <div class="listing-details">
                            <h3 class="listing-title">${listing.title}</h3>
                            <div class="listing-location">
                                <i class="fas fa-map-marker-alt"></i> ${listing.location}
                            </div>
                            <div class="listing-features">
                                <div class="listing-feature">
                                    <i class="fas fa-bed"></i> ${listing.bedrooms} Beds
                                </div>
                                <div class="listing-feature">
                                    <i class="fas fa-bath"></i> ${listing.bathrooms} Baths
                                </div>
                                <div class="listing-feature">
                                    <i class="fas fa-vector-square"></i> ${listing.area} sq ft
                                </div>
                            </div>
                            <p>${listing.description}</p>
                        </div>
                    </div>
                `;
            });
            featuredListingsGrid.innerHTML = listingsHTML;
        })
        .catch(error => console.error('Error fetching featured listings:', error));
}

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

// Remove these sections as they're now in index.php
// Remove or comment out:
// - emailjs.init() 
// - sendMail function
// - API Configuration section

// API Services Used:
// - EmailJS Service ID: service_wn8muwd
// - EmailJS Template ID: template_j8pgoca
// - EmailJS Public Key: WlE7MJuVyIGIo1SRI


