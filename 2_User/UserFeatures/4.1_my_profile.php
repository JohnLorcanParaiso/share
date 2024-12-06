<?php
require_once '../../2_User/UserBackend/userAuth.php';
require_once '../../2_User/UserBackend/db.php';

$login = new Login();
if (!$login->isLoggedIn()) {
    header('Location: ../../2_User/UserBackend/login.php');
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    $login->logout();
    header('Location: ../../2_User/UserBackend/login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'update_profile':
            // Handle profile update logic here
            break;
            
        case 'change_password':
            // Handle password change logic here
            break;
            
        // Include the same navigation cases as dashboard
        case 'dashboard':
            header("Location: 1_user_dashboard.php");
            exit();

        case 'create':
            header("Location: 2.1_create_new_report.php");
            exit();

        case 'view':
            header("Location: 3.1_view_reports.php");
            exit();

        case 'myProfile':
            header("Location: 4.1_my_profile.php");
            exit();

        case 'help':
            header("Location: 5_help_and_support.php");
            exit();

        case 'others':
            header("Location: 6_others.php");
            exit();

        case 'logout':
            $login->logout();
            header("Location: ../../2_User/UserBackend/login.php");
            exit();
        
        // Handle delete report action
        case 'delete_report':
            if (isset($_POST['report_id'])) {
                $deleteStmt = $pdo->prepare("DELETE FROM lost_reports WHERE id = ? AND user_id = ?");
                if ($deleteStmt->execute([$_POST['report_id'], $_SESSION['user_id']])) {
                    $_SESSION['success_message'] = "Report deleted successfully.";
                } else {
                    $_SESSION['error_message'] = "Failed to delete report.";
                }
                header("Location: 4.1_my_profile.php");
                exit();
            }
            break;
        
        case 'edit':
            if (isset($_POST['report_id'])) {
                header("Location: 4.2_edit_report.php?id=" . (int)$_POST['report_id']);
                exit();
            }
            break;
        
        case 'view_details':
            if (isset($_POST['report_id'])) {
                header("Location: 3.2_view_more.php?id=" . $_POST['report_id']);
                exit();
            }
            break;
    }
}

// Get user data from session
$username = $_SESSION['username'] ?? 'Guest';
$fullname = $_SESSION['fullname'] ?? 'Guest User';
$email = $_SESSION['email'] ?? '';

// Fetch user's reports
$reportsQuery = "SELECT r.*, GROUP_CONCAT(ri.image_path) as images 
                 FROM lost_reports r 
                 LEFT JOIN report_images ri ON r.id = ri.report_id 
                 WHERE r.user_id = ? 
                 GROUP BY r.id 
                 ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($reportsQuery);
$stmt->execute([$_SESSION['user_id']]);
$userReports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../4_Styles/user_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>User Profile</title>
</head>
<body>
<div class="side-menu">
    <div class="text-center">
        <img src="../../3_Images/logo.png" class="logo" style="width: 150px; height: 150px; margin: 20px auto; display: block;">
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <form method="POST">
                <button type="submit" name="action" value="dashboard" class="btn btn-link nav-link text-dark">
                    <i class="fas fa-home me-2"></i> Dashboard
                </button>
            </form>
        </li>
        <li class="nav-item">
            <form method="POST">
                <button type="submit" name="action" value="create" class="btn btn-link nav-link text-dark">
                    <i class="fas fa-plus-circle me-2"></i> Create New Report
                </button>
            </form>
        </li>
        <li class="nav-item">
            <form method="POST">
                <button type="submit" name="action" value="view" class="btn btn-link nav-link text-dark">
                    <i class="fas fa-eye me-2"></i> View Reports
                </button>
            </form>
        </li>
        <li class="nav-item">
            <form method="POST">
                <button type="submit" name="action" value="myProfile" class="btn btn-link nav-link text-dark active">
                    <i class="fas fa-user me-2"></i> My Profile
                </button>
            </form>
        </li>
        <li class="nav-item">
            <form method="POST">
                <button type="submit" name="action" value="help" class="btn btn-link nav-link text-dark">
                    <i class="fas fa-question-circle me-2"></i> Help and Support
                </button>
            </form>
        </li>
        <li class="nav-item">
            <form method="POST">
                <button type="submit" name="action" value="others" class="btn btn-link nav-link text-dark">
                    <i class="fas fa-ellipsis-h me-2"></i> Others
                </button>
            </form>
        </li>
        <li class="nav-item mt-auto">
            <form method="POST">
                <button type="submit" name="action" value="logout" class="btn btn-link nav-link text-dark">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>

