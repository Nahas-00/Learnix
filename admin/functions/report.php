<?php
include_once '../../utils/connect.php'; // PDO connection file
session_start();

// ✅ Only admin can access
if (!isset($_SESSION['logid']) || $_SESSION['logid'] !== 1) {
    header('Location: ../../login/login.php');
    exit();
}

// 🗓 Default date range: current month
$from_date = $_GET['from'] ?? date('Y-m-01');
$to_date   = $_GET['to'] ?? date('Y-m-t');

// Format labels
$period_label = date('d M Y', strtotime($from_date)) . " to " . date('d M Y', strtotime($to_date));
$generated_on = date('d M Y, h:i A');

// Prepare query-safe parameters
$params = [
    ':from_date' => $from_date . " 00:00:00",
    ':to_date'   => $to_date . " 23:59:59"
];

/* -------------------------------
    1️⃣ Total Users
--------------------------------*/
$user_stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$user_result = $user_stmt->fetch(PDO::FETCH_ASSOC);

/* -------------------------------
    2️⃣ Total & Successful Submissions
--------------------------------*/
$sub_query = "SELECT 
                COUNT(*) AS total_submissions,
                SUM(CASE WHEN result='Success' THEN 1 ELSE 0 END) AS successful_submissions
              FROM submission s
              WHERE s.timestamp BETWEEN :from_date AND :to_date";
$sub_stmt = $pdo->prepare($sub_query);
$sub_stmt->execute($params);
$sub_result = $sub_stmt->fetch(PDO::FETCH_ASSOC);

$total_sub = (int)($sub_result['total_submissions'] ?? 0);
$total_success = (int)($sub_result['successful_submissions'] ?? 0);
$success_rate = $total_sub > 0 ? round(($total_success / $total_sub) * 100, 2) : 0;

/* -------------------------------
    3️⃣ Most Active User
--------------------------------*/
$active_user_query = "SELECT u.username, COUNT(s.id) AS total 
                      FROM submission s
                      JOIN users u ON s.uid = u.id
                      WHERE s.timestamp BETWEEN :from_date AND :to_date
                      GROUP BY s.uid, u.username
                      ORDER BY total DESC 
                      LIMIT 1";
$active_stmt = $pdo->prepare($active_user_query);
$active_stmt->execute($params);
$active_user_result = $active_stmt->fetch(PDO::FETCH_ASSOC);

/* -------------------------------
    4️⃣ Top Category
--------------------------------*/
$category_query = "SELECT c.name AS category_name, COUNT(s.id) AS solved 
                   FROM submission s
                   JOIN question q ON s.qid = q.id
                   JOIN category c ON q.category_id = c.id
                   WHERE s.result = 'Success' 
                   AND s.timestamp BETWEEN :from_date AND :to_date
                   GROUP BY c.name
                   ORDER BY solved DESC
                   LIMIT 1";
$cat_stmt = $pdo->prepare($category_query);
$cat_stmt->execute($params);
$category_result = $cat_stmt->fetch(PDO::FETCH_ASSOC);

/* -------------------------------
    5️⃣ Per-User Detailed Stats (Table)
--------------------------------*/
$table_query = "SELECT u.username,
                       COUNT(s.id) AS total,
                       SUM(CASE WHEN s.result='Success' THEN 1 ELSE 0 END) AS success,
                       SUM(CASE WHEN s.result!='Success' THEN 1 ELSE 0 END) AS failed
                FROM submission s
                JOIN users u ON s.uid = u.id
                WHERE s.timestamp BETWEEN :from_date AND :to_date
                GROUP BY s.uid, u.username
                ORDER BY total DESC";
