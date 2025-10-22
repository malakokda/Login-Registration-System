<?php
// ENHANCED PHP ERROR REPORTING (Helpful for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session for storing user state and messages
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registration"; // Ensure this database exists

// Helper function to establish and check connection
function get_db_connection($servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        $_SESSION['error'] = 'Database connection failed: ' . $conn->connect_error;
        error_log('MySQL Connection Error: ' . $conn->connect_error);
        return null;
    }
    return $conn;
}

// --- Logout Handler ---
if (isset($_POST['logout_btn'])) {
    // Clear all session variables
    $_SESSION = array(); 
    // Destroy the session
    session_destroy();
    // Redirect to clear POST data and ensure clean state
    header('Location: malak.php'); // UPDATED FILE NAME
    exit();
}

// =========================================================
// 1. PHP SERVER-SIDE LOGIC (Handles standard POST requests)
// =========================================================

// --- Registration Handler ---
if (isset($_POST['register_btn'])) {
    $conn = get_db_connection($servername, $username, $password, $dbname);
    if ($conn) {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $user_password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirmpassword'] ?? '';

        $_SESSION['active_form'] = 'register';

        // Input validation simplified
        if (empty($name) || empty($email) || empty($user_password) || empty($confirm_password)) {
            $_SESSION['error'] = 'All fields are required.';
        } elseif ($user_password !== $confirm_password) {
            $_SESSION['error'] = 'Error: Passwords do not match.';
        } elseif (strlen($user_password) < 6) {
             $_SESSION['error'] = 'Error: Password must be at least 6 characters.';
        } else {
            // 1. Check if email already exists
            $stmt = $conn->prepare("SELECT email FROM user WHERE email = ?"); // UPDATED TABLE NAME
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $_SESSION['error'] = 'Email **' . htmlspecialchars($email) . '** already exists.';
            } else {
                // 2. Hash the password for secure storage
                $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

                // 3. Insert new user and log in immediately
                $stmt = $conn->prepare("INSERT INTO user (name, email, password) VALUES (?, ?, ?)"); // UPDATED TABLE NAME
                $stmt->bind_param("sss", $name, $email, $hashed_password);

                if ($stmt->execute()) {
                    // Successful Registration: Set Session for Welcome Screen
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_fullname'] = $name;
                    $_SESSION['success'] = 'Registration successful!';
                    unset($_SESSION['active_form']); // Clear form preference
                } else {
                    $_SESSION['error'] = 'Registration failed: ' . $stmt->error;
                }
            }
            $stmt->close();
        }
        $conn->close();
    }
    // Redirect to clear POST data and prevent resubmission
    header('Location: malak.php'); // UPDATED FILE NAME
    exit();
} 

// --- Login Handler ---
elseif (isset($_POST['login_btn'])) {
    $conn = get_db_connection($servername, $username, $password, $dbname);
    if ($conn) {
        $email = $_POST['email'] ?? '';
        $user_password = $_POST['password'] ?? '';

        $_SESSION['active_form'] = 'login';

        // Input validation simplified
        if (empty($email) || empty($user_password)) {
            $_SESSION['error'] = 'Email and Password are required.';
        } else {
            // 1. Find the user by email and fetch their full name and hashed password
            $stmt = $conn->prepare("SELECT name, password FROM user WHERE email = ?"); // UPDATED TABLE NAME
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                $hashed_password = $user['password'];

                // 2. Verify the password
                if (password_verify($user_password, $hashed_password)) {
                    // Successful Login: Set Session for Welcome Screen
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_fullname'] = $user['name'];
                    $_SESSION['success'] = 'Login successful!';
                    unset($_SESSION['active_form']); // Clear form preference
                } else {
                    $_SESSION['error'] = 'Incorrect password for ' . htmlspecialchars($email) . '.';
                }
            } else {
                $_SESSION['error'] = 'No account found with email ' . htmlspecialchars($email) . '.';
            }
            $stmt->close();
        }
        $conn->close();
    }
    // Redirect to clear POST data and prevent resubmission
    header('Location: malak.php'); // UPDATED FILE NAME
    exit();
}

// --- Determine which form is active or if user is logged in ---
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

