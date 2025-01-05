-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2025 at 12:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `detsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `auto_expenses`
--

CREATE TABLE `auto_expenses` (
  `ID` int(10) NOT NULL,
  `UserId` int(10) NOT NULL,
  `ExpenseItem` varchar(200) DEFAULT NULL,
  `ExpenseCost` decimal(10,2) DEFAULT NULL,
  `CategoryID` int(10) DEFAULT NULL,
  `Frequency` enum('monthly','yearly') NOT NULL,
  `NextDueDate` date NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_responses`
--

CREATE TABLE `chatbot_responses` (
  `id` int(11) NOT NULL,
  `keywords` text DEFAULT NULL,
  `response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chatbot_responses`
--

INSERT INTO `chatbot_responses` (`id`, `keywords`, `response`) VALUES
(1, 'expense report analytics insights', 'Our expense reports provide detailed analytics and insights into your spending patterns. You can view breakdowns by category, time period, and more.'),
(2, 'sync bank account credit card', 'Currently, we don\'t offer direct bank or credit card syncing. However, you can manually enter transactions for accurate tracking.'),
(3, 'split shared expenses roommates', 'While we don\'t have a built-in expense splitting feature, you can track shared expenses by creating a separate category and manually calculating splits.'),
(4, 'tax deduction expense categorization', 'You can use our category system to mark expenses that may be tax-deductible. However, please consult with a tax professional for specific advice.'),
(5, 'data privacy gdpr compliance', 'We take data privacy seriously and are compliant with GDPR regulations. Your data is encrypted and never shared without your explicit consent.'),
(6, 'customize dashboard widgets', 'You can customize your dashboard by rearranging widgets and selecting which metrics to display. Look for the \"Customize\" option on the dashboard page.'),
(7, 'OCR receipt scanning feature', 'We\'re working on adding an OCR feature for scanning receipts. For now, you can manually enter expense details or attach photos of receipts to transactions.'),
(8, 'expense report analytics insights', 'Our expense reports provide detailed analytics and insights into your spending patterns. You can view breakdowns by category, time period, and more.'),
(9, 'sync bank account credit card', 'Currently, we do not offer direct bank or credit card syncing. However, you can manually enter transactions for accurate tracking.'),
(10, 'split shared expenses roommates', 'While we do not have a built-in expense splitting feature, you can track shared expenses by creating a separate category and manually calculating splits.'),
(11, 'tax deduction expense categorization', 'You can use our category system to mark expenses that may be tax-deductible. However, please consult with a tax professional for specific advice.'),
(12, 'data privacy gdpr compliance', 'We take data privacy seriously and are compliant with GDPR regulations. Your data is encrypted and never shared without your explicit consent.'),
(13, 'customize dashboard widgets', 'You can customize your dashboard by rearranging widgets and selecting which metrics to display. Look for the \"Customize\" option on the dashboard page.'),
(14, 'OCR receipt scanning feature', 'We are working on adding an OCR feature for scanning receipts. For now, you can manually enter expense details or attach photos of receipts to transactions.'),
(15, 'budget planning set goals', 'You can set budget goals in our app. Go to the Budget section, set monthly limits for each category, and track your progress throughout the month.'),
(16, 'expense categories customize add', 'You can customize expense categories in the Settings. Add, edit, or delete categories to match your specific needs and spending habits.'),
(17, 'recurring expenses automatic tracking', 'Set up recurring expenses in the app to automatically track regular payments like rent or subscriptions. Find this option in the \"Add Expense\" section.'),
(18, 'export data excel csv', 'You can export your expense data to Excel or CSV format. Go to Reports, select your date range, and click the \"Export\" button to download your data.'),
(19, 'mobile app availability', 'Yes, we have a mobile app available for both iOS and Android. Search for \"Elegant Expense Tracker\" in your device\'s app store to download it.'),
(20, 'multi-currency support exchange rates', 'Our app supports multiple currencies. You can set your primary currency and add expenses in different currencies. We use real-time exchange rates for conversion.'),
(21, 'collaborate family expense tracking', 'You can collaborate on expense tracking with family members. Invite them via email in the Settings, and you can share and manage expenses together.'),
(22, 'investment expense tracking', 'Track your investments by creating an \"Investments\" category. You can log contributions, returns, and fees to monitor your investment expenses.'),
(23, 'debt repayment tracking', 'Monitor debt repayment by creating a \"Debt\" category. Log your payments and watch your balance decrease over time in the Reports section.'),
(24, 'receipt storage digital', 'You can attach digital copies of receipts to each expense entry. Use your devices camera to snap a photo and upload it directly to the expense.'),
(25, 'year end financial summary', 'We generate a comprehensive year-end financial summary. Find it in the Reports section in January to review your annual spending patterns.'),
(26, 'expense trends analysis', 'Our trend analysis feature in the Reports section shows how your spending changes over time. Identify patterns and adjust your budget accordingly.'),
(27, 'cash expenses tracking', 'Track cash expenses by creating a \"Cash\" payment method. Enter these expenses manually to ensure all your spending is accurately recorded.'),
(28, 'bill reminders notifications', 'Set up bill reminders in the app. You will receive notifications before due dates to help you avoid late payments and manage your expenses better.'),
(29, 'savings goals track progress', 'Create savings goals in the Goals section. Set a target amount and date, and the app will track your progress and suggest monthly savings.'),
(30, 'financial health score', 'We calculate a Financial Health Score based on your income, expenses, savings, and debt. Find this in the Dashboard to get an overview of your financial status.'),
(31, 'expense comparison average', 'In the Insights section, you can compare your expenses to averages in your area or income bracket, helping you understand your spending in context.'),
(32, 'loyalty programs points tracking', 'Track loyalty program points by creating a custom category. Log your points as \"income\" to monitor your rewards across different programs.'),
(33, 'tax report generation', 'Generate a tax report at year-end that categorizes your expenses into common tax deduction categories. Remember, always consult a tax professional for advice.'),
(34, 'budgeting tips personalized', 'Receive personalized budgeting tips based on your spending patterns. Look for these insights on your Dashboard or in the weekly email summary.'),
(35, 'expense approval workflow', 'For business users, we offer an expense approval workflow. Set up approvers in the Settings to streamline your reimbursement process.'),
(36, 'data backup security', 'We automatically back up your data daily. Your information is encrypted and stored securely in multiple locations to ensure you never lose your financial records.'),
(37, 'income tracking multiple sources', 'Track income from multiple sources by adding them as separate income categories. This helps you get a full picture of your financial inflows and outflows.'),
(38, 'net worth calculation assets liabilities', 'Calculate your net worth by adding your assets and liabilities in the Net Worth section. We will track changes over time to show your financial progress.');

-- --------------------------------------------------------

--
-- Table structure for table `linked_accounts`
--

CREATE TABLE `linked_accounts` (
  `user_id` int(11) NOT NULL,
  `linked_user_id` int(11) NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `linked_accounts`
--

INSERT INTO `linked_accounts` (`user_id`, `linked_user_id`, `requested_at`) VALUES
(11, 0, '2024-09-26 19:52:16'),
(11, 2, '2024-09-26 19:52:16'),
(11, 9, '2024-09-26 19:52:16'),
(11, 12, '2024-09-26 19:52:16'),
(20, 22, '2025-01-03 15:03:50'),
(22, 20, '2025-01-03 15:03:50'),
(20, 26, '2025-01-05 20:59:38'),
(26, 20, '2025-01-05 20:59:38');

-- --------------------------------------------------------

--
-- Table structure for table `link_requests`
--

CREATE TABLE `link_requests` (
  `id` int(10) NOT NULL,
  `sender_id` int(10) NOT NULL,
  `receiver_id` int(10) NOT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `link_requests`
--

INSERT INTO `link_requests` (`id`, `sender_id`, `receiver_id`, `status`, `request_date`) VALUES
(1, 21, 22, 'pending', '2024-10-03 15:06:52'),
(2, 21, 20, 'pending', '2024-10-03 15:10:26'),
(3, 22, 20, 'accepted', '2025-01-03 15:03:30'),
(4, 26, 20, 'accepted', '2025-01-05 20:59:15');

-- --------------------------------------------------------

--
-- Table structure for table `monthly_budgets`
--

CREATE TABLE `monthly_budgets` (
  `ID` int(11) NOT NULL,
  `UserId` int(11) DEFAULT NULL,
  `Year` int(11) DEFAULT NULL,
  `Month` int(11) DEFAULT NULL,
  `Budget` decimal(10,2) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monthly_budgets`
--

INSERT INTO `monthly_budgets` (`ID`, `UserId`, `Year`, `Month`, `Budget`, `CreatedAt`) VALUES
(1, 21, 2024, 10, 30000.00, '2024-10-03 15:05:56'),
(3, 20, 2025, 1, 20000.00, '2025-01-03 15:08:58'),
(4, 25, 2025, 1, 20000.00, '2025-01-05 15:19:59'),
(5, 26, 2025, 1, 25000.00, '2025-01-05 19:07:37');

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `ID` int(10) NOT NULL,
  `UserId` int(10) DEFAULT NULL,
  `CategoryName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`ID`, `UserId`, `CategoryName`) VALUES
(1, NULL, 'Groceries'),
(2, NULL, 'Bills'),
(4, NULL, 'Travel'),
(7, NULL, 'fruits and vegetable'),
(22, 23, 'malls'),
(23, 23, 'fashion'),
(24, 20, 'fruits and vegetable'),
(25, 25, 'Rent'),
(26, 26, 'food1'),
(34, 26, 'Rent1');

-- --------------------------------------------------------

--
-- Table structure for table `tblexpense`
--

CREATE TABLE `tblexpense` (
  `ID` int(10) NOT NULL,
  `UserId` int(10) NOT NULL,
  `ExpenseDate` date DEFAULT NULL,
  `ExpenseItem` varchar(200) DEFAULT NULL,
  `ExpenseCost` decimal(10,2) DEFAULT NULL,
  `CategoryID` int(10) DEFAULT NULL,
  `NoteDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblexpense`
--

INSERT INTO `tblexpense` (`ID`, `UserId`, `ExpenseDate`, `ExpenseItem`, `ExpenseCost`, `CategoryID`, `NoteDate`) VALUES
(60, 21, '0000-00-00', 'banana', 2024.00, 2, '2024-09-29 09:29:46'),
(61, 21, '2024-10-03', 'bfgdjhu', 4000.00, 1, '2024-10-03 15:06:17'),
(62, 21, '2024-10-03', 'gfsdhdfssdf', 5778.00, 4, '2024-10-03 15:10:13'),
(70, 20, '2025-01-03', 'banana', 500.00, 7, '2025-01-03 16:12:03'),
(72, 21, '2025-01-05', 'light bulb', 700.00, 2, '2025-01-05 07:11:51'),
(73, 20, '2025-01-05', 'dhhf', 500.00, 24, '2025-01-05 14:06:18'),
(74, 25, '2025-01-05', 'house', 5000.00, 25, '2025-01-05 15:19:29'),
(75, 26, '2025-01-05', 'mandhi', 500.00, 26, '2025-01-05 19:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `tblsummary_daily`
--

CREATE TABLE `tblsummary_daily` (
  `ID` int(10) NOT NULL,
  `UserId` int(10) NOT NULL,
  `SummaryDate` date NOT NULL,
  `TotalExpense` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblsummary_monthly`
--

CREATE TABLE `tblsummary_monthly` (
  `ID` int(10) NOT NULL,
  `UserId` int(10) NOT NULL,
  `Month` int(2) NOT NULL,
  `Year` int(4) NOT NULL,
  `TotalExpense` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblsummary_yearly`
--

CREATE TABLE `tblsummary_yearly` (
  `ID` int(10) NOT NULL,
  `UserId` int(10) NOT NULL,
  `Year` int(4) NOT NULL,
  `TotalExpense` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `ID` int(10) NOT NULL,
  `admin` int(1) NOT NULL,
  `FirstName` varchar(100) DEFAULT NULL,
  `LastName` varchar(100) DEFAULT NULL,
  `Gender` enum('Male','Female','Other') DEFAULT 'Male',
  `Email` varchar(200) DEFAULT NULL,
  `MobileNumber` varchar(15) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `MonthlyBudget` decimal(10,2) DEFAULT NULL,
  `LastBudgetEntry` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`ID`, `admin`, `FirstName`, `LastName`, `Gender`, `Email`, `MobileNumber`, `Password`, `RegDate`, `MonthlyBudget`, `LastBudgetEntry`) VALUES
(18, 0, 'aman', 'anu', 'Male', 'amanxnu@gmail.com', '9037078553', '$2y$10$9F7cpwjxUXR.fNRBYO4gyuDfzxqBYD4rbdiF0EH0fRqHAR5tgy9wS', '2024-09-26 15:51:58', NULL, NULL),
(19, 0, 'aman', 'xnu', 'Male', 'aman23@gmail.com', '9037078553', '$2y$10$qkOUFosLGPXZmfGoXihOb.V2.OxUlQhHAbxc0l/yiWC2zlV2yrytG', '2024-09-26 17:30:12', NULL, NULL),
(20, 0, 'alan', 'harris', 'Male', 'alan@gmail.com', '9037078553', '$2y$10$y2jaBjC9/VqA2PLSBF4ElewG3AGSmwN7PQdJI3tvBiyaNExxwPV3m', '2024-09-26 17:43:59', 20000.00, '2025-01-03'),
(21, 0, 'amjad', 'as', 'Male', 'amjad@gmail.com', '9037078553', '$2y$10$klAMj2TxIY8H4hSIplP66ORXDeYIOmXcvfF1QpqjSLUdv15WWqzcO', '2024-09-26 17:46:58', 30000.00, '2024-10-04'),
(22, 0, 'jisjis', 'jisjismm', 'Female', 'jismi3@gmail.com', '9037078556', '$2y$10$FNOAeX2hU21luG9NlatcBew92mrYCKxWfkwQ0.A/wbLJLoBEVIQKC', '2024-09-27 19:06:10', NULL, NULL),
(23, 0, 'aman', 'xnu', 'Male', 'amanu7000@gmail.com', '9037078553', '$2y$10$4ZFDn1FA0E3dnGvvk9NkTu6YEIj1whK4ddwC.HAbnTMblEh4EuHdS', '2025-01-05 13:11:10', NULL, NULL),
(24, 0, 'Aman', 'xnu', 'Male', 'aman@gmail.com', '9037078553', '$2y$10$hZgaAsI2sw.8/qLHecYG6O3Qre4rE9WGqL84icQHeQh.8Yt2px0/q', '2025-01-05 14:36:48', NULL, NULL),
(25, 0, 'aman', '1000', 'Male', 'aman1000@gmail.com', '9037078553', '$2y$10$FkGjey2iyv6olr8Pc2zkpeLbm.m1Fmcov9Pt.ZqvstY98VEPakUoC', '2025-01-05 15:17:36', 20000.00, '2025-01-05'),
(26, 0, 'hafiz', 'mhd', 'Male', 'hafiz@gmail.com', '9083782920', '$2y$10$pMBXQu8FYBL1URq5KmL2ReYgxJ0DAjGzLRA7K5tYaYVYYS8Dm9Rbq', '2025-01-05 19:04:25', 25000.00, '2025-01-06');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser_analytics`
--

CREATE TABLE `tbluser_analytics` (
  `ID` int(10) NOT NULL,
  `UserId` int(10) NOT NULL,
  `LastLogin` timestamp NOT NULL DEFAULT current_timestamp(),
  `TotalLogins` int(10) NOT NULL DEFAULT 0,
  `ExpensesEntered` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_auto_expenses`
--

CREATE TABLE `tbl_auto_expenses` (
  `ID` int(10) NOT NULL,
  `UserId` int(10) NOT NULL,
  `ExpenseItem` varchar(200) NOT NULL,
  `ExpenseCost` decimal(10,2) NOT NULL,
  `CategoryID` int(10) NOT NULL,
  `NextDueDate` date NOT NULL,
  `Frequency` enum('monthly','quarterly','yearly') NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auto_expenses`
--
ALTER TABLE `auto_expenses`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Indexes for table `chatbot_responses`
--
ALTER TABLE `chatbot_responses`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `chatbot_responses` ADD FULLTEXT KEY `keywords` (`keywords`);

--
-- Indexes for table `link_requests`
--
ALTER TABLE `link_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `monthly_budgets`
--
ALTER TABLE `monthly_budgets`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `UserId` (`UserId`,`Year`,`Month`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_userid` (`UserId`);

--
-- Indexes for table `tblexpense`
--
ALTER TABLE `tblexpense`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Indexes for table `tblsummary_daily`
--
ALTER TABLE `tblsummary_daily`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblsummary_monthly`
--
ALTER TABLE `tblsummary_monthly`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblsummary_yearly`
--
ALTER TABLE `tblsummary_yearly`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbluser_analytics`
--
ALTER TABLE `tbluser_analytics`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbl_auto_expenses`
--
ALTER TABLE `tbl_auto_expenses`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auto_expenses`
--
ALTER TABLE `auto_expenses`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chatbot_responses`
--
ALTER TABLE `chatbot_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `link_requests`
--
ALTER TABLE `link_requests`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `monthly_budgets`
--
ALTER TABLE `monthly_budgets`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `tblexpense`
--
ALTER TABLE `tblexpense`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `tblsummary_daily`
--
ALTER TABLE `tblsummary_daily`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblsummary_monthly`
--
ALTER TABLE `tblsummary_monthly`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblsummary_yearly`
--
ALTER TABLE `tblsummary_yearly`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `tbluser_analytics`
--
ALTER TABLE `tbluser_analytics`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_auto_expenses`
--
ALTER TABLE `tbl_auto_expenses`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auto_expenses`
--
ALTER TABLE `auto_expenses`
  ADD CONSTRAINT `auto_expenses_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `tbluser` (`ID`),
  ADD CONSTRAINT `auto_expenses_ibfk_2` FOREIGN KEY (`CategoryID`) REFERENCES `tblcategory` (`ID`);

--
-- Constraints for table `link_requests`
--
ALTER TABLE `link_requests`
  ADD CONSTRAINT `link_requests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `tbluser` (`ID`),
  ADD CONSTRAINT `link_requests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `tbluser` (`ID`);

--
-- Constraints for table `monthly_budgets`
--
ALTER TABLE `monthly_budgets`
  ADD CONSTRAINT `monthly_budgets_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `tbluser` (`ID`);

--
-- Constraints for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD CONSTRAINT `fk_userid` FOREIGN KEY (`UserId`) REFERENCES `tbluser` (`ID`);

--
-- Constraints for table `tblexpense`
--
ALTER TABLE `tblexpense`
  ADD CONSTRAINT `tblexpense_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `tblcategory` (`ID`);

--
-- Constraints for table `tbl_auto_expenses`
--
ALTER TABLE `tbl_auto_expenses`
  ADD CONSTRAINT `tbl_auto_expenses_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `tbluser` (`ID`),
  ADD CONSTRAINT `tbl_auto_expenses_ibfk_2` FOREIGN KEY (`CategoryID`) REFERENCES `tblcategory` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
