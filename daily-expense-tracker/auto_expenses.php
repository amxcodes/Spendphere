
session_start();
define('INCLUDED', true);
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];

// Process auto expenses
processAutoExpenses();

// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }
}

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submissions (add, edit, delete auto expenses)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_auto_expense'])) {
        $expenseItem = $_POST['expense_item'];
        $expenseCost = $_POST['expense_cost'];
        $categoryId = $_POST['category_id'];
        $frequency = $_POST['frequency'];
        $nextDueDate = $_POST['next_due_date'];

        $result = addAutoExpense($userId, $expenseItem, $expenseCost, $categoryId, $frequency, $nextDueDate);

        if ($result) {
            $success_message = "Auto expense added successfully!";
        } else {
            $error_message = "Failed to add auto expense. Please try again.";
        }
    } elseif (isset($_POST['edit_auto_expense'])) {
        $expenseId = $_POST['expense_id'];
        $expenseItem = $_POST['expense_item'];
        $expenseCost = $_POST['expense_cost'];
        $categoryId = $_POST['category_id'];
        $frequency = $_POST['frequency'];
        $nextDueDate = $_POST['next_due_date'];

        $result = editAutoExpense($expenseId, $expenseItem, $expenseCost, $categoryId, $frequency, $nextDueDate);

        if ($result) {
            $success_message = "Auto expense updated successfully!";
        } else {
            $error_message = "Failed to update auto expense. Please try again.";
        }
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $expenseId = $_GET['delete'];
    $result = deleteAutoExpense($expenseId);
    if ($result) {
        $success_message = "Auto expense deleted successfully!";
    } else {
        $error_message = "Failed to delete auto expense. Please try again.";
    }
}

// Fetch auto expenses
$autoExpenses = getAutoExpenses($userId);

// Fetch categories for the dropdown
$categories = getCategories($userId);

// ... (rest of the HTML code remains the same)
function addAutoExpense($userId, $expenseItem, $expenseCost, $categoryId, $frequency, $nextDueDate) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO auto_expenses (UserId, ExpenseItem, ExpenseCost, CategoryID, Frequency, NextDueDate) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdisd", $userId, $expenseItem, $expenseCost, $categoryId, $frequency, $nextDueDate);
    return $stmt->execute();
}

function deleteAutoExpense($userId, $expenseId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM auto_expenses WHERE ID = ? AND UserId = ?");
    $stmt->bind_param("ii", $expenseId, $userId);
    return $stmt->execute();
}

