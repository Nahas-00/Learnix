
<?php
require_once '../auth/auth_validate.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="../assets/images/web_icon.png" type="image/png">
  <link rel="stylesheet" href="../styles/admin_dash.css">
  <link rel="stylesheet" href="../styles/toast.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Space+Grotesk:wght@400;600&display=swap">
  <title>Admin | Dashboard</title>
</head>
<body>
  <div class="left">
      <div class="logo">
          <span class="icon">^_^</span>
          <span class="title">Learnix</span>
      </div>

      <div class="nav-bar">
      
          <a href="" class="nav-item active">
            <i class="fa-solid fa-gauge-high nav-icon"></i>
            Dashboard
          </a>
          <a href="" class="nav-item">
            <i class="fa-solid fa-users nav-icon"></i>
            Users
          </a>
          <a href="" class="nav-item">
            <i class="fa-solid fa-laptop-code nav-icon"></i>
            Questions
          </a>
          <a href="" class="nav-item">
            <i class="fa-solid fa-code nav-icon"></i>
            Submissions
          </a>
        
      </div>
  </div>

  <div class="right">

    <div class="welcome-message">
     <div class="welcome-header">
       <h1>Welcome , Admin</h1>
      <p class="welcome-text">Here's what's happening with your platform</p>
     </div>

         <div class="logout-btn">
         <a href="../auth/logout.php"><button type="submit" >Logout</button></a>
      </div>

      <div class="profile-icon">
        <img src="../uploads/no_profile.png" alt="">
        <span class="profile-span">View profile</span>
      </div>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="card-title">
                <h3 class="card-heading">Total Users</h3>
                <div class="card-icon">
                    <i class="fa-solid fa-users nav-icon"></i>
                </div>
            </div>
              <div class="card-value">270</div>
        </div>

         <div class="card">
            <div class="card-title">
                <h3 class="card-heading">Total Questions</h3>
                <div class="card-icon">
                    <i class="fa-solid fa-laptop-code nav-icon"></i>
                </div>
            </div>
              <div class="card-value">170</div>
        </div>

         <div class="card">
            <div class="card-title">
                <h3 class="card-heading">Total Submissions</h3>
                <div class="card-icon">
                    <i class="fa-solid fa-code nav-icon"></i>
                </div>
            </div>
              <div class="card-value">100</div>
        </div>

    </div>

    <!--Submissions table Section-->


    <div class="table-section">
        <div class="table-header">
          <h1 class="table-title">Recent Submissions</h1>
          <a href="#" class="view-link">View All</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Problem</th>
                    <th>Language</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="user-avatar"><img src="../uploads/20250102_231850.jpg" alt=""></div>
                <span>John Doe</span>
              </div>
            </td>
            <td>Two Sum</td>
            <td>Python</td>
            <td><span class="status status-success">Accepted</span></td>
            <td>2023-11-15</td>
          </tr>
          <tr>
            <td>
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="user-avatar"><img src="../uploads/no_profile.png" alt=""></div>
                <span>Alice Smith</span>
              </div>
            </td>
            <td>Reverse String</td>
            <td>JavaScript</td>
            <td><span class="status status-failed">Failed</span></td>
            <td>2023-11-14</td>
          </tr>
            </tbody>
        </table>
    </div>


<!--Questions table Section-->

    <div class="table-section">
        <div class="table-header">
          <h1 class="table-title">Recent Submissions</h1>
          <a href="#" class="view-link">View All</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Difficulty</th>
                    <th>Attempts</th>
                </tr>
            </thead>

            <tbody>
               <tr>
            <td>Two Sum</td>
            <td><span class="problem-tag">Easy</span></td>
            <td>1,248</td>
            
          </tr>
          <tr>
            <td>Reverse String</td>
            <td><span class="problem-tag">Hard</span></td>
            <td>987</td>
          </tr>
            </tbody>
        </table>
    </div>


      <div class="not-used"></div>
  </div>
</body>
</html>