<?php
session_start();
define('INCLUDED', true);
require_once 'includes/functions.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userDetails = getUserDetails($userId);
$linkedUsers = getLinkedUsers($userId);
$monthlyExpenses = getMonthlyExpenses($userId);
$categoryExpenses = getCategoryExpenses($userId);
$last7DaysExpenses = getLast7DaysExpenses($userId);
$currentMonthSummary = getCurrentMonthSummary($userId);

// Fetch data for linked users
$linkedUsersData = [];
foreach ($linkedUsers as $linkedUser) {
    $linkedUserId = $linkedUser['ID'];
    $linkedUsersData[$linkedUserId] = [
        'name' => $linkedUser['FirstName'] . ' ' . $linkedUser['LastName'],
        'monthlyExpenses' => getMonthlyExpenses($linkedUserId),
        'categoryExpenses' => getCategoryExpenses($linkedUserId),
        'last7DaysExpenses' => getLast7DaysExpenses($linkedUserId),
        'currentMonthSummary' => getCurrentMonthSummary($linkedUserId)
    ];
}

$allUsersData = [
    $userId => [
        'name' => $userDetails['FirstName'] . ' ' . $userDetails['LastName'],
        'monthlyExpenses' => $monthlyExpenses,
        'categoryExpenses' => $categoryExpenses,
        'last7DaysExpenses' => $last7DaysExpenses,
        'currentMonthSummary' => $currentMonthSummary
    ]
] + $linkedUsersData;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Statistics</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --text-color: #2d3436;
            --background-color: #f0f3f6;
            --navbar-width: 250px; /* Adjust this value based on your navbar width */
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
        }

        .main-content {
            flex-grow: 1;
            padding: 2rem;
            margin-left: var(--navbar-width);
            overflow-y: auto;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1, h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .chart-container {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 1rem;
            transition: transform 0.3s ease;
            height: 300px;
        }

        .chart-container:hover {
            transform: translateY(-5px);
        }

        .comparison-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 48px 0 rgba(31, 38, 135, 0.5);
        }

        .card h3 {
            color: var(--primary-color);
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }

        .card p {
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
        }

        .progress-bar {
            background-color: #e0e0e0;
            border-radius: 13px;
            padding: 3px;
            margin-top: 10px;
            margin-bottom: 8px;
        }

        .progress {
            background-color: var(--primary-color);
            height: 8px;
            border-radius: 10px;
            transition: width 0.5s ease-in-out;
        }

        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            .chart-container {
                height: 250px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            .chart-container {
                height: 200px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container fade-in">
            <h1>Expense Statistics</h1>

            <div class="charts-grid">
                <div class="chart-container">
                    <canvas id="monthlyExpensesChart"></canvas>
                </div>

                <div class="chart-container">
                    <canvas id="categoryExpensesChart"></canvas>
                </div>

                <div class="chart-container">
                    <canvas id="last7DaysExpensesChart"></canvas>
                </div>
            </div>

            <h2>Comparison with Linked Accounts</h2>
            <div class="comparison-cards" id="comparisonCards"></div>
        </div>
    </div>

    <script>
        const allUsersData = <?php echo json_encode($allUsersData); ?>;
const currentUserId = <?php echo $userId; ?>;

function createChart(canvasId, config) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    return new Chart(ctx, config);
}

function updateChart(chart, data) {
    chart.data = data;
    chart.update();
}

const chartConfigs = {
    monthlyExpenses: {
        type: 'bar',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: 'Monthly Expenses' },
                legend: { position: 'top' },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => '₹' + value.toLocaleString() }
                }
            }
        }
    },
    categoryExpenses: {
        type: 'bar',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: 'Expenses by Category' },
                legend: { position: 'top' },
            },
            scales: {
                x: { stacked: true },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: { callback: value => '₹' + value.toLocaleString() }
                }
            }
        }
    },
    last7DaysExpenses: {
        type: 'line',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: 'Last 7 Days Expenses' },
                legend: { position: 'top' },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => '₹' + value.toLocaleString() }
                }
            }
        }
    }
};

