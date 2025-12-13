<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$messageType = '';

// Validate course access before proceeding
if ($courseId > 0) {
    validateCourseAccess($courseId);
}

// Get course data
$courseQuery = "SELECT * FROM Courses WHERE courseId = $courseId";
$courseResult = mysqli_query($conn, $courseQuery);
$course = mysqli_fetch_assoc($courseResult);

if (!$course) {
    header("Location: course-management.php");
    exit;
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $fileId = (int)$_POST['fileId'];
    
    // Get file info
    $fileQuery = "SELECT * FROM CourseFiles WHERE courseMaterialId = $fileId AND courseId = $courseId";
    $fileResult = mysqli_query($conn, $fileQuery);
    $file = mysqli_fetch_assoc($fileResult);
    
    if ($file) {
        // Delete physical file
        if (file_exists('./' . $file['filePath'])) {
            unlink('./' . $file['filePath']);
        }
        
        // Delete from database
        $deleteQuery = "DELETE FROM CourseFiles WHERE courseMaterialId = $fileId";
        if (mysqli_query($conn, $deleteQuery)) {
            $message = 'File deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error deleting file: ' . mysqli_error($conn);
            $messageType = 'error';
        }
    }
}

// Get course files
$filesQuery = "SELECT * FROM CourseFiles WHERE courseId = $courseId ORDER BY createdDate DESC";
$filesResult = mysqli_query($conn, $filesQuery);
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php include("./partials/head.php"); ?>

