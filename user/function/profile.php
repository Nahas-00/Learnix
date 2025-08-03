<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User | Profile</title>
  <link rel="icon" href="../../assets/images/web_icon.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/profile.css">
</head>
<body>
  <!--Header-->
  <header>
    <div class="container">
      <nav>
        <div>
        <a href="#" class="logo">
          <span>^_^</span>
            <span>Learnix</span>
        </a>
        </div>

            <div class="nav-links">
                <a href="#">Logout</a>
                <a href="#">Dashboard</a>
            </div>
      </nav>
    </div>
  </header>

  <!--profile header-->
  <section class="profile-header">
    <div class="profile-background"></div>
      <div class="container">
        <div class="profile-avatar"></div>

        <div class="profile-info">
          <h1>John Doe</h1>
          <div class="profile-username">
            <span>Email100@gmail.com</span>
            <i class="fas fa-badge-check" style="color: var(--primary);"></i>
          </div>

          <div class="profile-badges">
            <div class="badge">
              <i class="fas fa-calendar-alt"></i>
              Last Active: Jan 2023
            </div>

            <div class="badge">
           <i class="fas fa-graduation-cap"></i>
            Computer Science
            </div>
            
          </div>

          <div class="profile-stats">
            <div class="stat-item">
              <div class="stat-value">124</div>
              <div class="stat-label">Problems Solved</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">92%</div>
              <div class="stat-label">Accuracy</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">42</div>
              <div class="stat-label">Day Streak</div>
            </div>
          </div>
        </div>
      </div>
  </section>

  <div class="container">
    <div class="profile-content">
      <div class="left-column">
        <!--Solved problems-->
        <div class="card">
          <div class="card-header">
            <h2>Recently Solved Problems</h2>
            <a href="">View All</a>
          </div>

          <div class="problems-grid">
            <div class="problem-card">
              <div class="problem-header">
                <h3 class="problem-title">Two Sum</h3>
                <span class="problem-difficulty difficulty-easy">Easy</span>
                </div>
                <div class="problem-meta">
                  <span>Array , Hash table</span>
                  <span>Solved: 2 Days ago</span>
                </div>
                <p class="problem-description">Find indices of the two numbers such that they add up to target.</p>
                <div class="problem-footer">
                    <button class="btn btn-outline">View Solution</button>
              </div>
            </div>

            <div class="problem-card">
              <div class="problem-header">
                <h3 class="problem-title">Two Sum</h3>
                <span class="problem-difficulty difficulty-easy">Easy</span>
                </div>
                <div class="problem-meta">
                  <span>Array , Hash table</span>
                  <span>Solved: 2 Days ago</span>
                </div>
                <p class="problem-description">Find indices of the two numbers such that they add up to target.</p>
                <div class="problem-footer">
                    <button class="btn btn-outline">View Solution</button>
              </div>
            </div>

            <div class="problem-card">
              <div class="problem-header">
                <h3 class="problem-title">Two Sum</h3>
                <span class="problem-difficulty difficulty-easy">Easy</span>
                </div>
                <div class="problem-meta">
                  <span>Array , Hash table</span>
                  <span>Solved: 2 Days ago</span>
                </div>
                <p class="problem-description">Find indices of the two numbers such that they add up to target.</p>
                <div class="problem-footer">
                    <button class="btn btn-outline">View Solution</button>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="right-column">
        
      <div class="card">
        <h2>Achievements</h2>
        <a href="">View All</a>
      </div>

      <div class="achievements-grid">
        <div class="achievement-item">
          <div class="achievement-icon">
              <i class="fas fa-fire"></i>
          </div>
          <div class="achievement-title">30-Day Streak</div>
      </div>

      <div class="achievement-item">
          <div class="achievement-icon">
              <i class="fas fa-fire"></i>
          </div>
          <div class="achievement-title">30-Day Streak</div>
      </div>

      <div class="achievement-item">
          <div class="achievement-icon">
              <i class="fas fa-fire"></i>
          </div>
          <div class="achievement-title">30-Day Streak</div>
      </div>
      </div>
      </div>
    </div>
  </div>

  <script>
     document.querySelectorAll('.achievement-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                const icon = this.querySelector('.achievement-icon');
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease';
            });
            
            item.addEventListener('mouseleave', function() {
                const icon = this.querySelector('.achievement-icon');
                icon.style.transform = 'scale(1)';
            });
        });
  </script>
</body>
</html>