<div class="container-custom">
    <header class="header-container mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <form method="POST" class="d-flex flex-grow-1">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search..">
                    <button type="submit" name="action" value="search" class="btn btn-outline-secondary">
                        <img src="../../3_Images/search.png" alt="search" style="width: 20px;">
                    </button>
                </div>
            </form>
            <div class="d-flex align-items-center gap-3">
                    <?php include '7_notifications.php'; ?>
                    <form method="POST" class="m-0">
                        <button type="submit" name="action" value="profile" class="btn rounded-circle p-0" style="width: 50px; height: 50px; overflow: hidden; border: none;">
                            <img src="../../3_Images/cat-user.png" alt="user profile" style="width: 100%; height: 100%; object-fit: cover;">
                        </button>
                </form>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Add this new card for profile picture and user info -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="position-relative d-inline-block">
                                        <?php
                                        // Fetch profile image from database
                                        $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $user = $stmt->fetch();
                                        $profileImage = isset($user['profile_image']) ? $user['profile_image'] : '../../3_Images/cat-user.png';
                                        ?>
                                        <img src="<?= htmlspecialchars($profileImage) ?>" 
                                             alt="Profile Picture" 
                                             class="rounded-circle" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle"
                                                style="width: 32px; height: 32px; padding: 0;"
                                                onclick="document.getElementById('profilePictureInput').click();">
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    </div>

                                    <!-- Hidden form for profile picture upload -->
                                    <form id="profilePictureForm" method="POST" enctype="multipart/form-data" style="display: none;">
                                        <input type="hidden" name="action" value="update_profile_picture">
                                        <input type="file" id="profilePictureInput" name="profile_picture" accept="image/*" 
                                               onchange="document.getElementById('profilePictureForm').submit();">
                                    </form>
                                </div>
                                <div class="col">
                                    <h4 class="mb-1"><?= htmlspecialchars($fullname) ?></h4>
                                    <p class="text-muted mb-0">@<?= htmlspecialchars($username) ?></p>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($email) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Your existing reports card continues here -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Reports List</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($userReports)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-folder-open text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                                    <p class="text-muted mt-3">You haven't created any reports yet.</p>
                                    <form method="POST">
                                        <button type="submit" name="action" value="create" class="btn btn-custom">
                                            Create Your First Report
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Cat</th>
                                                <th>Details</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Last Edited</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($userReports as $report): ?>
                                                <tr>
                                                    <td style="width: 100px;">
                                                        <?php 
                                                        $images = explode(',', $report['images']);
                                                        if (!empty($images[0])): 
                                                        ?>
                                                            <div class="report-images">
                                                                <?php foreach ($images as $index => $image): 
                                                                    if ($index < 3): // Show up to 3 images ?>
                                                                        <img src="<?= htmlspecialchars($image) ?>" 
                                                                             alt="<?= htmlspecialchars($report['cat_name']) ?>" 
                                                                             class="rounded report-thumbnail"
                                                                             onclick="openImageModal('<?= htmlspecialchars($image) ?>')"
                                                                             style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; margin: 2px;">
                                                                    <?php endif; 
                                                                    if ($index === 3): ?>
                                                                        <div class="more-images">+<?= count($images) - 3 ?></div>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="rounded bg-light d-flex align-items-center justify-content-center" 
                                                                 style="width: 80px; height: 80px;">
                                                                <i class="fas fa-cat text-muted" style="font-size: 2rem;"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <h6 class="mb-1"><?= htmlspecialchars($report['cat_name']) ?></h6>
                                                        <small class="text-muted"><?= htmlspecialchars($report['breed']) ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if (isset($report['status']) && $report['status'] === 'found'): ?>
                                                            <span class="badge bg-success">Found</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">Lost</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?= date('M j, Y, g:i A', strtotime($report['created_at'])) ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <?php if ($report['edited_at']): ?>
                                                            <small class="text-muted">
                                                                <?= date('M j, Y, g:i A', strtotime($report['edited_at'])) ?>
                                                                <span class="badge text-white" style="background-color: #6f42c1;">Edited</span>
                                                            </small>
                                                        <?php else: ?>
                                                            <small class="text-muted">-</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="action" value="view_details">
                                                                <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            </form>
                                                            
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="action" value="edit">
                                                                <input type="hidden" name="report_id" value="<?= (int)$report['id'] ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            </form>
                                                            
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $report['id'] ?>)">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Cat Image" style="max-width: 100%; max-height: 80vh;">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
    function confirmDelete(reportId) {
        if (confirm('Are you sure you want to delete this report?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_report">
                <input type="hidden" name="report_id" value="${reportId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function openImageModal(imageSrc) {
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageSrc;
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }

    document.getElementById('profilePictureInput').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('File size must be less than 5MB');
                this.value = '';
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please upload only JPG, JPEG, or PNG files');
                this.value = '';
                return;
            }
        }
    });
</script>
</body>
</html>