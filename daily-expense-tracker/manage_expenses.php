<?php
session_start();
define('INCLUDED', true);
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log'); // Adjust this path as needed

require_once 'includes/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$userId = $_SESSION['user_id'];

// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }
}
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Handle adding an expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_expense'])) {
    $expenseDate = sanitize($_POST['expense_date']);
    $expenseItem = sanitize($_POST['expense_item']);
    $expenseCost = filter_var($_POST['expense_cost'], FILTER_VALIDATE_FLOAT);
    $categoryId = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);

    if ($expenseDate && $expenseItem && $expenseCost !== false && $categoryId !== false) {
        if (addExpense($userId, $expenseCost, $expenseItem, $expenseDate, $categoryId)) {
            $success_message = "Expense added successfully.";
        } else {
            $error_message = "Failed to add expense. Please try again.";
        }
    } else {
        $error_message = "Please fill in all required fields correctly.";
    }
}

// Handle editing an expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_expense'])) {
    $expenseId = filter_var($_POST['expense_id'], FILTER_VALIDATE_INT);
    $expenseDate = sanitize($_POST['expense_date']);
    $expenseItem = sanitize($_POST['expense_item']);
    $expenseCost = filter_var($_POST['expense_cost'], FILTER_VALIDATE_FLOAT);
    $categoryId = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);

    if ($expenseId !== false && $expenseDate && $expenseItem && $expenseCost !== false && $categoryId !== false) {
        if (updateExpense($expenseId, $userId, $expenseDate, $expenseItem, $expenseCost, $categoryId)) {
            $success_message = "Expense updated successfully.";
        } else {
            $error_message = "Failed to edit expense. Please try again.";
        }
    } else {
        $error_message = "Please fill in all required fields correctly.";
    }
}

// Handle deleting an expense
if (isset($_GET['delete'])) {
    $expenseId = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
    if ($expenseId !== false && deleteExpense($expenseId, $userId)) {
        $success_message = "Expense deleted successfully.";
    } else {
        $error_message = "Failed to delete expense. Please try again.";
    }
}

// Fetch user's expenses
$expenses = getExpenses($userId);
if ($expenses === false) {
    $error_message = "Error fetching expenses.";
}

// Fetch categories
$categories = getcategories();
if ($categories === false) {
    $error_message = "Error fetching categories.";
}

// Check if we need to edit an expense
$editExpense = null;
if (isset($_GET['edit'])) {
    $expenseId = filter_var($_GET['edit'], FILTER_VALIDATE_INT);
    if ($expenseId !== false) {
        foreach ($expenses as $expense) {
            if ($expense['ID'] == $expenseId) {
                $editExpense = $expense;
                break;
            }
        }
    }
}

// Calculate total expenses
$totalExpenses = array_sum(array_column($expenses, 'ExpenseCost'));

// Get expense breakdown by category
$expensesByCategory = []; 
foreach ($expenses as $expense) {
    $categoryName = $expense['CategoryName'];
    if (!isset($expensesByCategory[$categoryName])) {
        $expensesByCategory[$categoryName] = 0;
    }
    $expensesByCategory[$categoryName] += $expense['ExpenseCost'];
}