<style>
    .files-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 2rem 0;
    }

    .file-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .file-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .file-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .file-icon.image { background: #e3f2fd; color: #1976d2; }
    .file-icon.video { background: #f3e5f5; color: #7b1fa2; }
    .file-icon.audio { background: #e8f5e8; color: #388e3c; }
    .file-icon.document { background: #fff3e0; color: #f57c00; }
    .file-icon.file { background: #f5f5f5; color: #616161; }

    .upload-area {
        border: 2px dashed #007bff;
        border-radius: 15px;
        padding: 3rem;
        text-align: center;
        background: #f8f9ff;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 2rem;
    }

    .upload-area:hover {
        background: #e3f2fd;
        border-color: #0056b3;
        transform: scale(1.02);
    }

    .upload-area.dragover {
        background: #e3f2fd;
        border-color: #0056b3;
        transform: scale(1.02);
    }

    .file-actions {
        display: flex;
        gap: 0.5rem;
    }

    .file-type-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .badge-image { background: #e3f2fd; color: #1976d2; }
    .badge-video { background: #f3e5f5; color: #7b1fa2; }
    .badge-audio { background: #e8f5e8; color: #388e3c; }
    .badge-document { background: #fff3e0; color: #f57c00; }
    .badge-file { background: #f5f5f5; color: #616161; }
</style>

<body>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <?php include("./partials/header.php"); ?>
        <?php include("./partials/navbar.php"); ?>
        
        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Course Files - <?php echo htmlspecialchars($course['courseName']); ?></h4>
                        <div class="ms-auto text-end">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="./home">Home</a></li>
                                    <li class="breadcrumb-item"><a href="./course-management">Course Management</a></li>
                                    <li class="breadcrumb-item"><a href="./edit-course.php?id=<?php echo $courseId; ?>">Edit Course</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Course Files</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid files-container">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <!-- Upload Area -->
                        <div class="upload-area" id="uploadArea">
                            <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-3"></i>
                            <h4>Upload Course Files</h4>
                            <p class="text-muted mb-0">Drag and drop files here or click to browse</p>
                            <input type="file" id="fileInput" multiple style="display: none;" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt,.rtf,.ppt,.pptx,.xls,.xlsx">
                        </div>

                        <!-- File Type Selector -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="fileType" id="allFiles" value="" checked>
                                    <label class="btn btn-outline-primary" for="allFiles">All Files</label>
                                    
                                    <input type="radio" class="btn-check" name="fileType" id="imageFiles" value="image">
                                    <label class="btn btn-outline-primary" for="imageFiles">Images</label>
                                    
                                    <input type="radio" class="btn-check" name="fileType" id="videoFiles" value="video">
                                    <label class="btn btn-outline-primary" for="videoFiles">Videos</label>
                                    
                                    <input type="radio" class="btn-check" name="fileType" id="audioFiles" value="audio">
                                    <label class="btn btn-outline-primary" for="audioFiles">Audio</label>
                                    
                                    <input type="radio" class="btn-check" name="fileType" id="documentFiles" value="document">
                                    <label class="btn btn-outline-primary" for="documentFiles">Documents</label>
                                </div>
                            </div>
                        </div>

                        <!-- Files List -->
                        <div id="filesList">
                            <?php if (mysqli_num_rows($filesResult) > 0): ?>
                                <?php while ($file = mysqli_fetch_assoc($filesResult)): ?>
                                    <div class="file-card" data-file-type="<?php echo $file['fileType']; ?>">
                                        <div class="d-flex align-items-center">
                                            <div class="file-icon <?php echo $file['fileType']; ?>">
                                                <?php
                                                switch ($file['fileType']) {
                                                    case 'image': echo '<i class="fas fa-image"></i>'; break;
                                                    case 'video': echo '<i class="fas fa-video"></i>'; break;
                                                    case 'audio': echo '<i class="fas fa-volume-up"></i>'; break;
                                                    case 'document': echo '<i class="fas fa-file-alt"></i>'; break;
                                                    default: echo '<i class="fas fa-file"></i>';
                                                }
                                                ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($file['fileName']); ?></h6>
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="file-type-badge badge-<?php echo $file['fileType']; ?>">
                                                        <?php echo ucfirst($file['fileType']); ?>
                                                    </span>
                                                    <small class="text-muted">
                                                        <?php echo number_format($file['fileSize'] / 1024 / 1024, 2); ?> MB
                                                    </small>
                                                    <small class="text-muted">
                                                        <?php echo date('M d, Y', strtotime($file['createdDate'])); ?>
                                                    </small>
                                                </div>
                                                <?php if ($file['fileDescription']): ?>
                                                    <p class="text-muted mb-0 mt-1"><?php echo htmlspecialchars($file['fileDescription']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="file-actions">
                                                <?php
                                                    $fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
                                                        . "://" . $_SERVER['HTTP_HOST'] . '/' . ltrim($file['filePath'], '/');
                                                ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-copy-url" data-url="<?php echo htmlspecialchars($fullUrl); ?>" title="Copy URL">
                                                    <i class="fas fa-link"></i>
                                                </button>
                                                <a href="../<?php echo $file['filePath']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="../<?php echo $file['filePath']; ?>" download class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                                    <input type="hidden" name="fileId" value="<?php echo $file['courseMaterialId']; ?>">
                                                    <button type="submit" name="delete_file" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-folder-open fa-5x text-muted mb-4"></i>
                                    <h4 class="text-muted">No Files Uploaded</h4>
                                    <p class="text-muted">Upload files to get started!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="./assets/extra-libs/sparkline/sparkline.js"></script>
    <script src="./dist/js/waves.js"></script>
    <script src="./dist/js/sidebarmenu.js"></script>
    <script src="./dist/js/custom.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const filesList = document.getElementById('filesList');
        const MAX_SIZE_BYTES = 50 * 1024 * 1024; // 50MB client-side cap

        // File type filtering
        document.querySelectorAll('input[name="fileType"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const fileType = this.value;
                const fileCards = document.querySelectorAll('.file-card');
                
                fileCards.forEach(card => {
                    if (fileType === '' || card.dataset.fileType === fileType) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Upload functionality
        uploadArea.addEventListener('click', () => fileInput.click());
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                uploadFiles(files);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                uploadFiles(e.target.files);
            }
        });

        function uploadFiles(files) {
            Array.from(files).forEach(file => {
                // Client-side size validation to avoid unnecessary uploads
                if (file.size > MAX_SIZE_BYTES) {
                    const fileCard = document.createElement('div');
                    fileCard.className = 'file-card';
                    fileCard.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="file-icon file">
                                <i class="fas fa-exclamation-circle text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${file.name}</h6>
                                <small class="text-warning">File too large. Maximum allowed size is 50MB.</small>
                            </div>
                        </div>
                    `;
                    filesList.insertBefore(fileCard, filesList.firstChild);
                    return;
                }
                uploadFile(file);
            });
        }

        function uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('courseId', <?php echo $courseId; ?>);
            formData.append('fileType', getFileType(file));

            // Show upload progress
            const fileCard = document.createElement('div');
            fileCard.className = 'file-card';
            fileCard.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="file-icon ${getFileType(file)}">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${file.name}</h6>
                        <small class="text-muted">Uploading...</small>
                    </div>
                </div>
            `;
            filesList.insertBefore(fileCard, filesList.firstChild);

            // Upload file
            fetch('php/upload-course-file.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload to show new file
                } else {
                    fileCard.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="file-icon file">
                                <i class="fas fa-exclamation-circle text-danger"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-danger">${file.name}</h6>
                                <small class="text-danger">Upload failed: ${data.message}</small>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                fileCard.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="file-icon file">
                            <i class="fas fa-exclamation-circle text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-danger">${file.name}</h6>
                            <small class="text-danger">Upload failed</small>
                        </div>
                    </div>
                `;
            });
        }

        function getFileType(file) {
            const type = file.type.split('/')[0];
            const extension = file.name.split('.').pop().toLowerCase();
            
            if (['image'].includes(type) || ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
                return 'image';
            }
            if (['video'].includes(type) || ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'].includes(extension)) {
                return 'video';
            }
            if (['audio'].includes(type) || ['mp3', 'wav', 'ogg', 'm4a', 'aac'].includes(extension)) {
                return 'audio';
            }
            if (['pdf', 'doc', 'docx', 'txt', 'rtf', 'ppt', 'pptx', 'xls', 'xlsx'].includes(extension)) {
                return 'document';
            }
            return 'file';
        }

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Copy file URL handler
        document.addEventListener('DOMContentLoaded', function() {
            const copyButtons = document.querySelectorAll('.btn-copy-url');
            copyButtons.forEach(btn => {
                btn.addEventListener('click', async function() {
                    const url = this.getAttribute('data-url') || '';
                    if (!url) return;
                    const originalHtml = this.innerHTML;
                    try {
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            await navigator.clipboard.writeText(url);
                        } else {
                            const temp = document.createElement('input');
                            temp.value = url;
                            document.body.appendChild(temp);
                            temp.select();
                            document.execCommand('copy');
                            document.body.removeChild(temp);
                        }
                        this.innerHTML = '<i class="fas fa-check"></i>';
                        this.classList.remove('btn-outline-secondary');
                        this.classList.add('btn-success');
                        setTimeout(() => {
                            this.innerHTML = originalHtml;
                            this.classList.remove('btn-success');
                            this.classList.add('btn-outline-secondary');
                        }, 1500);
                    } catch (e) {
                        console.error('Copy failed:', e);
                        this.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                        this.classList.remove('btn-outline-secondary');
                        this.classList.add('btn-warning');
                        setTimeout(() => {
                            this.innerHTML = originalHtml;
                            this.classList.remove('btn-warning');
                            this.classList.add('btn-outline-secondary');
                        }, 1500);
                    }
                });
            });
        });
    </script>
</body>
</html>