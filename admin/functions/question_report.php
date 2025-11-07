<?php
include_once '../../utils/connect.php';
session_start();

// ✅ Only admin access
if (!isset($_SESSION['logid']) || $_SESSION['logid'] !== 1) {
    header('Location: ../../login/login.php');
    exit();
}

// ----------------------------
//  Filters: Date + Question
// ----------------------------
$from_date = $_GET['from'] ?? date('Y-m-01');
$to_date   = $_GET['to'] ?? date('Y-m-t');
$qid       = $_GET['qid'] ?? 'all';  // question id or 'all'

// Fetch all questions for dropdown
$question_stmt = $pdo->query("SELECT id, title FROM question ORDER BY id ASC");
$questions = $question_stmt->fetchAll(PDO::FETCH_ASSOC);

$period_label = date('d M Y', strtotime($from_date)) . " to " . date('d M Y', strtotime($to_date));
$generated_on = date('d M Y, h:i A');

$params = [
    ':from' => $from_date . " 00:00:00",
    ':to'   => $to_date . " 23:59:59"
];

/* -------------------------------
   1️⃣ ALL QUESTIONS MODE
--------------------------------*/
if ($qid === 'all') {
    $report_title = "All Questions Summary";

    $query = "SELECT 
                q.id AS qid,
                q.title AS title,
                c.name AS category,
                COUNT(s.id) AS total_attempts,
                SUM(CASE WHEN s.result='Success' THEN 1 ELSE 0 END) AS successful,
                SUM(CASE WHEN s.result!='Success' THEN 1 ELSE 0 END) AS failed
              FROM question q
              LEFT JOIN category c ON q.category_id = c.id
              LEFT JOIN submission s ON s.qid = q.id
                AND s.timestamp BETWEEN :from AND :to
              GROUP BY q.id, q.title, c.name
              ORDER BY total_attempts DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* -------------------------------
   2️⃣ SINGLE QUESTION MODE
--------------------------------*/
else {
    $report_title = "Detailed Report for Question ID #$qid";

    // Question info
    $qinfo_stmt = $pdo->prepare("SELECT q.title, q.description, c.name AS category 
                                 FROM question q 
                                 JOIN category c ON q.category_id = c.id 
                                 WHERE q.id = :id");
    $qinfo_stmt->execute([':id' => $qid]);
    $question_info = $qinfo_stmt->fetch(PDO::FETCH_ASSOC);

    // Submissions for this question
    $sub_query = "SELECT u.username, s.result, s.timestamp, s.code 
                  FROM submission s
                  JOIN users u ON s.uid = u.id
                  WHERE s.qid = :qid AND s.timestamp BETWEEN :from AND :to
                  ORDER BY s.timestamp DESC";
    $sub_stmt = $pdo->prepare($sub_query);
    $sub_stmt->bindValue(':qid', $qid, PDO::PARAM_INT);
    $sub_stmt->bindValue(':from', $params[':from']);
    $sub_stmt->bindValue(':to', $params[':to']);
    $sub_stmt->execute();
    $submissions = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Learnix Question Report</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #fff;
  color: #222;
  margin: 30px;
}
h1, h2 {
  text-align: center;
  color: #4A00E0;
  margin: 0 0 10px 0;
}
.subtitle {
  text-align: center;
  color: #666;
  font-size: 14px;
  margin-bottom: 25px;
}
form {
  text-align: center;
  margin-bottom: 30px;
}
input[type=date], select {
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  margin: 0 5px;
}
button {
  padding: 9px 18px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
}
.generate-btn { background: #007bff; color: #fff; }
.print-btn { background: #28a745; color: #fff; margin-left: 8px; }

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 25px;
}
th, td {
  border: 1px solid #ccc;
  padding: 10px;
  text-align: center;
  font-size: 14px;
}
th { background: #f6f6f6; }

details summary {
  cursor: pointer;
  color: #007bff;
  font-weight: 600;
  margin-bottom: 5px;
}
pre {
  background: #f8f9fc;
  padding: 10px;
  border-radius: 6px;
  text-align: left;
  overflow-x: auto;
  font-size: 13px;
}
footer {
  text-align: center;
  margin-top: 40px;
  font-size: 13px;
  color: #777;
}
.btn-secondary{
  text-decoration: none;
  margin-left: 5px;
  background: #512151;
  color: #fff;
  padding: 0.58rem;
  border-radius: 8px;
  cursor: pointer;
}
@media print {
  form, button { display: none !important; }
  body { margin: 15mm; }
  summary { color: #000; }
}
</style>
</head>
<body>

<h1>❓ Learnix Question Report</h1>
<p class="subtitle">
  <?= htmlspecialchars($report_title) ?><br>
  From <strong><?= htmlspecialchars(date('d M Y', strtotime($from_date))) ?></strong>
  to <strong><?= htmlspecialchars(date('d M Y', strtotime($to_date))) ?></strong><br>
  Generated on <?= $generated_on ?>
</p>

<form method="GET">
  <label>From:</label>
  <input type="date" name="from" value="<?= htmlspecialchars($from_date) ?>">
  <label>To:</label>
  <input type="date" name="to" value="<?= htmlspecialchars($to_date) ?>">
  <label>Question:</label>
  <select name="qid">
    <option value="all" <?= $qid==='all'?'selected':'' ?>>All Questions</option>
    <?php foreach($questions as $q): ?>
      <option value="<?= $q['id'] ?>" <?= $qid==$q['id']?'selected':'' ?>>
        <?= htmlspecialchars($q['title']) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <button class="generate-btn" type="submit">Generate</button>
  <button type="button" class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
  <a href="../dashboard.php" class="btn btn-secondary">
      <i data-lucide="layout-dashboard"></i> Dashboard
  </a>
</form>

<?php if($qid === 'all'): ?>

  <?php if(count($data) > 0): ?>
  <table>
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Category</th>
      <th>Total Attempts</th>
      <th>Successful</th>
      <th>Failed</th>
      <th>Success %</th>
    </tr>
    <?php foreach($data as $row): 
      $rate = $row['total_attempts']>0 ? round(($row['successful']/$row['total_attempts'])*100,2) : 0;
    ?>
    <tr>
      <td><?= $row['qid'] ?></td>
      <td><?= htmlspecialchars($row['title']) ?></td>
      <td><?= htmlspecialchars($row['category']) ?></td>
      <td><?= $row['total_attempts'] ?></td>
      <td><?= $row['successful'] ?></td>
      <td><?= $row['failed'] ?></td>
      <td><?= $rate ?>%</td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?>
    <p style="text-align:center;color:#999;">No questions found in this period.</p>
  <?php endif; ?>

<?php else: ?>

  <?php if($question_info): ?>
    <h2><?= htmlspecialchars($question_info['title']) ?></h2>
    <p style="text-align:center;color:#555;">
      <strong>Category:</strong> <?= htmlspecialchars($question_info['category']) ?>
    </p>

    <?php if(count($submissions) > 0): ?>
    <table>
      <tr>
        <th>User</th>
        <th>Result</th>
        <th>Timestamp</th>
        <th>Code</th>
      </tr>
      <?php foreach($submissions as $s): ?>
      <tr>
        <td><?= htmlspecialchars($s['username']) ?></td>
        <td><?= htmlspecialchars($s['result']) ?></td>
        <td><?= htmlspecialchars($s['timestamp']) ?></td>
        <td>
          <?php if(!empty($s['code'])): ?>
          <details><summary>View Code</summary>
            <pre><code><?= htmlspecialchars($s['code']) ?></code></pre>
          </details>
          <?php else: ?> —
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php else: ?>
      <p style="text-align:center;color:#999;">No submissions found for this question.</p>
    <?php endif; ?>
  <?php else: ?>
    <p style="text-align:center;color:#999;">Invalid question selected.</p>
  <?php endif; ?>

<?php endif; ?>

<footer>Generated by Learnix © <?= date('Y') ?></footer>
</body>
</html>
