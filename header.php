<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// Get the current page name for "active" highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SchoolSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<script>
    // Initialize Theme immediately to prevent flash
    (function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    })();
</script>

    <!-- Mobile Top Bar -->
    <div class="mobile-header d-md-none sticky-top">
        <span class="fw-bold text-primary fs-5">SchoolSync</span>
        <button class="btn btn-light border" onclick="toggleSidebar()">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/></svg>
        </button>
    </div>

    <!-- Sidebar overlay -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:900;"></div>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column p-4" id="sidebar">
            <div class="mb-5 px-1 d-flex align-items-center justify-content-between text-primary fw-bold">
                 <span class="fs-4 tracking-tight">SchoolSync</span>
                 <button class="btn btn-sm btn-light d-md-none" onclick="toggleSidebar()">✕</button>
            </div>
            
            <ul class="nav nav-pills flex-column mb-auto gap-2">
                
                <!-- ADMIN MENU -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a href="admin_dashboard.php" class="nav-link <?php echo $current_page == 'admin_dashboard.php' ? 'active' : ''; ?>">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin_teachers.php" class="nav-link <?php echo $current_page == 'admin_teachers.php' ? 'active' : ''; ?>">
                            Manage Teachers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin_students.php" class="nav-link <?php echo $current_page == 'admin_students.php' ? 'active' : ''; ?>">
                            Student List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="admin_classes.php" class="nav-link <?php echo $current_page == 'admin_classes.php' ? 'active' : ''; ?>">
                            All Classes
                         </a>
                    </li>

                <!-- TEACHER MENU -->
                <?php else: ?>
                    <li class="nav-item">
                        <a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm1.5 0a.5.5 0 0 0-.5.5v12a.5.5 0 0 0 .5.5h12a.5.5 0 0 0 .5-.5V2.5a.5.5 0 0 0-.5-.5h-12z"/></svg>
                            My Classes
                        </a>
                    </li>
                    <?php
                    // Check for Official Class
                    if (isset($pdo) && isset($_SESSION['user_id'])) {
                        $offStmt = $pdo->prepare("SELECT * FROM official_class_teachers WHERE teacher_id = ?");
                        $offStmt->execute([$_SESSION['user_id']]);
                        $offClass = $offStmt->fetch();
                        if ($offClass):
                    ?>
                    <li class="nav-item">
                        <a href="official_class_students.php" class="nav-link <?php echo $current_page == 'official_class_students.php' ? 'active' : ''; ?>">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.677-2.041 1.03-2.927.029-.074.06-.148.094-.22.149-.318.32-.624.51-.913.083-.124.17-.245.26-.363.072-.095.147-.186.223-.274.025-.03.05-.059.076-.088.025-.028.05-.056.077-.083.05-.051.102-.102.155-.151.052-.049.105-.098.158-.144.155-.136.319-.263.492-.382Zm12.042.843c.092.112.176.226.252.342.368.56.591 1.228.625 1.946.035.738-.135 1.458-.466 1.99a3.78 3.78 0 0 1-.168.254A2.256 2.256 0 0 1 19 14.5a2.5 2.5 0 0 1-2.5 2.5h-1a.5.5 0 0 1 0-1h1a1.5 1.5 0 0 0 1.5-1.5c0-.62-.357-1.157-.864-1.564-.09-.071-.184-.139-.281-.202-.676-.43-1.636-.264-2.126.368-.48.62-.257 1.62.408 2.127.664.506.602 1.503-.092 2.062-.505.405-1.227.42-1.79.035-.557-.38-.813-1.025-.776-1.63.037-.621.41-1.173.96-1.428.528-.245.89-.706.945-1.192.059-.517-.23-1.025-.722-1.24-.51-.223-1.11-.137-1.545.22-.44.364-.69.907-.638 1.487.052.57.41 1.066.907 1.303.468.223.714.653.642 1.062-.073.408-.432.736-.93.774-.954.072-1.625-.008-2.022-.24a1.86 1.86 0 0 1-.689-.806c-.197-.474-.216-1.028-.052-1.62.17-.61.54-1.203 1.054-1.678a3.28 3.28 0 0 1 1.765-.794c.06-.006.12-.01.18-.01.52.004 1.01.144 1.433.39.297.172.56.398.775.663Z"/></svg>
                            My Official Class (<?php echo $offClass['grade'] . $offClass['class_letter']; ?>)
                        </a>
                    </li>
                    <?php endif; } ?>
                    <li>
                        <a href="profile.php" class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                            Profile
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
            
            <div class="mt-auto pt-4 border-top border-secondary-subtle">
                <!-- Dark Mode Toggle -->
                <button class="btn btn-sm w-100 mb-3 d-flex align-items-center justify-content-center gap-2" 
                        onclick="toggleTheme()" 
                        style="background: var(--bg-body); color: var(--text-main); border: 1px solid var(--border-color);">
                    <svg id="moon-icon" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="display:none"><path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/></svg>
                    <svg id="sun-icon" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/></svg>
                    <span id="theme-text">Toggle Theme</span>
                </button>
                
                <div class="fw-bold text-truncate" style="color: var(--text-main);">
                    <?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : 'User'; ?>
                </div>
                <a href="logout.php" class="small text-danger text-decoration-none">Log out</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content w-100">
            
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('show');
    overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
}

function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateThemeIcon(next);
}

function updateThemeIcon(theme) {
    const sun = document.getElementById('sun-icon');
    const moon = document.getElementById('moon-icon');
    const text = document.getElementById('theme-text');
    
    if (theme === 'dark') {
        sun.style.display = 'block';
        moon.style.display = 'none';
        text.innerText = "Light Mode";
    } else {
        sun.style.display = 'none';
        moon.style.display = 'block';
        text.innerText = "Dark Mode";
    }
}

// Init Icon
document.addEventListener('DOMContentLoaded', () => {
    updateThemeIcon(document.documentElement.getAttribute('data-theme') || 'light');
});
</script>