$table_stmt = $pdo->prepare($table_query);
$table_stmt->execute($params);
$table_rows = $table_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Learnix Admin Report</title>
<link rel="icon" href="../../assets/images/web_icon.png" type="image/png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --bg-primary: #18181b;      /* Zinc 900 */
        --bg-secondary: #27272a;   /* Zinc 800 */
        --border-primary: #3f3f46; /* Zinc 700 */
        --text-primary: #f4f4f5;   /* Zinc 100 */
        --text-secondary: #a1a1aa; /* Zinc 400 */
        --accent-primary: #8b5cf6; /* Violet 500 */
        --accent-hover: #7c3aed;   /* Violet 600 */
        --green: #22c55e;          /* Green 500 */
        --red: #ef4444;            /* Red 500 */
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html {
        color-scheme: dark;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background-color: var(--bg-primary);
        color: var(--text-primary);
        margin: 0;
        padding: 1rem;
    }

    .container {
        max-width: 1400px;
        margin: 1rem auto;
        padding: 1.5rem;
    }

    /* Header */
    .report-header {
        margin-bottom: 1.5rem;
    }
    .report-header h1 {
        font-size: 1.875rem; /* 30px */
        font-weight: 600;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .report-header .subtitle {
        font-size: 0.875rem; /* 14px */
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }
    .report-header .subtitle strong {
        color: var(--text-primary);
    }

    /* Controls Form */
    .controls-form {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background-color: var(--bg-secondary);
        border: 1px solid var(--border-primary);
        border-radius: 8px;
        margin-bottom: 2rem;
    }
    .form-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
    }
    .form-group label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
    }
    .form-group input[type="date"] {
        background-color: var(--bg-primary);
        color: var(--text-primary);
        border: 1px solid var(--border-primary);
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        font-family: inherit;
        font-size: 0.875rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.875rem;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }
    .btn-primary {
        background-color: var(--accent-primary);
        color: #ffffff;
    }
    .btn-primary:hover {
        background-color: var(--accent-hover);
    }
    .btn-secondary {
        background-color: var(--border-primary);
        color: var(--text-primary);
    }
    .btn-secondary:hover {
        background-color: #52525b; /* Zinc 600 */
    }
    .btn-green {
        background-color: #16a34a; /* Green 600 */
        color: #ffffff;
    }
    .btn-green:hover {
        background-color: #15803d; /* Green 700 */
    }

    /* Stat Cards Grid */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background-color: var(--bg-secondary);
        border: 1px solid var(--border-primary);
        border-radius: 8px;
        padding: 1.5rem;
    }
    .stat-card .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .stat-card .title {
        font-size: 0.875rem; /* 14px */
        font-weight: 500;
        color: var(--text-secondary);
    }
    .stat-card .icon {
        stroke: var(--text-secondary);
    }
    .stat-card .value {
        font-size: 2.25rem; /* 36px */
        font-weight: 600;
        color: #ffffff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Table */
    .table-container {
        width: 100%;
        overflow-x: auto;
        background-color: var(--bg-secondary);
        border: 1px solid var(--border-primary);
        border-radius: 8px;
    }
    .report-table {
        width: 100%;
        border-collapse: collapse;
    }
    .report-table th,
    .report-table td {
        padding: 0.75rem 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-primary);
        font-size: 0.875rem;
        white-space: nowrap;
    }
    .report-table thead th {
        background-color: #3f3f46; /* Zinc 700 */
        color: #ffffff;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .report-table tbody tr:hover {
        background-color: #333338; /* Slightly lighter */
    }
    .report-table td:last-child,
    .report-table th:last-child,
    .report-table td:nth-child(2),
    .report-table th:nth-child(2),
    .report-table td:nth-child(3),
    .report-table th:nth-child(3),
    .report-table td:nth-child(4),
    .report-table th:nth-child(4) {
        text-align: right;
    }
    
    .status-success { color: var(--green); }
    .status-fail { color: var(--red); }
    .status-total { color: var(--text-primary); }

    .no-data {
        text-align: center;
        padding: 3rem;
        color: var(--text-secondary);
        font-size: 1rem;
    }

    footer {
        text-align: center;
        margin-top: 2rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    /* Print Styles */
    @media print {
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #ffffff;
            --border-primary: #dee2e6;
            --text-primary: #212529;
            --text-secondary: #6c757d;
        }
        body {
            background-color: #ffffff;
            color: #000000;
            padding: 0;
            margin: 15mm;
        }
        .container {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }
        .controls-form, .btn {
            display: none !important;
        }
        .report-header h1 {
            color: #000000;
        }
        .stat-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        .stat-card {
            border: 1px solid var(--border-primary);
            box-shadow: none;
        }
        .table-container {
            border: 1px solid var(--border-primary);
            overflow: hidden;
        }
        .report-table th,
        .report-table td {
            border-bottom: 1px solid var(--border-primary);
        }
        .report-table thead th {
            background-color: #f8f9fa;
            color: #000000;
        }
        footer {
            color: #000000;
        }
    }
