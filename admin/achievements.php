<?php
include '../utils/connect.php';

if($_SESSION['logid'] !== 1){
    header('Location: ../login/login.php');
    exit;
  }

$stmt = $pdo->query("SELECT * FROM achievement");
$achieve = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = null;
$title = null;

if (isset($_SESSION['toast'])) {
  $msg = $_SESSION['toast']['msg'];
  $title = $_SESSION['toast']['title'];
  unset($_SESSION['toast']);
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | Submissions</title>
  <link rel="stylesheet" href="../styles/admin_dash.css">
   <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <link rel="stylesheet" href="../styles/toast.css">
  <link rel="stylesheet" href="../styles/achieve.css">
 

</head>
<body>
    <!--Toast Message-->
    <div class="notification"></div>

    <div class="welcome-message">
        <div class="welcome-header">
            <h1>Achievements</h1>
            <p class="welcome-text">Celebrating Milestone, Shaping future</p>
        </div>
        <div class="profile-icon">
            <img src="../uploads/no_profile.png" alt="">
            <span class="profile-span">View profile</span>
        </div>
    </div>

   <a href="#achievement-form-container" style="text-decoration: none;"> <button onclick="toggleAchievementForm()" class="achieve-btn">Add +</button> </a>

    <!--Achievements display-->
    <div class="display-grid-card">
        <?php foreach($achieve as $item): ?>
        <div class="achieve-card">
            <div class="icon-achieve">
                <i class="<?= htmlspecialchars($item['icon']) ?>"></i>
            </div>
            <div class="text-block">
                <h1 class="title"><?= htmlspecialchars($item['title']) ?></h1>
                <p class="description"><?= htmlspecialchars($item['description']) ?></p>
            </div>

             <div class="achieve-actions">
                <form action="functions/edit-achievements.php" method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <button type="submit" class="edit-btn"><i class="fa-solid fa-pen"></i> Edit</button>
                </form>
                <form action="functions/delete-achievement.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this achievement?');">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <button type="submit" class="delete-btn"><i class="fa-solid fa-trash"></i> Delete</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>


    

    <!-- Add achievements form (hidden by default) -->
    <div id="achievement-form-container" style="display: none;">
        <div class="topic-div-line visible-line" id="topic-div-line"></div>
        <div class="btn-add-id">
            <button id="close-add-achievement" onclick="toggleAchievementForm()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <section id="add-topic" class="add-topic">
            <h1 class="topic-title">Add Achievement</h1>
            <form action="functions/add-achievement.php" method="post">
                <input type="text" name="name" placeholder="Title" class="title-input" >
                <input type="text" name="description" placeholder="Achievement description" class="title-input description-input" >
                <div class="img-input">
                    <label>Icon</label>
                    <div class="icon-select">
                        <select name="icon-img" id="icon-img" >
                            <?php foreach($icons as $icon_item): ?>
                                <option value="<?= htmlspecialchars($icon_item) ?>"><?= htmlspecialchars($icon_item) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i id="icon-preview" class="<?= htmlspecialchars($icons[0]) ?>"></i>
                    </div>
                </div>
                <input type="submit" name="submit" value="Add" class="title-add-btn">
            </form>
        </section>
    </div>

    <div class="ignore"></div>

    <?php if(isset($msg) && isset($title)): ?>
    <script>
        window.toastMsgData = {
            title: <?= json_encode($title) ?>,
            msg: <?= json_encode($msg) ?> 
        }
    </script>
    <?php endif; ?>

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
        function toggleAchievementForm() {
            const formContainer = document.getElementById('achievement-form-container');
            formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
              
        }

        // Update icon preview when selection changes
        document.getElementById('icon-img').addEventListener('change', function() {
            const iconPreview = document.getElementById('icon-preview');
            iconPreview.className = this.value;
        });
    </script>

    <script src="../scripts/add-user-toast.js" type="module"></script>
  
</body>
</html>