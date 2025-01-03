<?php
session_start();
require_once 'includes/database.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

// Handle budget setting and visualization
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget Management</title>
    <link rel="stylesheet" href="css/main.css">
    <script src="js/budget.js"></script>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="budget-management">
        <h1>Budget Management</h1>
        <!-- Budget form and visualization goes here -->
    </div>
</body>
</html>