// Get monthly budget and current month summary
$monthlyBudget = getMonthlyBudget($userId);
$currentMonthSummary = getCurrentMonthSummary($userId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Expenses - Elegant Expense Tracker</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --text-color: #2d3436;
            --background-color: #f0f3f5;
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: 1px solid rgba(255, 255, 255, 0.35);
            --input-bg: rgba(255, 255, 255, 0.5);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(135deg, #74b9ff, #a29bfe);
            font-family: 'Poppins', sans-serif;
            display: flex;
            min-height: 100vh;
            color: var(--text-color);
        }

        .main-content {
            flex-grow: 1;
            padding: 2rem;
            margin-left: 250px;
            width: calc(100% - 250px);
            overflow-y: auto;
        }

        .container {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: var(--glass-border);
            padding: 2rem;
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
            transition: all 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.45);
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

        h1, h2, h3 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #34495e;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.1);
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 0.75rem;
            background: var(--input-bg);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            color: #2c3e50;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        input[type="text"]::placeholder,
        input[type="number"]::placeholder,
        input[type="date"]::placeholder,
        select::placeholder {
            color: rgba(44, 62, 80, 0.7);
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        select:focus {
            background: rgba(255, 255, 255, 0.6);
            border-color: rgba(108, 92, 231, 0.5);
            outline: none;
            box-shadow: 0 0 15px rgba(108, 92, 231, 0.2);
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%232c3e50' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }

        .submit-btn {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
        }

        .submit-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-weight: 600;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .error-message {
            background-color: rgba(255, 87, 87, 0.3);
            color: #c0392b;
            border: 1px solid rgba(255, 87, 87, 0.5);
        }

        .success-message {
            background-color: rgba(46, 204, 113, 0.3);
            color: #f3f6f4;
            border: 1px solid rgba(46, 204, 113, 0.5);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-top: 1rem;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            background: rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            color: #2c3e50;
        }

        th {
            background-color: rgba(108, 92, 231, 0.4);
            color: white;
            font-weight: 600;
        }

        tr {
            transition: all 0.3s ease;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.5);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .action-links a {
            color: #3498db;
            text-decoration: none;
            margin-right: 1rem;
            transition: all 0.3s ease;
        }

        .action-links a:hover {
            color: #2980b9;
            text-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        .summary-section {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .summary-box {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex-basis: calc(50% - 1rem);
            border: var(--glass-border);
            transition: all 0.3s ease;
        }

        .summary-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .summary-box h3 {
            color: #e67e22;
            margin-bottom: 1rem;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.1);
        }

        .summary-box p, .summary-box li {
            color: #34495e;
            margin-bottom: 0.5rem;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }
            .container {
                padding: 1.5rem;
            }
            .summary-box {
                flex-basis: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1>Manage Expenses</h1>
            <?php if (isset($error_message)): ?>
                <div class="message error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <?php if (isset($success_message)): ?>
                <div class="message success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <?php if ($editExpense): ?>
                    <input type="hidden" name="expense_id" value="<?php echo $editExpense['ID']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="expense_date">Date</label>
                    <input type="date" name="expense_date" id="expense_date" required value="<?php echo $editExpense ? $editExpense['ExpenseDate'] : date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="expense_item">Item</label>
                    <input type="text" name="expense_item" id="expense_item" required value="<?php echo $editExpense ? htmlspecialchars($editExpense['ExpenseItem']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="expense_cost">Cost (₹)</label>
                    <input type="number" name="expense_cost" id="expense_cost" required step="0.01" value="<?php echo $editExpense ? $editExpense['ExpenseCost'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['ID']; ?>" <?php echo ($editExpense && $editExpense['CategoryID'] == $category['ID']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['CategoryName']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="submit-btn" name="<?php echo $editExpense ? 'edit_expense' : 'add_expense'; ?>">
                    <?php echo $editExpense ? 'Update Expense' : 'Add Expense'; ?>
                </button>
            </form>
        </div>

        <div class="container">
            <h2>Your Expenses</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['ExpenseDate']); ?></td>
                            <td><?php echo htmlspecialchars($expense['ExpenseItem']); ?></td>
                            <td><?php echo htmlspecialchars($expense['CategoryName']); ?></td>
                            <td>₹<?php echo number_format($expense['ExpenseCost'], 2); ?></td>
                            <td class="action-links">
                                <a href="?edit=<?php echo $expense['ID']; ?>">Edit</a>
                                <a href="?delete=<?php echo $expense['ID']; ?>" onclick="return confirm('Are you sure you want to delete this expense?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total Expenses:</strong></td>
                        <td colspan="2"><strong>₹<?php echo number_format($totalExpenses, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="container">
            <h2>Expense Summary</h2>
            <div class="summary-section">
                <div class="summary-box">
                    <h3>Monthly Overview</h3>
                    <p>Monthly Budget: ₹<?php echo number_format($monthlyBudget, 2); ?></p>
                    <p>Total Expenses This Month: ₹<?php echo number_format($currentMonthSummary['TotalExpense'], 2); ?></p>
                    <p>Remaining Budget: ₹<?php echo number_format($currentMonthSummary['RemainingBudget'], 2); ?></p>
                    <p>Budget Usage: <?php echo number_format($currentMonthSummary['BudgetPercentage'], 2); ?>%</p>
                </div>
                <div class="summary-box">
                    <h3>Expenses by Category</h3>
                    <ul>
                        <?php foreach ($expensesByCategory as $category => $amount): ?>
                            <li><?php echo htmlspecialchars($category); ?>: ₹<?php echo number_format($amount, 2); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        feather.replace();

        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add staggered animation to table rows
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach((row, index) => {
            row.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s forwards`;
            row.style.opacity = '0';
        });
    </script>
</body>
</html>
