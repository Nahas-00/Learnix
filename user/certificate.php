<?php
  include '../utils/connect.php';
  try{
    session_start();
  }catch(error){
    
  }

    $user_id = $_SESSION['userid'];

  if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])){
    $name=trim($_POST['name']);
    if(!empty($name)){
      $check = $pdo->prepare("SELECT * FROM certificate WHERE user_id = ?");
      $check->execute([$user_id]);
      $existing = $check->fetch(PDO::FETCH_ASSOC);

      if (!$existing) {
        $insert = $pdo->prepare("INSERT INTO certificate (user_id, name) VALUES (?, ?)");
        $insert->execute([$user_id, $name]);
        $existing = ['name' => $name, 'issued_at' => date('Y-m-d H:i:s')];
      }
      echo "
        <div class='certificate'>
            <h2>Certificate of Completion</h2>
            <p>This is to certify that</p>
            <div class='name'>" . htmlspecialchars($existing['name']) . "</div>
            <p>has successfully completed the course.</p>
            <p><small>Issued on " . date('F j, Y', strtotime($existing['issued_at'])) . "</small></p>
        </div>
        ";
    }
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM certificate WHERE user_id = ?");
$stmt->execute([$user_id]);
$certificate = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT q.*
    FROM question q
    WHERE NOT EXISTS (
        SELECT 1
        FROM submission s
        WHERE s.qid = q.id
          AND s.uid = :userid
          AND s.result = 'Success'
    );
 ");

  $stmt->execute([':userid' => $user_id]);

  $ques = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>certificate</title>
  <link rel="stylesheet" href="../assets/user_dash.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
  <link rel="stylesheet" href="../assets/certificate.css">
</head>
<body>

  <?php if(!$ques): ?>
    <div class="no-cert">
      <h1>You are not eligible for certifictae</h1>
    </div>
    <?php else: ?>

    <?php if(!$certificate): ?>
    <div class="name-field">
      <form action="" id="certForm" method="post">
        <div>
      <input type="text" name="name" required placeholder="Enter name to print on certificate">
        </div>
        <div>
      <input type="submit" name="Submit" value="Generate Certificate" class="btn">
      </div>
      </form>
    </div>
    <?php else: ?>
      <div class="scroll-wrapper">
      <div class="certContainer" id="certContainer" >
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>
        
        <div class="seal">✧</div>
        <div class="logo-l">learnix</div>
        <div class="date">Issued on: <span contenteditable="false"><?= htmlspecialchars($certificate['issue_date']) ?></span></div>
        
        <div class="certificate-content">
            <div class="header">
                <h1>CERTIFICATE OF COMPLETION</h1>
                <div class="subtitle">HONORS OF ACHIEVEMENT</div>
            </div>
            
            <div class="content">
                <div class="description">
                    This prestigious certificate is proudly presented to
                </div>
                <div class="name" contenteditable="false"><?= htmlspecialchars($certificate['name']) ?></div>
                <div class="description">
                    for outstanding dedication in completing all questions on the learnix platform,
                    demonstrating exceptional commitment to knowledge and personal growth.
                </div>
            </div>
            
            <div class="signature-container">
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-name">Saimon Josh</div>
                    <div class="position">Head of Education</div>
                </div>
                
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-name">Muhammad Nahas P S</div>
                    <div class="position">CEO, learnix</div>
                </div>
            </div>
        </div>
    </div>
    </div>
      
      <button id="downloadBtn">Download Certificate</button>
    <?php endif; ?>

    <?php endif; ?>

    <div class="no-use"></div>

    <script>
      document.getElementById('certForm')?.addEventListener('submit',function(e){
        e.preventDefault();

        const form = e.target;
        const name = form.name.value.trim();

        if(!name) return;

        const formData = new FormData();
        formData.append('name',name);
        formData.append('ajax','1');

        fetch('certificate.php',{
          method: 'POST',
          body: formData
        }).then(res=>res.text())
        .then(html =>{
          form.classList.add('hidden');
          document.getElementById('certContainer').innerHTML = html;
        });
      });
      
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>

      document.getElementById('downloadBtn')?.addEventListener('click', () => {
          
        const cert = document.getElementById('certContainer');
        const originalClass = cert.className;

        cert.className = "fixed-size";
          html2canvas(cert).then(canvas => {
              const link = document.createElement('a');
              link.download = 'certificate.png';
              link.href = canvas.toDataURL('image/png');
              link.click();

               cert.className = originalClass;
          });
      });

     
    </script>

</body>
</html>