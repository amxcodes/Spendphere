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

// Function to get chatbot response
function getChatbotResponse($input) {
    global $conn;
    
    // Convert input to lowercase for case-insensitive matching
    $input = strtolower($input);
    
    // Define an array of keywords and their responses
    $responses = [
        'hello' => 'Hello! How can I help you with your expense tracking today?',
        'hi' => 'Hi there! Need any help with managing your expenses?',
        'help' => 'I can help you with adding expenses, viewing reports, and managing categories. What would you like to know?',
        'add expense' => 'To add an expense, go to the "Manage Expenses" page and fill out the form with the date, item, cost, and category.',
        'view report' => 'You can view your expense reports on the "Reports" page. It shows your spending by category and over time.',
        'category' => 'Categories help organize your expenses. You can add or manage categories on the "Add Category" page.',
        'budget' => 'You can set and view your monthly budget on the "Budget" page. This helps you keep track of your spending limits.',
        'total expenses' => 'Your total expenses are displayed on the "Dashboard" page. It gives you an overview of your spending.',
        'save money' => 'To save money, try tracking all your expenses, setting a budget, and identifying areas where you can cut back.',
        'tips' => 'Here are some tips: 1. Track every expense, no matter how small. 2. Set realistic budgets. 3. Review your spending regularly. 4. Look for areas to cut unnecessary expenses.',
        'export' => 'You can export your expense data from the "Reports" page. This allows you to analyze your spending in other applications.',
        'import' => 'Currently, theres no direct import feature. You will need to add expenses manually to ensure accuracy.',
        'recurring' => 'For recurring expenses, you can set up reminders on the "Reminders" page to help you remember to add them regularly.',
        'currency' => 'The default currency is set to Indian Rupees (₹). You can change this in your account settings.',
        'secure' => 'Your data is secure. We use encryption for all sensitive information and regular security audits.',
        'mobile' => 'While we dont have a mobile app yet, our website is mobile-responsive for easy use on your smartphone or tablet.',
        'forgot password' => 'If you forgot your password, use the "Forgot Password" link on the login page to reset it.',
        'delete account' => 'To delete your account, please contact our support team. Note that this action is irreversible.',
        'contact' => 'For any other questions or support, please email us at support@expensetracker.com',
    ];
    
    // Check for exact matches first
    foreach ($responses as $keyword => $response) {
        if (strpos($input, $keyword) !== false) {
            return $response;
        }
    }
    
    // If no exact match, check for similar words using MySQL FULLTEXT search
    $stmt = $conn->prepare("SELECT response FROM chatbot_responses WHERE MATCH(keywords) AGAINST(?) LIMIT 1");
    $stmt->bind_param("s", $input);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['response'];
    }
    
    // If still no match, return a default response
    return "I'm not sure how to help with that. Could you try rephrasing your question or ask about adding expenses, viewing reports, or managing categories?";
}

// Handle chat request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_input'])) {
    $userInput = sanitize($_POST['user_input']);
    $response = getChatbotResponse($userInput);
    echo json_encode(['response' => $response]);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot - Elegant Expense Tracker</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --accent-color: #fd79a8;
            --text-color: #2d3436;
            --background-color: #f0f3f5;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: 1px solid rgba(255, 255, 255, 0.2);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #74b9ff, #a29bfe, #fd79a8);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        .main-content {
            flex-grow: 1;
            padding: 2rem;
            margin-left: 250px;
            width: calc(100% - 250px);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .chat-container {
            width: 90%;
            max-width: 800px;
            height: 80vh;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: var(--glass-border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .chat-header {
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-bottom: var(--glass-border);
        }

        .chat-header h1 {
            font-size: 24px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        #chatbox {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .message {
            max-width: 70%;
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 18px;
            font-size: 16px;
            line-height: 1.4;
            animation: messageAppear 0.3s forwards;
        }

        @keyframes messageAppear {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user-message {
            align-self: flex-end;
            background-color: var(--primary-color);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .bot-message {
            align-self: flex-start;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border-bottom-left-radius: 4px;
        }

        .input-area {
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-top: var(--glass-border);
            display: flex;
            align-items: center;
        }

        #user-input {
            flex-grow: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s;
        }

        #user-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        #user-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 15px rgba(108, 92, 231, 0.2);
        }

        #send-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-left: 10px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #send-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(253, 121, 168, 0.4);
        }

        #send-btn i {
            font-size: 20px;
        }

        .mascot {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 100px;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="%236c5ce7"/><circle cx="35" cy="40" r="5" fill="white"/><circle cx="65" cy="40" r="5" fill="white"/><path d="M35 70 Q50 80 65 70" stroke="white" stroke-width="3" fill="none"/></svg>') no-repeat center center;
            background-size: contain;
            cursor: pointer;
            transition: all 0.3s;
        }

        .mascot:hover {
            transform: scale(1.1) rotate(10deg);
        }

        .typing-indicator {
            display: inline-flex;
            align-items: center;
            padding: 10px 15px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 18px;
            margin-bottom: 15px;
        }

        .typing-indicator span {
            height: 8px;
            width: 8px;
            background-color: #fff;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            animation: typing 1s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
            margin-right: 0;
        }

        @keyframes typing {
            0% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }
            .chat-container {
                width: 95%;
                height: 90vh;
            }
            .mascot {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="chat-container">
            <div class="chat-header">
                <h1>Chatbot Assistant</h1>
            </div>
            <div id="chatbox"></div>
            <div class="input-area">
                <input type="text" id="user-input" placeholder="Type your message here...">
                <button id="send-btn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function addMessage(message, isUser = false) {
                const messageDiv = $('<div>').addClass('message').addClass(isUser ? 'user-message' : 'bot-message').text(message);
                $('#chatbox').append(messageDiv);
                $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight);
            }

            function addTypingIndicator() {
                const indicator = $('<div class="typing-indicator"><span></span><span></span><span></span></div>');
                $('#chatbox').append(indicator);
                $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight);
            }

            function removeTypingIndicator() {
                $('.typing-indicator').remove();
            }

            function sendMessage() {
                const userInput = $('#user-input').val().trim();
                if (userInput) {
                    addMessage(userInput, true);
                    $('#user-input').val('');
                    
                    addTypingIndicator();
                    
                    $.ajax({
                        url: '',
                        method: 'POST',
                        data: { user_input: userInput },
                        dataType: 'json',
                        success: function(data) {
                            setTimeout(() => {
                                removeTypingIndicator();
                                addMessage(data.response);
                            }, 1000);
                        },
                        error: function() {
                            setTimeout(() => {
                                removeTypingIndicator();
                                addMessage("Sorry, there was an error processing your request.");
                            }, 1000);
                        }
                    });
                }
            }

            $('#send-btn').click(sendMessage);
            $('#user-input').keypress(function(e) {
                if (e.which == 13) {
                    sendMessage();
                    return false;
                }
            });

            // Initial greeting
            setTimeout(() => {
                addTypingIndicator();
                setTimeout(() => {
                    removeTypingIndicator();
                    addMessage("Hello! I'm your Expense Tracker assistant. How can I help you today?");
                }, 1500);
            }, 500);
        });

        feather.replace();
    </script>
    <div class="mascot"></div>
</body>
</html>