<?php
session_start();
require_once 'includes/database.php'; // Ensure this points to your database connection
require_once 'includes/functions.php'; // Include your functions file if needed

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$firstName = '';
$lastName = '';
$email = '';
$mobileNumber = '';
$password = '';
$confirmPassword = '';
$gender = '';
$error = '';

// Process the form when it's submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['mobileNumber'], $_POST['password'], $_POST['confirmPassword'], $_POST['gender'])) {
        $firstName = sanitize($_POST['firstName']);
        $lastName = sanitize($_POST['lastName']);
        $email = sanitize($_POST['email']);
        $mobileNumber = sanitize($_POST['mobileNumber']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $gender = sanitize($_POST['gender']);

        // Validate passwords
        if ($password === $confirmPassword) {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT * FROM tbluser WHERE Email = ?");
            $stmt->bind_param('s', $email); // 's' indicates the type is string
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO tbluser (FirstName, LastName, Email, MobileNumber, Password, Gender) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssss', $firstName, $lastName, $email, $mobileNumber, $hashedPassword, $gender); // 'ssssss' indicates six strings

                // Execute the statement and check for errors
                if ($stmt->execute()) {
                    $_SESSION['user_id'] = $conn->insert_id; // Get the last inserted ID
                    header("Location: dashboard.php");
                    exit(); // Always make sure to exit after a header redirect
                } else {
                    $error = "There was an error registering your account: " . $stmt->error;
                }
            } else {
                $error = "Email already registered. Please use a different email.";
            }
        } else {
            $error = "Passwords do not match.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Register</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            text-align: center;
            width: 100%;
            max-width: 400px;
            transform: translateY(20px);
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        .input-group {
            position: relative;
            margin-bottom: 15px;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: inset 2px 2px 5px #BABECC, inset -5px -5px 10px #FFF;
            color: #333;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            box-shadow: inset 1px 1px 2px #BABECC, inset -1px -1px 2px #FFF;
        }

        .input-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #4bc0c0;
        }

        button {
            background-color: #4bc0c0;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: -5px -5px 20px #FFF, 5px 5px 20px #BABECC;
            width: 100%;
            margin-top: 10px;
        }

        button:hover {
            background-color: #36a2a2;
            transform: translateY(-2px);
            box-shadow: -2px -2px 5px #FFF, 2px 2px 5px #BABECC;
        }

        button:active {
            transform: translateY(0);
            box-shadow: inset 1px 1px 2px #BABECC, inset -1px -1px 2px #FFF;
        }

        p {
            margin-top: 15px;
            font-size: 14px;
        }

        a {
            color: #4bc0c0;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #36a2a2;
        }

        .error {
            color: #ff4757;
            font-size: 14px;
            margin-bottom: 15px;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
            20%, 40%, 60%, 80% { transform: translateX(10px); }
        }

        .shake {
            animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both;
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="POST" action="" id="registerForm">
            <h2>Create Account</h2>
            <?php if ($error): ?>
                <div id="errorMessage" class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="firstName" required placeholder="First Name" value="<?php echo htmlspecialchars($firstName ?? ''); ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="lastName" required placeholder="Last Name" value="<?php echo htmlspecialchars($lastName ?? ''); ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" required placeholder="Email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-phone"></i>
                <input type="tel" name="mobileNumber" required placeholder="Mobile Number" value="<?php echo htmlspecialchars($mobileNumber ?? ''); ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" required placeholder="Password">
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirmPassword" required placeholder="Confirm Password">
            </div>
            <div class="input-group">
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?php if ($gender === 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($gender === 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>
            <button type="submit">Register</button>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</body>
</html>