</style>
</head>
<body>

<div class="container">
    <header class="report-header">
        <h1><i data-lucide="bar-chart-3"></i> Learnix Performance Report</h1>
        <p class="subtitle">
            From <strong><?= htmlspecialchars(date('d M Y', strtotime($from_date))) ?></strong>
            to <strong><?= htmlspecialchars(date('d M Y', strtotime($to_date))) ?></strong><br>
            <span class="generated-on">Generated on <?= $generated_on ?></span>
        </p>
    </header>

    <form method="GET" class="controls-form">
        <div class="form-group">
            <label for="from">From:</label>
            <input type="date" id="from" name="from" value="<?= htmlspecialchars($from_date) ?>">
            <label for="to">To:</label>
            <input type="date" id="to" name="to" value="<?= htmlspecialchars($to_date) ?>">
        </div>
        <div class="form-group">
            <button class="btn btn-primary" type="submit">
                <i data-lucide="search"></i> Generate Report
            </button>
            <button type="button" class="btn btn-green" onclick="window.print()">
                <i data-lucide="printer"></i> Print / PDF
            </button>
            <a href="../dashboard.php" class="btn btn-secondary">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>
        </div>
    </form>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="header">
                <span class="title">Total Users</span>
                <i class="icon" data-lucide="users-2"></i>
            </div>
            <p class="value"><?= $user_result['total_users'] ?? 0 ?></p>
        </div>
        <div class="stat-card">
            <div class="header">
                <span class="title">Total Submissions</span>
                <i class="icon" data-lucide="file-stack"></i>
            </div>
            <p class="value"><?= $total_sub ?></p>
        </div>
        <div class="stat-card">
            <div class="header">
                <span class="title">Successful</span>
                <i class="icon" data-lucide="check-circle"></i>
            </div>
            <p class="value" style="color: var(--green);"><?= $total_success ?></p>
        </div>
        <div class="stat-card">
            <div class="header">
                <span class="title">Success Rate</span>
                <i class="icon" data-lucide="trending-up"></i>
            </div>
            <p class="value"><?= $success_rate ?>%</p>
        </div>
        <div class="stat-card">
            <div class="header">
                <span class="title">Most Active User</span>
                <i class="icon" data-lucide="award"></i>
            </div>
            <p class="value"><?= htmlspecialchars($active_user_result['username'] ?? 'N/A') ?></p>
        </div>
        <div class="stat-card">
            <div class="header">
                <span class="title">Top Category</span>
                <i class="icon" data-lucide="tag"></i>
            </div>
            <p class="value"><?= htmlspecialchars($category_result['category_name'] ?? 'N/A') ?></p>
        </div>
    </div>

    <div class="table-container">
        <?php if (count($table_rows) > 0): ?>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Total Submissions</th>
                        <th>Successful</th>
                        <th>Failed</th>
                        <th>Success %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($table_rows as $row): 
                        $rate = $row['total'] > 0 ? round(($row['success'] / $row['total']) * 100, 2) : 0;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td class="status-total"><?= (int)$row['total'] ?></td>
                        <td class="status-success"><?= (int)$row['success'] ?></td>
                        <td class="status-fail"><?= (int)$row['failed'] ?></td>
                        <td><?= $rate ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No submissions found for the selected date range.</p>
        <?php endif; ?>
    </div>

    <footer>Generated by Learnix © <?= date('Y') ?></footer>
</div>

<!-- Lucide Icons Script -->
<script src="https://unpkg.com/lucide@latest/dist/lucide.min.js"></script>
<script>
    lucide.createIcons();
</script>

</body>
</html>