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
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
}
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Handle adding or updating monthly budget
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_budget'])) {
    $newBudget = filter_var($_POST['monthly_budget'], FILTER_VALIDATE_FLOAT);
    if ($newBudget !== false && $newBudget >= 0) {
        if (setMonthlyBudget($userId, $newBudget)) {
            $success_message = "Monthly budget updated successfully.";
        } else {
            $error_message = "Failed to update monthly budget. Please try again.";
        }
    } else {
        $error_message = "Please enter a valid non-negative budget amount.";
    }
}

// Fetch current monthly budget
$currentBudget = getMonthlyBudget($userId);

// Fetch monthly expenses for the past 6 months
$monthlyExpenses = getMonthlyExpenses($userId);

// Get current month summary
$currentMonthSummary = getCurrentMonthSummary($userId);

// Get expense breakdown by category for the current month
$expensesByCategory = getCategoryExpenses($userId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Budget and Expenses - Elegant Expense Tracker</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        input[type="number"] {
            width: 100%;
            padding: 0.75rem;
            background: var(--input-bg);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            color: #2c3e50;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        input[type="number"]:focus {
            background: rgba(255, 255, 255, 0.6);
            border-color: rgba(108, 92, 231, 0.5);
            outline: none;
            box-shadow: 0 0 15px rgba(108, 92, 231, 0.2);
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

        .summary-box p {
            color: #34495e;
            margin-bottom: 0.5rem;
            text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.1);
        }

        .chart-container {
            width: 100%;
            height: 300px;
            margin-top: 2rem;
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
            <h1>Monthly Budget and Expenses</h1>
            <?php if (isset($error_message)): ?>
                <div class="message error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <?php if (isset($success_message)): ?>
                <div class="message success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label for="monthly_budget">Monthly Budget (₹)</label>
                    <input type="number" name="monthly_budget" id="monthly_budget" required step="0.01" min="0" value="<?php echo $currentBudget; ?>">
                </div>
                <button type="submit" class="submit-btn" name="update_budget">Update Monthly Budget</button>
            </form>
        </div>

        <div class="container">
            <h2>Current Month Overview</h2>
            <div class="summary-section">
                <div class="summary-box">
                    <h3>Budget Summary</h3>
                    <p>Monthly Budget: ₹<?php echo number_format($currentMonthSummary['MonthlyBudget'], 2); ?></p>
                    <p>Total Expenses: ₹<?php echo number_format($currentMonthSummary['TotalExpense'], 2); ?></p>
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
            <div class="chart-container">
                <canvas id="expensePieChart"></canvas>
            </div>
        </div>

        <div class="container">
            <h2>Monthly Expenses History</h2>
            <div class="chart-container">
                <canvas id="monthlyExpensesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Pie Chart for Expenses by Category
        const pieCtx = document.getElementById('expensePieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($expensesByCategory)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($expensesByCategory)); ?>,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ]
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Expenses by Category'
                }
            }
        });

        // Bar Chart for Monthly Expenses History
        const barCtx = document.getElementById('monthlyExpensesChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($monthlyExpenses)); ?>,
                datasets: [{
                    label: 'Monthly Expenses',
                    data: <?php echo json_encode(array_values($monthlyExpenses)); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Expenses (₹)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Monthly Expenses History'
                }
            }
        });
    </script>
</body>
</html>