<?php
    require_once 'login_register.php';
  

    // Redirect logged-in users to their dashboard
    if (isset($_SESSION['user_id'])) {
        $redirectPage = ($_SESSION['user_role'] === 'admin') ? 'admin.php' : 'user-dashboard.php';
        header("Location: $redirectPage");
        exit();
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="wrapper">
      <h1>Login</h1>
      <?php if (isset($_SESSION['notification'])): ?>
        <p id="error-message" class="<?= $_SESSION['notification']['type'] ?>">
          <?= $_SESSION['notification']['message'] ?>
        </p>
        <?php unset($_SESSION['notification']); ?>
      <?php endif; ?>
      <form method="POST" action="login.php">
        <div>
          <label for="email-input">
            <span>@</span>
          </label>
          <input
            type="email"
            name="email-input"
            id="email-input"
            placeholder="Email"
            required
          />
        </div>
        <div>
          <label for="password-input">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              height="24"
              viewBox="0 -960 960 960"
              width="24"
            >
              <path
                d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm240-200q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80Z"
              />
            </svg>
          </label>
          <input
            type="password"
            name="password-input"
            id="password-input"
            placeholder="Password"
            required
          />
        </div>
        <button type="submit" name="login">Login</button>
      </form>
      <p>New here? <a href="signup.php">Create an Account</a></p>
    </div>
  </body>
</html>