const charts = {
    monthlyExpenses: createChart('monthlyExpensesChart', chartConfigs.monthlyExpenses),
    categoryExpenses: createChart('categoryExpensesChart', chartConfigs.categoryExpenses),
    last7DaysExpenses: createChart('last7DaysExpensesChart', chartConfigs.last7DaysExpenses)
};

function getChartData() {
    const colors = ['rgba(255, 99, 132, 0.8)', 'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)', 'rgba(75, 192, 192, 0.8)'];
    
    // Get all unique categories across all users
    const allCategories = [...new Set(Object.values(allUsersData).flatMap(userData => Object.keys(userData.categoryExpenses)))];
    
    return {
        monthlyExpenses: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: Object.entries(allUsersData).map(([userId, userData], index) => ({
                label: userData.name,
                data: Object.values(userData.monthlyExpenses),
                backgroundColor: colors[index % colors.length],
                borderColor: colors[index % colors.length].replace('0.8', '1'),
                borderWidth: 1
            }))
        },
        categoryExpenses: {
            labels: allCategories,
            datasets: Object.entries(allUsersData).map(([userId, userData], index) => ({
                label: userData.name,
                data: allCategories.map(category => userData.categoryExpenses[category] || 0),
                backgroundColor: colors[index % colors.length],
                borderColor: colors[index % colors.length].replace('0.8', '1'),
                borderWidth: 1
            }))
        },
        last7DaysExpenses: {
            labels: Object.keys(allUsersData[currentUserId].last7DaysExpenses),
            datasets: Object.entries(allUsersData).map(([userId, userData], index) => ({
                label: userData.name,
                data: Object.values(userData.last7DaysExpenses),
                borderColor: colors[index % colors.length].replace('0.8', '1'),
                backgroundColor: colors[index % colors.length].replace('0.8', '0.2'),
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }))
        }
    };
}

function createComparisonCards() {
    const container = document.getElementById('comparisonCards');
    container.innerHTML = '';

    Object.entries(allUsersData).forEach(([userId, userData]) => {
        const card = document.createElement('div');
        card.className = 'card fade-in';
        card.innerHTML = `
            <h3>${userData.name}</h3>
            <p>Monthly Budget: ₹${userData.currentMonthSummary.MonthlyBudget.toLocaleString()}</p>
            <p>Total Expenses: ₹${userData.currentMonthSummary.TotalExpense.toLocaleString()}</p>
            <p>Remaining Budget: ₹${userData.currentMonthSummary.RemainingBudget.toLocaleString()}</p>
            <div class="progress-bar">
                <div class="progress" style="width: ${userData.currentMonthSummary.BudgetPercentage}%"></div>
            </div>
            <p>${userData.currentMonthSummary.BudgetPercentage.toFixed(2)}% of budget used</p>
        `;
        container.appendChild(card);
    });
}

function updateCharts() {
    const data = getChartData();
    updateChart(charts.monthlyExpenses, data.monthlyExpenses);
    updateChart(charts.categoryExpenses, data.categoryExpenses);
    updateChart(charts.last7DaysExpenses, data.last7DaysExpenses);
    createComparisonCards();
}

updateCharts();

// Simulated real-time updates (replace with actual data fetching in production)
setInterval(() => {
    Object.values(allUsersData).forEach(userData => {
        Object.keys(userData.monthlyExpenses).forEach(month => {
            userData.monthlyExpenses[month] *= Math.random() * 0.4 + 0.8;
        });
        Object.keys(userData.categoryExpenses).forEach(category => {
            userData.categoryExpenses[category] *= Math.random() * 0.4 + 0.8;
        });
        Object.keys(userData.last7DaysExpenses).forEach(day => {
            userData.last7DaysExpenses[day] *= Math.random() * 0.4 + 0.8;
        });
        userData.currentMonthSummary.TotalExpense *= Math.random() * 0.4 + 0.8;
        userData.currentMonthSummary.RemainingBudget = userData.currentMonthSummary.MonthlyBudget - userData.currentMonthSummary.TotalExpense;
        userData.currentMonthSummary.BudgetPercentage = (userData.currentMonthSummary.TotalExpense / userData.currentMonthSummary.MonthlyBudget) * 100;
    });
    updateCharts();
}, 10000);
    </script>
</body>
</html>