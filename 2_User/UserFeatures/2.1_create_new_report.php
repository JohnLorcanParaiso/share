<?php
require_once '../../2_User/UserBackend/userAuth.php';

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

require_once '../../2_User/UserBackend/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    error_log("POST action received: " . $action);
    
    switch ($action) {
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

        case 'search':
            $search_query = isset($_POST['search']) ? $_POST['search'] : '';
            header("Location: search.php?q=" . urlencode($search_query));
            exit();

        case 'profile':
            header("Location: 4.1_my_profile.php");
            exit();

        default:
            error_log("Unhandled action: " . $action);
            header("Location: 1_user_dashboard.php");
            exit();
    }
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Guest User';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../../4_Styles/user_style.css">
</head>
<body>
<div class="side-menu">
        <div class="text-center">
            <img src="../../3_Images/logo.png" class="logo" style="width: 150px; height: 150px; margin: 20px auto; display: block;">
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <form method="POST">
                    <button type="submit" name="action" value="dashboard" class="btn btn-link nav-link text-dark active">
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
                    <button type="submit" name="action" value="myProfile" class="btn btn-link nav-link text-dark">
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
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-4">
                            <div class="d-flex flex-column">
                                <h4 class="card-title mb-2">Create New Report</h4>
                                <p class="text-muted mb-0">Please fill in the details about the missing cat</p>
                            </div>
                        </div>
                        <div class="card-body px-4 py-5">
                            <?php if(isset($_SESSION['report_success'])): ?>
                                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php 
                                        echo $_SESSION['report_success']; 
                                        unset($_SESSION['report_success']);
                                    ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php if(isset($_SESSION['report_error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php 
                                        echo $_SESSION['report_error']; 
                                        unset($_SESSION['report_error']);
                                    ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form action="2.2_process_report.php" method="POST" enctype="multipart/form-data">
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">Cat Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Cat Name</label>
                                            <input type="text" class="form-control" name="cat_name" placeholder="Enter cat's name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Breed</label>
                                            <input type="text" class="form-control" name="breed" placeholder="Enter cat's breed" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">Physical Characteristics</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Gender</label>
                                            <select class="form-select" name="gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="unknown">Unknown</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Age (Years)</label>
                                            <input type="number" class="form-control" name="age" min="0" max="30" step="0.1" placeholder="Enter age (e.g. 1.5)" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Color</label>
                                            <input type="text" class="form-control" name="color" placeholder="Enter color(s)" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">Additional Details</h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="3" 
                                                placeholder="Description about the cat" required></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Seen Date</label>
                                            <input type="date" class="form-control" name="last_seen_date" 
                                                   max="<?= date('Y-m-d') ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Seen Time (Approximate)</label>
                                            <input type="time" class="form-control" name="last_seen_time">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Last Seen Location</label>
                                            <input type="text" class="form-control" name="last_seen_location" 
                                                placeholder="Enter the location where the cat was last seen" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">Owner Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Owner's Name</label>
                                            <input type="text" class="form-control" name="owner_name" 
                                                placeholder="Enter owner's name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" name="phone_number" 
                                                placeholder="Enter phone number" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold mb-3">Upload Images</h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <input type="file" class="form-control" name="cat_images[]" multiple accept="image/*" max="5">                                   
                                            <div class="form-text">Upload an image of your cat (Max: 5)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-custom">Submit Report</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
    <?php if (isset($_SESSION['report_success'])): ?>
        Swal.fire({
            title: 'Success!',
            text: 'Your report has been successfully submitted.',
            icon: 'success',
            confirmButtonColor: '#3085d6'
        }).then((result) => {
            window.location.href = '3.1_view_reports.php';
        });
        <?php unset($_SESSION['report_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['report_error'])): ?>
        Swal.fire({
            title: 'Error!',
            text: '<?php echo $_SESSION['report_error']; ?>',
            icon: 'error',
            confirmButtonColor: '#d33'
        });
        <?php unset($_SESSION['report_error']); ?>
    <?php endif; ?>

    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        let firstInvalidField = null;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
                if (!firstInvalidField) firstInvalidField = field;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        const fileInput = this.querySelector('input[type="file"]');
        if (fileInput && fileInput.files.length === 0) {
            isValid = false;
            fileInput.classList.add('is-invalid');
            if (!firstInvalidField) firstInvalidField = fileInput;
        }
        
        if (!isValid) {
            e.preventDefault(); // Only prevent submission if invalid
            Swal.fire({
                title: 'Error!',
                text: 'Please fill in all required fields and upload at least one image.',
                icon: 'error',
                confirmButtonColor: '#d33'
            }).then(() => {
                if (firstInvalidField) {
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
        // If valid, the form will submit naturally
    });
    </script>
</body>
</html> 