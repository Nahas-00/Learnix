<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | User Manage</title>
    <link rel="stylesheet" href="../styles/admin_dash.css">
    <link rel="stylesheet" href="../styles/add-user.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
</head>
<body>
    <div class="welcome-message">
     <div class="welcome-header">
       <h1>Manage Users</h1>
      <p class="welcome-text">Add, Edit or Delete User</p>
     </div>

      <div class="profile-icon">
        <img src="../uploads/no_profile.png" alt="">
        <span class="profile-span">View profile</span>
      </div>
    </div>


    <div class="overlay" id="overlay"></div>
        <button onclick="openAddUser()">Add User</button>
        <!--Add user form-->

    <div class="add-user" id="add-user">
        <div class="add-user-title">
            <h1>Add User</h1>
            <i class="fa-solid fa-xmark" onclick="closeAddUser()"></i>
        </div>
        
        <form action="add-user.php" class="user-form">
            <div class="username">
            <label for="username">Username</label>
            <input type="text" placeholder="eg: John Doe">
            </div>

            <div class="email">
                <label for="username">Email</label>
                <input type="text" placeholder=" eg: test@gmail.com">
            </div>

            <div class="password">
                <label for="username">Password</label>
                <input type="password" placeholder="Atleast 5 character nad minimum 1 number">
            </div>

            <div class="submit-btn">
                <input type="submit" value="Add" name="submit">
            </div>
        </form>
    </div>

    <script src="../scripts/add-user.js"></script>
</body>
</html>