// If not logged in, determine which form to show
$active_form_id = 'loginForm';
if (isset($_SESSION['active_form']) && $_SESSION['active_form'] === 'register') {
    $active_form_id = 'registerForm';
}
?>
<!DOCTYPE html>
<html> 
<head> 
    <title><?php echo $is_logged_in ? 'Welcome' : 'Login and Registration'; ?></title>
    <style>
        body {
            background: #1690A7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            margin: 0;
            transition: background 0.5s ease;
        }

        * {
            font-family: sans-serif;
            box-sizing: border-box;
        }

        .container > div { 
            width: 500px;
            max-width: 90%; 
            border: 2px solid #ccc;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h2 {
            margin-bottom: 40px;
            color: #333;
        }

        input {
            display: block;
            border: 2px solid #ccc;
            width: 95%;
            padding: 10px;
            margin: 10px auto;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #1690A7;
            outline: none;
        }

        button {
            background: #1690A7;
            padding: 10px 15px;
            color: #fff;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        button.float-right {
            float: right;
            margin-right: 10px;
        }

        button:hover {
            background: #106f82;
        }
        
        .message {
            padding: 10px;
            width: 95%;
            border-radius: 5px;
            margin: 20px auto;
            font-weight: bold;
            display: block !important; /* Always show if content exists */
        }
        
        .message.error {
            background: #F2DEDE;
            color: #A94442;
            border: 1px solid #A94442;
        }

        .message.success {
            background: #D4EDDA;
            color: #155724;
            border: 1px solid #155724;
        }

        .toggle-text {
            text-align: right;
            margin-top: 20px;
            color: #1690A7;
            cursor: pointer;
            font-size: 14px;
            display: block;
            clear: both;
            padding-top: 10px;
        }

        .toggle-text:hover {
            text-decoration: underline;
        }

        .form {
            display: none;
        }

        .form.active {
            display: block;
        }
        
        .welcome-card {
            padding: 50px 30px;
            background: #EAF4F4; /* Lighter background for welcome screen */
        }
        
        .welcome-card h1 {
            color: #1690A7;
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .welcome-card p {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
  <div class="container">

<?php if ($is_logged_in): ?>

    <!-- WELCOME SCREEN -->
    <div id="welcomeScreen" class="welcome-card active">
        <?php 
        // Display one-time success message only after login/registration
        if (isset($_SESSION['success'])) {
            echo '<div class="message success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'User'); ?>!</h1>
        <p>You are now successfully logged in to the application.</p>
        <form method="POST" action="malak.php"> <!-- UPDATED FILE NAME -->
            <button type="submit" name="logout_btn">Log Out</button>
        </form>
    </div>

<?php else: 
    // Prepare message variable for cleaner display
    $message = '';
    $message_class = 'error';

    if (isset($_SESSION['success'])) {
        $message = $_SESSION['success'];
        $message_class = 'success';
        unset($_SESSION['success']);
    } elseif (isset($_SESSION['error'])) {
        $message = $_SESSION['error'];
        $message_class = 'error';
        unset($_SESSION['error']);
    }
?>

  <!-- LOGIN FORM -->
  <div id="loginForm" class="form <?php echo $active_form_id === 'loginForm' ? 'active' : ''; ?>">
    <h2>Welcome Back!</h2>
    <form method="POST" action="malak.php"> <!-- UPDATED FILE NAME -->
        <?php if ($active_form_id === 'loginForm' && $message): ?>
            <div class="message <?php echo $message_class; ?>" id="login_message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <input type="email" name="email" id="login_email" placeholder="Email" required>
        <input type="password" name="password" id="login_password" placeholder="Password" required>
        <button type="submit" name="login_btn" class="float-right">Log In</button>
        <div class="toggle-text" id="showRegister">
          Create new account
        </div>
    </form>
  </div>

  <!-- REGISTER FORM -->
  <div id="registerForm" class="form <?php echo $active_form_id === 'registerForm' ? 'active' : ''; ?>">
    <h2>Create Your Account</h2>
    <form method="POST" action="malak.php"> <!-- UPDATED FILE NAME -->
        <?php if ($active_form_id === 'registerForm' && $message): ?>
            <div class="message <?php echo $message_class; ?>" id="register_message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <input type="text" name="name" id="reg_fullname" placeholder="Full Name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        <input type="email" name="email" id="reg_email" placeholder="Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        <input type="password" name="password" id="reg_password" placeholder="Password (Min 6 chars)" required minlength="6">
        <input type="password" name="confirmpassword" id="reg_confirmpassword" placeholder="Confirm Password" required minlength="6">
        <button type="submit" name="register_btn" class="float-right">Sign Up</button>
        <div class="toggle-text" id="showLogin">
          Already have an account? Log in
        </div>
    </form>
  </div>
<?php endif; ?>

</div>

<script>
  // --- Client-Side Form Toggling Logic (No AJAX) ---
  // This JS only handles switching between the login and register forms on a fresh page load.
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  const showRegister = document.getElementById('showRegister');
  const showLogin = document.getElementById('showLogin');

  if (showRegister) {
    showRegister.addEventListener('click', () => {
      loginForm.classList.remove('active');
      registerForm.classList.add('active');
      // No need to clear messages via JS, as PHP handles messages only after a POST request/redirect.
    });
  }

  if (showLogin) {
    showLogin.addEventListener('click', () => {
      registerForm.classList.remove('active');
      loginForm.classList.add('active');
      // No need to clear messages via JS, as PHP handles messages only after a POST request/redirect.
    });
  }
</script>

</body>
</html>