function getAutoExpenses($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT ae.*, c.CategoryName FROM auto_expenses ae JOIN tblcategory c ON ae.CategoryID = c.ID WHERE ae.UserId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Remove the duplicate getCategories() function
// The function is now defined in functions.php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Expenses - Elegant Expense Tracker</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        :root {
            --primary-color: #406ff3;
            --text-color: #ffffff;
            --background-color: rgba(255, 255, 255, 0.1);
            --hover-color: rgba(255, 255, 255, 0.2);
            --transition-speed: 0.3s;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: 1px solid rgba(255, 255, 255, 0.18);
        }

        body {
            background: linear-gradient(135deg, #74b9ff, #a29bfe);
            font-family: 'Poppins', sans-serif;
            display: flex;
            min-height: 100vh;
            color: var(--text-color);
            margin: 0;
        }

        .main-content {
            flex-grow: 1;
            padding: 2rem;
            margin-left: 250px;
            width: calc(100% - 250px);
            overflow-y: auto;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1, h2 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        h2 {
            font-size: 2rem;
        }

        .message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .error-message {
            background-color: #ff7675;
            color: #ffffff;
        }

        .success-message {
            background-color: #55efc4;
            color: #ffffff;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            margin: 10% auto;
            padding: 2rem;
            border: var(--glass-border);
            border-radius: 15px;
            width: 50%;
            max-width: 500px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .submit-btn {
            background-color: var(--primary-color);
            color: #ffffff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .auto-expenses-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .auto-expense-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            border: var(--glass-border);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .auto-expense-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .auto-expense-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .auto-expense-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #ffffff;
        }

        .auto-expense-amount {
            font-size: 1.1rem;
            font-weight: 600;
            color: #ffeaa7;
        }

        .auto-expense-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1rem;
        }

        .auto-expense-detail {
            text-align: center;
        }

        .auto-expense-detail-label {
            font-size: 0.9rem;
            color: #dfe6e9;
            margin-bottom: 0.5rem;
        }

        .auto-expense-detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: #ffffff;
        }

        .auto-expense-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        .auto-expense-action {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            margin-left: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .auto-expense-action.edit {
            background-color: #3498db;
            color: white;
        }

        .auto-expense-action.delete {
            background-color: #e74c3c;
            color: white;
        }

        .auto-expense-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .auto-expenses-list {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>Auto Expenses</h1>
            <?php if (isset($error_message)): ?>
                <div class="message error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <?php if (isset($success_message)): ?>
                <div class="message success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <!-- Add Auto Expense Button -->
            <button id="openModalBtn" class="submit-btn">Add Auto Expense</button>

            <!-- Add Auto Expense Modal -->
            <div id="addExpenseModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Add Auto Expense</h2>
                    <form method="POST" class="add-auto-expense-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="form-group">
                            <label for="expense_item">Expense Item</label>
                            <input type="text" id="expense_item" name="expense_item" required>
                        </div>
                        <div class="form-group">
                            <label for="expense_cost">Expense Cost</label>
                            <input type="number" id="expense_cost" name="expense_cost" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['ID']; ?>"><?php echo htmlspecialchars($category['CategoryName']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="frequency">Frequency</label>
                            <select id="frequency" name="frequency" required>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="next_due_date">Next Due Date</label>
                            <input type="date" id="next_due_date" name="next_due_date" required>
                        </div>
                        <button type="submit" class="submit-btn" name="add_auto_expense">Add Auto Expense</button>
                    </form>
                </div>
            </div>

            <!-- List of Auto Expenses -->
            <div class="auto-expenses-list">
                <?php foreach ($autoExpenses as $index => $expense): ?>
                    <div class="auto-expense-card" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                        <div class="auto-expense-header">
                            <span class="auto-expense-title"><?php echo htmlspecialchars($expense['ExpenseItem']); ?></span>
                            <span class="auto-expense-amount">₹<?php echo number_format($expense['ExpenseCost'], 2); ?></span>
                        </div>
                        <div class="auto-expense-details">
                            <div class="auto-expense-detail">
                                <div class="auto-expense-detail-label">Category</div>
                                <div class="auto-expense-detail-value"><?php echo htmlspecialchars($expense['CategoryName']); ?></div>
                            </div>
                            <div class="auto-expense-detail">
                                <div class="auto-expense-detail-label">Frequency</div>
                                <div class="auto-expense-detail-value"><?php echo ucfirst($expense['Frequency']); ?></div>
                            </div>
                            <div class="auto-expense-detail">
                                <div class="auto-expense-detail-label">Next Due Date</div>
                                <div class="auto-expense-detail-value"><?php echo $expense['NextDueDate']; ?></div>
                            </div>
                        </div>
                        <div class="auto-expense-actions">
                            <a href="edit_auto_expense.php?id=<?php echo $expense['ID']; ?>" class="auto-expense-action edit">Edit</a>
                            <a href="?delete=<?php echo $expense['ID']; ?>" class="auto-expense-action delete" onclick="return confirm('Are you sure you want to delete this auto expense?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        feather.replace();

        const modal = document.getElementById("addExpenseModal");
        const btn = document.getElementById("openModalBtn");
        const span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add hover effect to auto expense cards
        const autoExpenseCards = document.querySelectorAll('.auto-expense-card');
        autoExpenseCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-5px)';
                card.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.1)';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = 'none';
            });
        });
    </script>
</body>
</html>