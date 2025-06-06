<?php
  
    require_once 'login_register.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="wrapper">
      <h1>Create an Account</h1>
      <?php if (isset($_SESSION['notification'])): ?>
        <p id="error-message" class="<?= $_SESSION['notification']['type'] ?>">
          <?= $_SESSION['notification']['message'] ?>
        </p>
        <?php unset($_SESSION['notification']); ?>
      <?php endif; ?>
      <form method="POST" action="signup.php">
        <div>
          <label for="name-input">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              height="24"
              viewBox="0 -960 960 960"
              width="24"
            >
              <path
                d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"
              />
            </svg>
          </label>
          <input
            type="text"
            name="name-input"
            id="name-input"
            placeholder="Full Name"
            required
          />
        </div>
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
            minlength="9"
            required
          />
          <small class="password-hint">Password must be at least 9 characters</small>
        </div>
        <div>
          <label for="confirm-password-input">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              height="24"
              viewBox="http://www.w3.org/2000/svg"
              width="24"
            >
              <path
                d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm240-200q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80Z"
              />
            </svg>
          </label>
          <input
            type="password"
            name="confirm-password-input"
            id="confirm-password-input"
            placeholder="Confirm Password"
            minlength="9"
            required
          />
        </div>
        <button type="submit" name="signup">Create Account</button>
      </form>
      <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
  </body>
</html>
