<?Php
  session_start();
  include_once '../../utils/connect.php';

  $user_id = $_SESSION['userid'] ?? null;
  $question_id = $_POST['question_id'] ?? null;
  $code = $_POST['code'] ?? '';
  $language_id = $_POST['language_id'] ?? null;
  $used_solution = $_POST['viewed_solution'] ?? 0;
  $status_tag = $_POST['status'] ?? 'failed';
  $status_tag = strtolower($status_tag) === 'success' ? 'Success' : 'Failed';

  if($used_solution == 1){
    $status_tag = 'Failed';
  }

  if (!$user_id || !$question_id || !$language_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
    exit;
  }

  $check_stmt = $pdo->prepare("SELECT id FROM submission WHERE uid = ? AND qid = ? AND result = 'Success' LIMIT 1");
  $check_stmt->execute([$user_id, $question_id]);
  $exists = $check_stmt->fetch();

  if ($exists) {
      echo json_encode([
          "status" => "info",
          "message" => "✅ You have already successfully submitted this problem!"
      ]);
      exit;
  }

 

  $stmt = $pdo->prepare("INSERT INTO submission (uid, qid, code, language, result) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$user_id, $question_id, $code, $language_id, $status_tag]);

  echo json_encode(['status' => 'success', 'message' => 'Submission recorded successfully.']);

?>