<?php
session_start(); // Start the session
include 'includes/database.php'; // Include your database connection file

// Initialize variables
$error = '';
$username = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); // Get the username (email)
    $password = trim($_POST['password']); // Get the password

    // Prepare and execute the SQL query
    $sql = "SELECT * FROM tbluser WHERE Email = ?"; // Assuming the 'Email' column holds the user's email
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Fetch the user data
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['Password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['ID']; // Save user ID in session
            $_SESSION['username'] = $user['Email']; // Save email in session
            
            // Redirect to the dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Invalid password. Please try again.';
        }
    } else {
        $error = 'No user found with that email.';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Login</title>
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
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
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
            font-size: 28px;
            font-weight: 600;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: inset 2px 2px 5px #BABECC, inset -5px -5px 10px #FFF;
            color: #333;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            outline: none;
            box-shadow: inset 1px 1px 2px #BABECC, inset -1px -1px 2px #FFF;
        }

        .input-group i {
            position: absolute;
            left: 15px;
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
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: -5px -5px 20px #FFF, 5px 5px 20px #BABECC;
            width: 100%;
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

        .links {
            margin-top: 20px;
        }

        .links a {
            color: #4bc0c0;
            text-decoration: none;
            transition: color 0.3s ease;
            display: inline-block;
            margin: 5px 0;
        }

        .links a:hover {
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
        <form method="POST" action="" id="loginForm">
            <h2>Welcome Back</h2>
            <div id="errorMessage" class="error" style="display: <?= $error ? 'block' : 'none' ?>;">
                <?= htmlspecialchars($error) ?>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="username" required placeholder="Email" value="<?php echo htmlspecialchars($username ?? ''); ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" required placeholder="Password">
            </div>
            <button type="submit">Login</button>
            <div class="links">
                <a href="register.php">Don't have an account? Register here</a><br>
                <a href="forgot-password.php">Forgot Password?</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            var email = document.querySelector('input[name="username"]').value;
            var password = document.querySelector('input[name="password"]').value;
            var errorMessage = document.getElementById('errorMessage');

            // Reset error message
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';

            // Basic client-side validation
            if (email === '' || password === '') {
                errorMessage.textContent = 'Please fill in all fields.';
                errorMessage.style.display = 'block';
                document.querySelector('.container').classList.add('shake');
                setTimeout(() => {
                    document.querySelector('.container').classList.remove('shake');
                }, 820);
                e.preventDefault(); // Prevent form submission
            }
        });
    </script>
</body>
</html>
