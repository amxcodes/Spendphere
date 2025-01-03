<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Users</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/ScrollTrigger.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --text-color: #2d3436;
            --background-color: #f9f9f9;
            --card-background: rgba(255, 255, 255, 0.7);
            --card-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            --card-border: 1px solid rgba(255, 255, 255, 0.18);
            --input-background: rgba(255, 255, 255, 0.9);
            --button-background: var(--primary-color);
            --button-text: white;
        }

        .dark-mode {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --text-color: #ecf0f1;
            --background-color: #1c1c1c;
            --card-background: rgba(44, 62, 80, 0.7);
            --card-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            --card-border: 1px solid rgba(255, 255, 255, 0.1);
            --input-background: rgba(44, 62, 80, 0.9);
            --button-background: var(--secondary-color);
            --button-text: #2d3436;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background-color);
            color: var(--text-color);
            transition: all 0.3s ease;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        h1, h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 2rem;
        }

        .search-form {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .search-form input {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border: none;
            border-radius: 25px 0 0 25px;
            background: var(--input-background);
            color: var(--text-color);
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.1),
                        inset -2px -2px 5px rgba(255, 255, 255, 0.1);
        }

        .search-form button {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border: none;
            border-radius: 0 25px 25px 0;
            background: var(--button-background);
            color: var(--button-text);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .search-results, .linked-users {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .user-card {
            background: var(--card-background);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            border: var(--card-border);
            transition: all 0.3s ease;
            transform: translateY(0);
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 36px 0 rgba(31, 38, 135, 0.37);
        }

        .user-card h3 {
            margin-top: 0;
            color: var(--primary-color);
        }

        .user-card p {
            margin-bottom: 1rem;
        }

        .user-card form {
            display: flex;
            justify-content: center;
        }

        .user-card button {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border: none;
            border-radius: 25px;
            background: var(--button-background);
            color: var(--button-text);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-card button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .flash-message {
            background: var(--primary-color);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .mode-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .toggle {
            height: 30px;
            width: 60px;
            border-radius: 15px;
            background-color: var(--card-background);
            box-shadow: inset 0 -2px 2px 0 rgba(0,0,0,0.1),
                        inset 0 2px 2px 0 rgba(255,255,255,0.1),
                        0 2px 5px 0 rgba(0,0,0,0.1);
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .switch {
            height: 22px;
            width: 22px;
            border-radius: 50%;
            background: var(--primary-color);
            position: absolute;
            top: 4px;
            left: 4px;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.2),
                        inset 0 1px 1px rgba(255,255,255,0.2);
        }

        .toggle.dark .switch {
            transform: translateX(30px);
            background: var(--secondary-color);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .search-form {
                flex-direction: column;
                align-items: center;
            }

            .search-form input,
            .search-form button {
                width: 100%;
                border-radius: 25px;
                margin-bottom: 0.5rem;
            }

            .user-card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="mode-toggle">
        <div class="toggle">
            <div class="switch"></div>
        </div>
    </div>

    <div class="container">
        <h1>Link Users</h1>

        <?php
        if (isset($_SESSION['flash_message'])) {
            echo "<p class='flash-message'>" . $_SESSION['flash_message'] . "</p>";
            unset($_SESSION['flash_message']);
        }
        ?>

        <form action="" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search users" value="<?php echo $searchTerm; ?>">
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($searchResults)): ?>
            <h2>Search Results</h2>
            <div class="search-results">
                <?php foreach ($searchResults as $user): ?>
                    <div class="user-card">
                        <h3><?php echo safeOutput($user['FirstName'] . ' ' . $user['LastName']); ?></h3>
                        <form action="" method="POST">
                            <input type="hidden" name="follow" value="<?php echo $user['ID']; ?>">
                            <button type="submit">Follow</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($searchTerm)): ?>
            <p>No users found matching your search.</p>
        <?php endif; ?>

        <h2>Linked Users</h2>
        <div class="linked-users">
            <?php foreach ($linkedUsers as $user): ?>
                <div class="user-card">
                    <h3><?php echo safeOutput($user['FirstName'] . ' ' . $user['LastName']); ?></h3>
                    <?php
                    $totalExpense = getTotalExpenses($user['ID']);
                    echo "<p>Total Expense: $" . number_format($totalExpense, 2) . "</p>";
                    ?>
                    <form action="" method="POST">
                        <input type="hidden" name="unfollow" value="<?php echo $user['ID']; ?>">
                        <button type="submit">Unfollow</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // GSAP Animations
        gsap.registerPlugin(ScrollTrigger);

        // User cards animation
        gsap.from('.user-card', {
            opacity: 0,
            y: 50,
            stagger: 0.1,
            duration: 0.8,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: '.search-results, .linked-users',
                start: 'top 80%',
            }
        });

        // Micro-interactions for user cards
        document.querySelectorAll('.user-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                gsap.to(card, {
                    scale: 1.05,
                    duration: 0.3,
                    ease: 'power2.out'
                });
            });

            card.addEventListener('mouseleave', () => {
                gsap.to(card, {
                    scale: 1,
                    duration: 0.3,
                    ease: 'power2.out'
                });
            });

            card.addEventListener('click', () => {
                gsap.to(card, {
                    scale: 0.95,
                    duration: 0.1,
                    ease: 'power2.in',
                    yoyo: true,
                    repeat: 1
                });
            });
        });

        // Dark Mode Toggle
        const toggle = document.querySelector('.toggle');
        const body = document.body;

        toggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            toggle.classList.toggle('dark');
            localStorage.setItem('darkMode', body.classList.contains('dark-mode'));
        });

        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            body.classList.add('dark-mode');
            toggle.classList.add('dark');
        }
    </script>
</body>
</html>