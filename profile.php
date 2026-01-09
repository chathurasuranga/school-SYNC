<?php 
require 'db.php'; 
include 'header.php'; 

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Default image if none uploaded
$profile_pic = $user['profile_pic'] ? 'uploads/' . $user['profile_pic'] : 'https://via.placeholder.com/150';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="d-flex align-items-center mb-4">
            <h2 class="fw-bold mb-0">My Profile</h2>
        </div>

        <!-- Success Message -->
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Profile updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm p-4">
            <form action="actions/update_profile.php" method="POST" enctype="multipart/form-data">
                
                <!-- Profile Image Section -->
                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <img src="<?php echo htmlspecialchars($profile_pic); ?>" 
                             alt="Profile" 
                             class="rounded-circle border" 
                             style="width: 120px; height: 120px; object-fit: cover;">
                        
                        <!-- Hidden File Input -->
                        <label for="fileUpload" class="position-absolute bottom-0 end-0 bg-primary text-white p-2 rounded-circle cursor-pointer shadow-sm" style="cursor: pointer;">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M10.5 8.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2zm.5 2a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm9 2.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0z"/></svg>
                        </label>
                        <input type="file" id="fileUpload" name="profile_pic" class="d-none" onchange="previewImage(event)">
                    </div>
                    <div class="small text-muted mt-2">Click icon to change photo</div>
                </div>

                <!-- Form Fields -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>

                <div class="mb-3">
                     <label class="form-label fw-bold">Contact Number</label>
                    <input type="text" name="phone_number" class="form-control" placeholder="+94 77 123 4567" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                    <div class="form-text">Username cannot be changed.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Bio / About Me</label>
                    <textarea name="bio" class="form-control" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio']); ?></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary py-2 fw-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Script -->
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.querySelector('img');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

<?php include 'footer.php'; ?>