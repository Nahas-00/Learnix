<?php
  include_once '../../utils/connect.php';

  session_start();

  if($_SESSION['logid'] !== 1){
    header('Location: ../../login/login.php');
    exit;
  }

  $id=$_POST['id'];
  $stmt = $pdo->prepare("SELECT * FROM achievement WHERE id = :id");
  $stmt->execute(['id'=>$id]);
  $item = $stmt->fetch(PDO::FETCH_ASSOC);

   $icons = [
      "fa-solid fa-trophy", "fa-solid fa-star", "fa-solid fa-medal", "fa-solid fa-crown",
      "fa-solid fa-fire", "fa-solid fa-bolt", "fa-solid fa-gem", "fa-solid fa-rocket",
      "fa-solid fa-shield-halved", "fa-solid fa-brain", "fa-solid fa-mountain", "fa-solid fa-heart",
      "fa-solid fa-check-circle", "fa-solid fa-graduation-cap", "fa-solid fa-code", "fa-solid fa-lightbulb",
      "fa-solid fa-user-astronaut", "fa-solid fa-puzzle-piece", "fa-solid fa-ribbon", "fa-solid fa-thumbs-up",
      "fa-solid fa-handshake", "fa-solid fa-briefcase", "fa-solid fa-magic", "fa-solid fa-microchip",
      "fa-solid fa-calendar-check", "fa-solid fa-user-ninja", "fa-solid fa-compass", "fa-solid fa-globe",
      "fa-solid fa-book", "fa-solid fa-book-open", "fa-solid fa-award", "fa-solid fa-shield",
      "fa-solid fa-star-half-stroke", "fa-solid fa-circle-check", "fa-solid fa-flag-checkered", "fa-solid fa-arrow-up-right-dots",
      "fa-solid fa-ranking-star", "fa-solid fa-diagram-successor", "fa-solid fa-scale-balanced", "fa-solid fa-hands-clapping",
      "fa-solid fa-chart-line", "fa-solid fa-bug", "fa-solid fa-gear", "fa-solid fa-bell",
      "fa-solid fa-pen-nib", "fa-solid fa-laptop-code", "fa-solid fa-code-branch", "fa-solid fa-cubes",
      "fa-solid fa-dumbbell", "fa-solid fa-user-graduate", "fa-solid fa-circle-nodes", "fa-solid fa-comments",
      "fa-solid fa-wand-magic-sparkles", "fa-solid fa-infinity", "fa-solid fa-terminal", "fa-solid fa-tree",
      "fa-solid fa-circle-dot", "fa-solid fa-vial", "fa-solid fa-lightbulb-on", "fa-solid fa-fingerprint",
      "fa-solid fa-eye", "fa-solid fa-skull", "fa-solid fa-dragon", "fa-solid fa-mask",
      "fa-solid fa-wand-sparkles", "fa-solid fa-trophy-star", "fa-solid fa-cloud-sun", "fa-solid fa-cloud-moon",
      "fa-solid fa-meteor", "fa-solid fa-bolt-lightning", "fa-solid fa-user-secret", "fa-solid fa-vr-cardboard",
      "fa-solid fa-database", "fa-solid fa-graduation-cap", "fa-solid fa-shapes", "fa-solid fa-circle-radiation",
      "fa-solid fa-flask", "fa-solid fa-magnifying-glass", "fa-solid fa-circle-exclamation", "fa-solid fa-face-smile",
      "fa-solid fa-face-laugh", "fa-solid fa-face-kiss-wink-heart", "fa-solid fa-face-grin-stars", "fa-solid fa-face-surprise",
      "fa-solid fa-hand-peace", "fa-solid fa-person-running", "fa-solid fa-user-tie", "fa-solid fa-rocket-launch",
      "fa-solid fa-star-sharp", "fa-solid fa-award-simple", "fa-solid fa-people-group", "fa-solid fa-circle-half-stroke",
      "fa-solid fa-hands-holding", "fa-solid fa-hourglass-half", "fa-solid fa-palette", "fa-solid fa-masks-theater",
      "fa-solid fa-gamepad", "fa-solid fa-hand-holding-heart", "fa-solid fa-certificate", "fa-solid fa-bolt-slash","fa-solid fa-crosshairs"
      ,"fa-solid fa-cookie-bite","fa-solid fa-hand-fist","fa-solid fa-soap"
      ];


      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['id'], $_POST['title'], $_POST['description'], $_POST['icon'])) {
            $id = intval($_POST['id']);
            $title = $_POST['title'];
            $desc = $_POST['description'];
            $icon = $_POST['icon'];

            $stmt = $pdo->prepare("UPDATE achievement SET title=?, description=?, icon=? WHERE id=?");
            if ($stmt->execute([$title, $desc, $icon, $id])) {
                $_SESSION['toast'] = ['title' => 'Success', 'msg' => 'Achievement updated successfully'];
            } else {
                $_SESSION['toast'] = ['title' => 'Error', 'msg' => 'Failed to update achievement'];
            }

            header("Location: ../dashboard.php?page=achievements");
            exit;
        }
  }
?>

<?php if(isset($item)): ?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Achievement</title>
  <link rel="stylesheet" href="../../styles/admin_dash.css">
  <link rel="stylesheet" href="../../styles/edit-achieve.css">
     <link rel="icon" href="../../assets/images/web_icon.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
  <div class="edit-container">
      <h1>Edit Achievement</h1>
      <form method="post">
        <input type="hidden" name="id" value="<?= $item['id'] ?>">

        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($item['title']) ?>">

        <label for="description">Description</label>
        <input type="text" id="description" name="description" value="<?= htmlspecialchars($item['description']) ?>">

        <label for="icon">Icon</label>
        <div class="icon-select">
          <select id="icon-img" name="icon">
            <?php foreach($icons as $icon_item): ?>
              <option value="<?= htmlspecialchars($icon_item) ?>" <?= $item['icon']===$icon_item?'selected':'' ?>>
                <?= htmlspecialchars($icon_item) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <i id="icon-preview" class="<?= htmlspecialchars($item['icon']) ?>"></i>
        </div>

        <button type="submit" class="save-btn">
          <i class="fa-solid fa-floppy-disk"></i> Save Changes
        </button>

        <a href="../dashboard.php?page=achievements" class="back-btn">
          <i class="fa-solid fa-arrow-left"></i> Back
        </a>
      </form>
  </div>




  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  $(document).ready(function () {
    // Initialize Select2
    $('#icon-img').select2();

    // Update icon preview on change
    $('#icon-img').on('change', function () {
      const selectedIcon = $(this).val(); // Get selected value
      $('#icon-preview').attr('class', selectedIcon); // Update <i> tag class
    });
  });
</script>


  <script>
    // Update icon preview on select change
    document.getElementById('icon-img').addEventListener('change', function() {
      document.getElementById('icon-preview').className = this.value;
    });
  </script>
</body>
</html>
<?php endif; ?>
