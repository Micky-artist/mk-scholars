<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$messageType = '';

// Get course data with optimized query
$courseQuery = "SELECT courseId, courseName, courseContent, courseDisplayStatus, 
                       courseStartDate, courseEndDate, courseRegEndDate 
                FROM Courses WHERE courseId = $courseId";
$courseResult = mysqli_query($conn, $courseQuery);
$course = mysqli_fetch_assoc($courseResult);

if (!$course) {
    header("Location: course-management.php");
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_course_content'])) {
        $courseContent = $_POST['course_content'];
        
        $updateQuery = "UPDATE Courses SET courseContent = '" . mysqli_real_escape_string($conn, $courseContent) . "' WHERE courseId = $courseId";
        
        if (mysqli_query($conn, $updateQuery)) {
            $message = 'Course content saved successfully!';
            $messageType = 'success';
            
            // Refresh course data from database after successful save
            $courseQuery = "SELECT * FROM Courses WHERE courseId = $courseId";
            $courseResult = mysqli_query($conn, $courseQuery);
            $course = mysqli_fetch_assoc($courseResult);
        } else {
            $message = 'Error saving course content: ' . mysqli_error($conn);
            $messageType = 'error';
        }
    }
}

// Parse course content
$courseData = json_decode($course['courseContent'], true);
if (!$courseData) {
    $courseData = [
        'sections' => [],
        'theme' => [
            'primaryColor' => '#007bff',
            'secondaryColor' => '#6c757d',
            'fontFamily' => 'Arial, sans-serif',
            'headerFontSize' => '2rem',
            'bodyFontSize' => '1rem'
        ],
        'settings' => [
            'allowComments' => true,
            'showProgress' => true,
            'enableDownloads' => true,
            'publishDate' => null,
            'unpublishDate' => null,
            'visibilityMode' => 'immediate' // immediate, scheduled, hidden
        ],
        'links' => []
    ];
}
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php include("./partials/head.php"); ?>

<style>
    :root {
        --bg-primary: #f8f9fa;
        --bg-secondary: #ffffff;
        --text-primary: #1f2937;
        --text-secondary: #4b5563;
        --glass-bg: rgba(255, 255, 255, 0.9);
        --glass-border: rgba(255, 255, 255, 0.3);
    }

    [data-theme="dark"] {
        --bg-primary: #1a1a1a;
        --bg-secondary: #2d2d2d;
        --text-primary: #f9fafb;
        --text-secondary: #9ca3af;
        --glass-bg: rgba(45, 45, 45, 0.9);
        --glass-border: rgba(255, 255, 255, 0.1);
    }

    .editor-container {
        background: var(--bg-primary);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .editor-sidebar {
        background: var(--glass-bg);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .editor-main {
        background: var(--glass-bg);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .section-item {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        cursor: move;
        transition: all 0.3s ease;
        user-select: none;
        backdrop-filter: blur(10px);
    }

    .section-item:hover {
        background: var(--bg-secondary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .section-item.dragging {
        opacity: 0.5;
        transform: rotate(5deg);
    }

    .section-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .section-order-controls {
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-right: 0.75rem;
    }

    .section-order-controls .btn {
        padding: 0.25rem 0.375rem;
        font-size: 0.7rem;
        line-height: 1;
    }

    .section-title {
        font-weight: 600;
        color: #495057;
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.3;
    }

    .section-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .theme-controls {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .color-picker {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 50%;
        cursor: pointer;
    }

    .font-size-control {
        width: 100%;
    }

    .content-editor {
        min-height: 200px;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1rem;
        background: white;
    }

    .toolbar {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px 10px 0 0;
        padding: 0.5rem;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .toolbar button {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        padding: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .toolbar button:hover {
        background: #e9ecef;
    }

    .toolbar button.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .section-preview {
        border: 2px dashed #e9ecef;
        border-radius: 10px;
        padding: 1rem;
        margin: 1rem 0;
        min-height: 100px;
        background: white;
    }

    .file-upload-area {
        border: 2px dashed #007bff;
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        background: #f8f9ff;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        user-select: none;
    }

    .file-upload-area:hover {
        background: #e3f2fd;
        border-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
    }

    .file-upload-area.dragover {
        background: #e3f2fd;
        border-color: #0056b3;
        transform: scale(1.02);
        box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
    }

    .file-upload-area:active {
        transform: scale(0.98);
    }

    .uploaded-files {
        margin-top: 1rem;
    }

    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        margin-bottom: 0.5rem;
    }

    .file-icon {
        width: 24px;
        height: 24px;
        margin-right: 0.5rem;
    }

    .section-type-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .section-type-card {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
        position: relative;
        z-index: 1;
    }

    .section-type-card:hover {
        border-color: #007bff;
        background: #f8f9ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }

    .section-type-card.selected {
        border-color: #007bff;
        background: #e3f2fd;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    .section-type-card:active {
        transform: translateY(0);
    }

    .section-type-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: #007bff;
    }

    /* Modal fixes */
    .modal-backdrop {
        z-index: 1040 !important;
    }

    .modal {
        z-index: 1050 !important;
    }

    .modal.show {
        display: block !important;
    }

    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
        transform: translate(0, -50px);
    }

    .modal.show .modal-dialog {
        transform: none;
    }

    /* Ensure modal is fully removed */
    .modal:not(.show) {
        display: none !important;
    }

    .modal:not(.show) .modal-dialog {
        transform: translate(0, -50px);
    }

    /* Pagination Controls */
    .pagination-controls {
        background: var(--glass-bg, rgba(255, 255, 255, 0.9));
        border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.3));
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        margin-top: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(10px);
    }

    .pagination-controls .pagination-info {
        font-size: 0.9rem;
        color: var(--text-secondary, #6c757d);
        font-weight: 500;
        white-space: nowrap;
    }

    .pagination-controls .pagination-buttons {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .pagination-controls .page-numbers {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
        align-items: center;
        max-width: 100%;
        justify-content: center;
    }

    .pagination-controls .page-numbers .btn {
        min-width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
        padding: 0.375rem 0.75rem;
    }

    .pagination-controls .page-numbers .btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
    }

    .pagination-controls .page-numbers .btn.btn-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.4);
    }

    .pagination-controls .page-numbers .ellipsis {
        padding: 0.375rem 0.5rem;
        color: var(--text-secondary, #6c757d);
        font-weight: 600;
        user-select: none;
        pointer-events: none;
    }

    .pagination-controls .btn-nav {
        min-width: 100px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
        padding: 0.5rem 1rem;
        gap: 0.5rem;
    }

    .pagination-controls .btn-nav:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
    }

    .pagination-controls .btn-nav:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .sections-pagination {
        border-top: 1px solid var(--glass-border, #e9ecef);
        padding-top: 1.25rem;
        margin-top: 1.5rem;
        background: var(--glass-bg, rgba(255, 255, 255, 0.9));
        border-radius: 12px;
        padding: 1rem 1.25rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .sections-pagination .pagination-info {
        font-size: 0.85rem;
        color: var(--text-secondary, #6c757d);
        font-weight: 500;
        white-space: nowrap;
    }

    .sections-pagination .pagination-buttons {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .sections-pagination .page-numbers {
        display: flex;
        gap: 0.3rem;
        flex-wrap: wrap;
        align-items: center;
        max-width: 100%;
        justify-content: center;
    }

    .sections-pagination .page-numbers .btn {
        min-width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.2s ease;
        padding: 0.25rem 0.5rem;
    }

    .sections-pagination .page-numbers .btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
    }

    .sections-pagination .page-numbers .btn.btn-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.4);
    }

    .sections-pagination .page-numbers .ellipsis {
        padding: 0.25rem 0.4rem;
        color: var(--text-secondary, #6c757d);
        font-weight: 600;
        user-select: none;
        pointer-events: none;
        font-size: 0.8rem;
    }

    .sections-pagination .btn-nav {
        min-width: 85px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.2s ease;
        padding: 0.375rem 0.75rem;
        gap: 0.4rem;
    }

    .sections-pagination .btn-nav:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
    }

    .sections-pagination .btn-nav:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Break Section Styling */
    .break-section {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%) !important;
        border: 2px dashed #007bff !important;
        border-radius: 10px !important;
        position: relative;
    }

    .break-section::before {
        content: "PAGE BREAK";
        position: absolute;
        top: -8px;
        left: 50%;
        transform: translateX(-50%);
        background: #007bff;
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: bold;
        z-index: 1;
    }

    .break-section .section-title {
        color: #007bff !important;
        font-weight: bold !important;
    }

    .break-section .section-actions {
        opacity: 0.7;
    }

    /* Responsive pagination */
    @media (max-width: 768px) {
        .pagination-controls {
            padding: 1rem;
        }

        .pagination-controls .d-flex {
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }
        
        .pagination-controls .pagination-info {
            text-align: center;
        }
        
        .pagination-controls .pagination-buttons {
            justify-content: center;
            width: 100%;
        }

        .pagination-controls .page-numbers {
            width: 100%;
            justify-content: center;
        }

        .pagination-controls .btn-nav {
            flex: 1;
            min-width: auto;
        }
        
        .sections-pagination {
            padding: 0.875rem;
        }
        
        .sections-pagination .d-flex {
            flex-direction: column;
            gap: 0.75rem;
            align-items: center;
        }

        .sections-pagination .pagination-info {
            text-align: center;
        }
        
        .sections-pagination .pagination-buttons {
            justify-content: center;
            width: 100%;
        }

        .sections-pagination .page-numbers {
            width: 100%;
            justify-content: center;
        }

        .sections-pagination .btn-nav {
            flex: 1;
            min-width: auto;
        }
    }

    /* Context Menu for Pagination */
    .pagination-context-menu {
        position: absolute;
        z-index: 9999;
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        min-width: 220px;
        display: none;
        overflow: hidden;
    }
    .pagination-context-menu .menu-item {
        padding: 10px 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
        color: #374151;
        transition: background 0.15s ease;
        border-bottom: 1px solid #f3f4f6;
    }
    .pagination-context-menu .menu-item:last-child {
        border-bottom: none;
    }
    .pagination-context-menu .menu-item:hover {
        background: #f3f4f6;
    }
    .pagination-context-menu .menu-item.danger {
        color: #b91c1c;
    }
    .pagination-context-menu .menu-item.disabled {
        color: #9ca3af;
        cursor: not-allowed;
        background: #ffffff !important;
    }
</style>

<body>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <?php include("./partials/header.php"); ?>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <?php include("./partials/navbar.php"); ?>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Course Editor - <?php echo htmlspecialchars($course['courseName']); ?></h4>
                        <div class="ms-auto text-end">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="./home">Home</a></li>
                                    <li class="breadcrumb-item"><a href="./course-management">Course Management</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Course Editor</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid editor-container">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Sidebar -->
                    <div class="col-lg-3">
                        <div class="editor-sidebar">
                            <h5 class="mb-3">Course Sections</h5>
                            <div id="sections-list">
                                <?php foreach ($courseData['sections'] as $index => $section): ?>
                                    <div class="section-item" data-index="<?php echo $index; ?>">
                                        <div class="section-controls">
                                            <h6 class="section-title"><?php echo htmlspecialchars($section['title']); ?></h6>
                                            <div class="section-actions">
                                                <button class="btn btn-sm btn-outline-primary" onclick="editSection(<?php echo $index; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSection(<?php echo $index; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted"><?php echo ucfirst($section['type']); ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <button class="btn btn-primary w-100" onclick="showAddSectionModal()" id="addSectionBtn">
                                <i class="fas fa-plus me-2"></i>Add Section
                            </button>
                        </div>

                        <!-- Visibility Controls -->
                        <div class="editor-sidebar">
                            <h5 class="mb-3">Content Visibility</h5>
                            <div class="theme-controls">
                                <div class="mb-3">
                                    <label class="form-label">Visibility Mode</label>
                                    <select class="form-control" id="visibilityMode">
                                        <option value="immediate" <?php echo $courseData['settings']['visibilityMode'] === 'immediate' ? 'selected' : ''; ?>>Immediate</option>
                                        <option value="scheduled" <?php echo $courseData['settings']['visibilityMode'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                        <option value="hidden" <?php echo $courseData['settings']['visibilityMode'] === 'hidden' ? 'selected' : ''; ?>>Hidden</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="publishDateContainer" style="display: none;">
                                    <label class="form-label">Publish Date</label>
                                    <input type="datetime-local" class="form-control" id="publishDate" 
                                           value="<?php echo $courseData['settings']['publishDate'] ? date('Y-m-d\TH:i', strtotime($courseData['settings']['publishDate'])) : ''; ?>">
                                </div>
                                <div class="mb-3" id="unpublishDateContainer" style="display: none;">
                                    <label class="form-label">Unpublish Date</label>
                                    <input type="datetime-local" class="form-control" id="unpublishDate" 
                                           value="<?php echo $courseData['settings']['unpublishDate'] ? date('Y-m-d\TH:i', strtotime($courseData['settings']['unpublishDate'])) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Links Management -->
                        <div class="editor-sidebar">
                            <h5 class="mb-3">Course Links</h5>
                            <div class="theme-controls">
                                <div class="mb-3">
                                    <button class="btn btn-primary btn-sm w-100" onclick="showAddLinkModal()">
                                        <i class="fas fa-plus me-2"></i>Add Link
                                    </button>
                                </div>
                                <div id="linksList">
                                    <?php foreach ($courseData['links'] as $index => $link): ?>
                                        <div class="link-item mb-2 p-2 border rounded">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($link['title']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($link['url']); ?></small>
                                                </div>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editLink(<?php echo $index; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteLink(<?php echo $index; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Theme Controls -->
                        <div class="editor-sidebar">
                            <h5 class="mb-3">Theme Settings</h5>
                            <div class="theme-controls">
                                <div class="mb-3">
                                    <label class="form-label">Primary Color</label>
                                    <input type="color" class="color-picker" id="primaryColor" value="<?php echo $courseData['theme']['primaryColor']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Secondary Color</label>
                                    <input type="color" class="color-picker" id="secondaryColor" value="<?php echo $courseData['theme']['secondaryColor']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Font Family</label>
                                    <select class="form-control" id="fontFamily">
                                        <option value="Arial, sans-serif" <?php echo $courseData['theme']['fontFamily'] === 'Arial, sans-serif' ? 'selected' : ''; ?>>Arial</option>
                                        <option value="Georgia, serif" <?php echo $courseData['theme']['fontFamily'] === 'Georgia, serif' ? 'selected' : ''; ?>>Georgia</option>
                                        <option value="'Times New Roman', serif" <?php echo $courseData['theme']['fontFamily'] === "'Times New Roman', serif" ? 'selected' : ''; ?>>Times New Roman</option>
                                        <option value="'Courier New', monospace" <?php echo $courseData['theme']['fontFamily'] === "'Courier New', monospace" ? 'selected' : ''; ?>>Courier New</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Header Font Size</label>
                                    <input type="range" class="form-range font-size-control" id="headerFontSize" min="1" max="4" step="0.1" value="<?php echo str_replace('rem', '', $courseData['theme']['headerFontSize']); ?>">
                                    <small class="text-muted"><?php echo $courseData['theme']['headerFontSize']; ?></small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Body Font Size</label>
                                    <input type="range" class="form-range font-size-control" id="bodyFontSize" min="0.8" max="1.5" step="0.1" value="<?php echo str_replace('rem', '', $courseData['theme']['bodyFontSize']); ?>">
                                    <small class="text-muted"><?php echo $courseData['theme']['bodyFontSize']; ?></small>
                                </div>
                            </div>
                            <!-- Pagination Settings removed -->
                        </div>
                    </div>

                    <!-- Main Editor -->
                    <div class="col-lg-9">
                        <div class="editor-main">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4>Course Content Editor</h4>
                                <div>
                                    <div class="btn-group me-2" role="group">
                                        <button class="btn btn-outline-primary" onclick="previewCourse('student')">
                                            <i class="fas fa-user me-2"></i>Student View
                                    </button>
                                        <button class="btn btn-outline-info" onclick="previewCourse('admin')">
                                            <i class="fas fa-cog me-2"></i>Admin View
                                        </button>
                                    </div>
                                    <button class="btn btn-primary" onclick="saveCourse()">
                                        <i class="fas fa-save me-2"></i>Save Course
                                    </button>
                                </div>
                            </div>

                            <div id="course-preview">
                                <!-- Course content will be rendered here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>

    <!-- Add Link Modal -->
    <div class="modal fade" id="addLinkModal" tabindex="-1" aria-labelledby="addLinkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLinkModalLabel">Add Course Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="linkTitle" class="form-label">Link Title *</label>
                        <input type="text" class="form-control" id="linkTitle" placeholder="Enter link title">
                    </div>
                    <div class="mb-3">
                        <label for="linkUrl" class="form-label">URL *</label>
                        <input type="url" class="form-control" id="linkUrl" placeholder="https://example.com">
                    </div>
                    <div class="mb-3">
                        <label for="linkDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="linkDescription" rows="3" placeholder="Brief description of the link"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="linkIcon" class="form-label">Icon Class (Optional)</label>
                        <input type="text" class="form-control" id="linkIcon" placeholder="fas fa-external-link-alt">
                        <small class="text-muted">FontAwesome icon class (e.g., fas fa-external-link-alt)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addLink()">Add Link</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Section Modal -->
    <div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSectionModalLabel">Add New Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="section-type-selector">
                        <div class="section-type-card" data-type="text">
                            <div class="section-type-icon"><i class="fas fa-paragraph"></i></div>
                            <h6>Text Section</h6>
                            <small>Rich text content</small>
                        </div>
                        <div class="section-type-card" data-type="video">
                            <div class="section-type-icon"><i class="fas fa-video"></i></div>
                            <h6>Video Section</h6>
                            <small>Video content</small>
                        </div>
                        <div class="section-type-card" data-type="audio">
                            <div class="section-type-icon"><i class="fas fa-volume-up"></i></div>
                            <h6>Audio Section</h6>
                            <small>Audio content</small>
                        </div>
                        <div class="section-type-card" data-type="image">
                            <div class="section-type-icon"><i class="fas fa-image"></i></div>
                            <h6>Image Section</h6>
                            <small>Image gallery</small>
                        </div>
                        <div class="section-type-card" data-type="quiz">
                            <div class="section-type-icon"><i class="fas fa-question-circle"></i></div>
                            <h6>Quiz Section</h6>
                            <small>Interactive quiz</small>
                        </div>
                        <div class="section-type-card" data-type="file">
                            <div class="section-type-icon"><i class="fas fa-file"></i></div>
                            <h6>File Section</h6>
                            <small>Downloadable files</small>
                        </div>
                        <div class="section-type-card" data-type="break">
                            <div class="section-type-icon"><i class="fas fa-page-break"></i></div>
                            <h6>New Page</h6>
                            <small>Page break for pagination</small>
                        </div>
                    </div>

                    <div id="section-form" style="display: none;">
                        <div class="mb-3">
                            <label for="sectionTitle" class="form-label">Section Title</label>
                            <input type="text" class="form-control" id="sectionTitle" placeholder="Enter section title">
                        </div>
                        <div class="mb-3">
                            <label for="sectionContent" class="form-label">Section Content</label>
                            <div class="toolbar" id="textToolbar" style="display: none;">
                                <button type="button" onclick="formatText('bold')" title="Bold"><i class="fas fa-bold"></i></button>
                                <button type="button" onclick="formatText('italic')" title="Italic"><i class="fas fa-italic"></i></button>
                                <button type="button" onclick="formatText('underline')" title="Underline"><i class="fas fa-underline"></i></button>
                                <button type="button" onclick="formatText('strikeThrough')" title="Strikethrough"><i class="fas fa-strikethrough"></i></button>
                                <button type="button" onclick="formatText('justifyLeft')" title="Align Left"><i class="fas fa-align-left"></i></button>
                                <button type="button" onclick="formatText('justifyCenter')" title="Align Center"><i class="fas fa-align-center"></i></button>
                                <button type="button" onclick="formatText('justifyRight')" title="Align Right"><i class="fas fa-align-right"></i></button>
                                <button type="button" onclick="formatText('insertUnorderedList')" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                                <button type="button" onclick="formatText('insertOrderedList')" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                                <button type="button" onclick="promptAndInsertLink()" title="Insert Link"><i class="fas fa-link"></i></button>
                            </div>
                            <div class="content-editor" id="sectionContent" contenteditable="true" placeholder="Enter section content..."></div>
                        </div>
                        
                        <!-- Link input for text sections -->
                        <div class="mb-3" id="linkInputContainer" style="display: none;">
                            <label for="sectionLink" class="form-label">Add Link (Optional)</label>
                            <div class="input-group">
                                <input type="url" class="form-control" id="sectionLink" placeholder="https://example.com">
                                <button class="btn btn-outline-secondary" type="button" onclick="addLinkToContent()">
                                    <i class="fas fa-link me-1"></i>Add Link
                                </button>
                            </div>
                            <small class="text-muted">Enter a URL and click "Add Link" to insert it into the content</small>
                            
                            <!-- Add Media by URL -->
                            <div class="mt-3">
                                <label class="form-label">Add Media by URL</label>
                                <div class="input-group mb-2">
                                    <input type="url" class="form-control" id="imageUrlInput" placeholder="Image URL (https://...)">
                                    <button class="btn btn-outline-primary" type="button" onclick="insertImageUrl()">
                                        <i class="fas fa-image me-1"></i>Insert Image
                                    </button>
                                </div>
                                <div class="input-group mb-2">
                                    <input type="url" class="form-control" id="videoUrlInput" placeholder="Video URL (direct .mp4, .webm)">
                                    <button class="btn btn-outline-primary" type="button" onclick="insertVideoUrl()">
                                        <i class="fas fa-video me-1"></i>Insert Video
                                    </button>
                                </div>
                                <div class="input-group mb-2">
                                    <input type="url" class="form-control" id="audioUrlInput" placeholder="Audio URL (direct .mp3, .wav, .ogg)">
                                    <button class="btn btn-outline-primary" type="button" onclick="insertAudioUrl()">
                                        <i class="fas fa-headphones me-1"></i>Insert Audio
                                    </button>
                                </div>
                                <div class="input-group mb-2">
                                    <input type="url" class="form-control" id="fileUrlInput" placeholder="File URL (PDF, DOC, etc.)">
                                    <button class="btn btn-outline-primary" type="button" onclick="insertFileUrl()">
                                        <i class="fas fa-file me-1"></i>Insert File Link
                                    </button>
                                </div>
                                <small class="text-muted">Direct file URLs are required for preview (e.g., .mp4, .webm, images). Links are inserted into the editor preview.</small>
                            </div>
                        </div>
                        <div class="file-upload-area" id="fileUploadArea" style="display: none;">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                            <h5>Drop files here or click to upload</h5>
                            <p class="text-muted">Supports videos, audio, images, and documents</p>
                            <input type="file" id="fileInput" multiple style="display: none;" accept="">
                        </div>
                        
                        <!-- Section Scheduling Controls -->
                        <div class="mb-3">
                            <label class="form-label">Section Visibility</label>
                            <select class="form-control" id="sectionVisibilityMode">
                                <option value="immediate">Immediate</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="hidden">Hidden</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="sectionPublishDateContainer" style="display: none;">
                            <label class="form-label">Publish Date</label>
                            <input type="datetime-local" class="form-control" id="sectionPublishDate">
                        </div>
                        
                        <div class="mb-3" id="sectionUnpublishDateContainer" style="display: none;">
                            <label class="form-label">Unpublish Date (Optional)</label>
                            <input type="datetime-local" class="form-control" id="sectionUnpublishDate">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addSection()">Add Section</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for saving -->
    <form id="saveForm" method="POST" style="display: none;">
        <input type="hidden" name="save_course_content" value="1">
        <textarea name="course_content" id="courseContentInput"></textarea>
    </form>

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
        let courseData = <?php echo json_encode($courseData); ?>;
        let selectedSectionType = '';
        let editingSectionIndex = -1;
        let editingLinkIndex = -1;
        
        // Pagination variables
        let currentPage = 1;
        let sectionsPerPage = 3; // Number of sections per page
        let currentSectionsPage = 1;
        let sectionsPerPageList = 5; // Number of sections per page in the list

        // Smart pagination function - generates page numbers with ellipsis
        function generatePaginationNumbers(currentPage, totalPages, maxVisible = 7) {
            const pages = [];
            
            if (totalPages <= maxVisible) {
                // Show all pages if total is less than max visible
                for (let i = 1; i <= totalPages; i++) {
                    pages.push(i);
                }
            } else {
                // Always show first page
                pages.push(1);
                
                let startPage = Math.max(2, currentPage - 1);
                let endPage = Math.min(totalPages - 1, currentPage + 1);
                
                // Adjust if we're near the beginning
                if (currentPage <= 3) {
                    endPage = Math.min(4, totalPages - 1);
                }
                
                // Adjust if we're near the end
                if (currentPage >= totalPages - 2) {
                    startPage = Math.max(2, totalPages - 3);
                }
                
                // Add ellipsis after first page if needed
                if (startPage > 2) {
                    pages.push('ellipsis-start');
                }
                
                // Add middle pages
                for (let i = startPage; i <= endPage; i++) {
                    if (i !== 1 && i !== totalPages) {
                        pages.push(i);
                    }
                }
                
                // Add ellipsis before last page if needed
                if (endPage < totalPages - 1) {
                    pages.push('ellipsis-end');
                }
                
                // Always show last page
                if (totalPages > 1) {
                    pages.push(totalPages);
                }
            }
            
            return pages;
        }

        // Change page function
        function changePage(page) {
            if (page < 1) return;
            
            // Get current view mode from the active tab
            const activeTab = document.querySelector('.nav-link.active');
            const viewMode = activeTab ? activeTab.getAttribute('data-view-mode') : 'admin';
            
            renderCoursePreview(viewMode, page);
        }

        // Change sections list page function
        function changeSectionsPage(page) {
            if (page < 1) return;
            currentSectionsPage = page;
            renderSectionsList();
        }

        // Initialize course preview
        function renderCoursePreview(viewMode = 'admin', page = 1) {
            const preview = document.getElementById('course-preview');
            preview.innerHTML = '';
            
            // Add modern styling to preview container
            preview.style.cssText = `
                background: var(--bg-primary, #f8f9fa);
                border-radius: 15px;
                padding: 2rem;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.3));
                min-height: 400px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            `;
            
            if (courseData.sections.length === 0) {
                preview.innerHTML = `
                    <div class="text-center py-5" style="color: var(--text-secondary, #6c757d);">
                        <i class="fas fa-file-alt fa-5x mb-4" style="opacity: 0.5;"></i>
                        <h4 style="color: var(--text-secondary, #6c757d);">No Sections Added Yet</h4>
                        <p style="color: var(--text-secondary, #6c757d);">Click "Add Section" to start building your course content.</p>
                    </div>
                `;
                return;
            }
            
            // Filter sections based on visibility for student view
            let visibleSections = courseData.sections;
            if (viewMode === 'student') {
                const now = new Date();
                visibleSections = courseData.sections.filter(section => {
                    const sectionPublishDate = section.publishDate ? new Date(section.publishDate) : null;
                    const sectionUnpublishDate = section.unpublishDate ? new Date(section.unpublishDate) : null;
                    
                    if (section.visibilityMode === 'hidden') {
                        return false;
                    }
                    
                    if (section.visibilityMode === 'scheduled') {
                        if (sectionPublishDate && now < sectionPublishDate) {
                            return false;
                        }
                        if (sectionUnpublishDate && now > sectionUnpublishDate) {
                            return false;
                        }
                    }
                    return true;
                });
            }

            // Calculate pagination based on break sections
            const breakSections = visibleSections.filter(section => section.type === 'break');
            const totalPages = breakSections.length + 1; // +1 for content before first break
            
            let sectionsToShow = [];
            if (page === 1) {
                // First page: show sections from start until first break
                const firstBreakIndex = visibleSections.findIndex(section => section.type === 'break');
                sectionsToShow = firstBreakIndex === -1 ? visibleSections : visibleSections.slice(0, firstBreakIndex);
            } else {
                // Subsequent pages: show sections between breaks
                const breakIndices = visibleSections.map((section, index) => section.type === 'break' ? index : -1).filter(index => index !== -1);
                const startBreakIndex = breakIndices[page - 2]; // Previous break
                const endBreakIndex = breakIndices[page - 1]; // Current break
                
                if (startBreakIndex !== undefined && endBreakIndex !== undefined) {
                    sectionsToShow = visibleSections.slice(startBreakIndex + 1, endBreakIndex);
                } else if (startBreakIndex !== undefined) {
                    // Last page: from last break to end
                    sectionsToShow = visibleSections.slice(startBreakIndex + 1);
                }
            }

            // Update current page
            currentPage = page;

            // Check visibility for student view
            if (viewMode === 'student') {
                const now = new Date();
                const publishDate = courseData.settings.publishDate ? new Date(courseData.settings.publishDate) : null;
                const unpublishDate = courseData.settings.unpublishDate ? new Date(courseData.settings.unpublishDate) : null;
                
                if (courseData.settings.visibilityMode === 'hidden') {
                    preview.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-eye-slash fa-5x text-muted mb-4"></i>
                            <h4 class="text-muted">Content Hidden</h4>
                            <p class="text-muted">This content is currently hidden from students.</p>
                        </div>
                    `;
                    return;
                }
                
                if (courseData.settings.visibilityMode === 'scheduled') {
                    if (publishDate && now < publishDate) {
                        preview.innerHTML = `
                            <div class="text-center py-5">
                                <i class="fas fa-clock fa-5x text-muted mb-4"></i>
                                <h4 class="text-muted">Content Not Yet Available</h4>
                                <p class="text-muted">This content will be available on ${publishDate.toLocaleDateString()}.</p>
                            </div>
                        `;
                        return;
                    }
                    
                    if (unpublishDate && now > unpublishDate) {
                        preview.innerHTML = `
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-5x text-muted mb-4"></i>
                                <h4 class="text-muted">Content No Longer Available</h4>
                                <p class="text-muted">This content is no longer available.</p>
                            </div>
                        `;
                        return;
                    }
                }
            }
            
            // Render only the sections for the current page
            sectionsToShow.forEach((section, pageIndex) => {
                // Find the original index in the full sections array
                const originalIndex = courseData.sections.findIndex(s => s === section);
                
                const sectionDiv = document.createElement('div');
                sectionDiv.className = 'section-preview mb-4';
                sectionDiv.style.cssText = `
                    margin-bottom: 2rem;
                    padding: 1.5rem;
                    border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.3));
                    border-radius: 12px;
                    background: var(--glass-bg, rgba(255, 255, 255, 0.9));
                    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                    backdrop-filter: blur(10px);
                `;
                
                // Add hover effect
                sectionDiv.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 16px rgba(0,0,0,0.1)';
                });
                
                sectionDiv.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.05)';
                });
                
                // Get appropriate icon for section type
                let sectionIcon = '';
                switch(section.type) {
                    case 'text': sectionIcon = '<i class="fas fa-paragraph"></i>'; break;
                    case 'video': sectionIcon = '<i class="fas fa-video"></i>'; break;
                    case 'audio': sectionIcon = '<i class="fas fa-volume-up"></i>'; break;
                    case 'image': sectionIcon = '<i class="fas fa-image"></i>'; break;
                    case 'quiz': sectionIcon = '<i class="fas fa-question-circle"></i>'; break;
                    case 'file': sectionIcon = '<i class="fas fa-file"></i>'; break;
                    case 'break': sectionIcon = '<i class="fas fa-page-break"></i>'; break;
                    default: sectionIcon = '<i class="fas fa-file-alt"></i>';
                }
                
                // Handle different section types
                let contentHtml = '';
                if (section.type === 'image' && section.files && section.files.length > 0) {
                    // Display uploaded images
                    contentHtml = '<div class="image-gallery">';
                    section.files.forEach(file => {
                        const imageUrl = `<?php echo getImageUrl(''); ?>${file.filePath}`;
                        contentHtml += `
                            <div class="image-item mb-3" style="text-align: center;">
                                <img src="${imageUrl}" alt="${file.fileName}" class="img-fluid rounded" style="max-width: 250px; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <div class="small mt-1" style="color: var(--text-secondary, #4b5563);">${file.fileName}</div>
                            </div>
                        `;
                    });
                    contentHtml += '</div>';
                } else if (section.type === 'video' && section.files && section.files.length > 0) {
                    // Display uploaded videos
                    contentHtml = '<div class="video-gallery">';
                    section.files.forEach(file => {
                        const videoUrl = `<?php echo getImageUrl(''); ?>${file.filePath}`;
                        contentHtml += `
                            <div class="video-item mb-3" style="text-align: center;">
                                <video controls class="img-fluid rounded" style="max-width: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <source src="${videoUrl}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <div class="small mt-1" style="color: var(--text-secondary, #4b5563);">${file.fileName}</div>
                            </div>
                        `;
                    });
                    contentHtml += '</div>';
                } else if (section.type === 'audio' && section.files && section.files.length > 0) {
                    // Display uploaded audio
                    contentHtml = '<div class="audio-gallery">';
                    section.files.forEach(file => {
                        const audioUrl = `<?php echo getImageUrl(''); ?>${file.filePath}`;
                        contentHtml += `
                            <div class="audio-item mb-3">
                                <audio controls class="w-100">
                                    <source src="${audioUrl}" type="audio/mpeg">
                                    Your browser does not support the audio tag.
                                </audio>
                                <div class="text-muted small mt-1">${file.fileName}</div>
                            </div>
                        `;
                    });
                    contentHtml += '</div>';
                } else if (section.type === 'file' && section.files && section.files.length > 0) {
                    // Display uploaded files
                    contentHtml = '<div class="file-gallery">';
                    section.files.forEach(file => {
                        const fileUrl = `<?php echo getImageUrl(''); ?>${file.filePath}`;
                        contentHtml += `
                            <div class="file-item mb-2" style="display: inline-block; margin-right: 1rem; margin-bottom: 0.75rem;">
                                <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm" style="font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 6px; transition: all 0.2s ease;">
                                    <i class="fas fa-download me-2"></i>${file.fileName}
                                </a>
                                <span class="small ms-2" style="color: var(--text-secondary, #4b5563);">(${(file.fileSize / 1024 / 1024).toFixed(2)} MB)</span>
                            </div>
                        `;
                    });
                    contentHtml += '</div>';
                } else if (section.type === 'break') {
                    // Display break section with special styling
                    contentHtml = `
                        <div class="page-break-display" style="
                            text-align: center; 
                            padding: 2rem; 
                            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                            border: 2px dashed #007bff;
                            border-radius: 10px;
                            margin: 1rem 0;
                            font-family: ${courseData.theme.fontFamily};
                            font-size: ${courseData.theme.bodyFontSize};
                        ">
                            <i class="fas fa-page-break" style="font-size: 2rem; color: #007bff; margin-bottom: 0.5rem;"></i>
                            <div style="font-weight: bold; color: #007bff; margin-bottom: 0.5rem;">${section.title}</div>
                            <div style="color: #6c757d; font-size: 0.9rem;">Content after this break will appear on the next page</div>
                        </div>
                    `;
                } else {
                    // Display text content with modern styling
                    contentHtml = `
                        <div class="text-content" style="
                            line-height: 1.6;
                            color: var(--text-primary, #1f2937);
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        ">
                            <div style="color: var(--text-secondary, #4b5563);">${section.content}</div>
                        </div>
                    `;
                }

                sectionDiv.innerHTML = `
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3" style="color: var(--text-primary, #1f2937); font-size: 1.5rem;">
                            ${sectionIcon}
                        </div>
                        <h3 style="color: var(--text-primary, #1f2937); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 1.3rem; margin: 0; font-weight: 600;">
                            ${section.title}
                        </h3>
                        <span class="badge ms-auto" style="background: var(--text-primary, #1f2937); color: var(--bg-primary, #f8f9fa); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem;">
                            ${section.type.charAt(0).toUpperCase() + section.type.slice(1)}
                        </span>
                    </div>
                    ${contentHtml}
                `;
                
                preview.appendChild(sectionDiv);
            });

            // Add pagination controls if there are multiple pages
            if (totalPages > 1) {
                const paginationDiv = document.createElement('div');
                paginationDiv.className = 'pagination-controls mt-4';
                paginationDiv.style.cssText = `
                    background: var(--glass-bg, rgba(255, 255, 255, 0.9));
                    border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.3));
                    border-radius: 12px;
                    padding: 1rem;
                    backdrop-filter: blur(10px);
                    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                `;
                const pageNumbers = generatePaginationNumbers(page, totalPages, 7);
                const pageNumbersHtml = pageNumbers.map(pageNum => {
                    if (pageNum === 'ellipsis-start' || pageNum === 'ellipsis-end') {
                        return `<span class="ellipsis">...</span>`;
                    }
                    const isActive = pageNum === page;
                    return `<button class="btn btn-sm ${isActive ? 'btn-primary' : 'btn-outline-primary'}" 
                                   data-page-number="${pageNum}"
                                   onclick="changePage(${pageNum})">${pageNum}</button>`;
                }).join('');

                paginationDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="pagination-info">
                            <span style="color: var(--text-secondary, #4b5563);">
                                Page ${page} of ${totalPages} (${sectionsToShow.length} sections on this page)
                            </span>
                        </div>
                        <div class="pagination-buttons">
                            <button class="btn btn-outline-primary btn-nav" 
                                    onclick="changePage(${page - 1})" 
                                    ${page === 1 ? 'disabled' : ''}>
                                <i class="fas fa-chevron-left"></i>
                                <span>Previous</span>
                            </button>
                            <span class="page-numbers">
                                ${pageNumbersHtml}
                            </span>
                            <button class="btn btn-outline-primary btn-nav" 
                                    onclick="changePage(${page + 1})" 
                                    ${page === totalPages ? 'disabled' : ''}>
                                <span>Next</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                `;
                preview.appendChild(paginationDiv);

                // Setup right-click context menu for pagination page numbers
                setupPaginationContextMenu(preview);
            }
        }

        // Helper: get indices of break sections in full courseData.sections
        function getBreakIndices() {
            const breaks = [];
            courseData.sections.forEach((section, index) => {
                if (section.type === 'break') breaks.push(index);
            });
            return breaks;
        }

        // Helper: for a given page (>1), return the break index in courseData.sections
        function getBreakIndexForPage(pageNumber) {
            if (pageNumber <= 1) return -1;
            const breaks = getBreakIndices();
            // Page 2 is after first break → index 0, page 3 → index 1, etc.
            const breakIdxInList = pageNumber - 2;
            return breaks[breakIdxInList] !== undefined ? breaks[breakIdxInList] : -1;
        }

        // Helper: count sections on a given page (non-break) based on current courseData.sections
        function countSectionsOnPage(pageNumber) {
            if (pageNumber < 1) return 0;
            const sections = courseData.sections;
            const breakIndices = sections.map((s, i) => s.type === 'break' ? i : -1).filter(i => i !== -1);
            let startIdx = 0;
            let endIdx = sections.length;
            if (pageNumber === 1) {
                endIdx = breakIndices.length > 0 ? breakIndices[0] : sections.length;
            } else {
                const prevBreak = breakIndices[pageNumber - 2];
                const nextBreak = breakIndices[pageNumber - 1];
                startIdx = prevBreak + 1;
                endIdx = nextBreak !== undefined ? nextBreak : sections.length;
            }
            let count = 0;
            for (let i = startIdx; i < endIdx; i++) {
                if (sections[i].type !== 'break') count++;
            }
            return count;
        }

        // Delete the page break that starts the given page (page > 1)
        function deletePageBreakForPage(pageNumber) {
            if (pageNumber <= 1) return;
            const breakIndex = getBreakIndexForPage(pageNumber);
            if (breakIndex < 0) return;

            const sectionsToMove = countSectionsOnPage(pageNumber);
            const message = sectionsToMove > 0
                ? `Delete page ${pageNumber} break? ${sectionsToMove} section(s) on that page will be merged into the previous page.`
                : `Delete page ${pageNumber} break? This page is empty.`;
            if (!confirm(message)) return;

            // Remove the break section; content naturally merges into previous page
            courseData.sections.splice(breakIndex, 1);
            renderCoursePreview();
            renderSectionsList();
            showNotification(`Page ${pageNumber} break deleted successfully.`, 'success');
        }

        // Create and wire a context menu for pagination page numbers
        function setupPaginationContextMenu(previewRoot) {
            let menu = document.getElementById('paginationContextMenu');
            if (!menu) {
                menu = document.createElement('div');
                menu.id = 'paginationContextMenu';
                menu.className = 'pagination-context-menu';
                menu.innerHTML = `
                    <div class="menu-item danger" id="menuDeletePageBreak">
                        <i class="fas fa-trash-alt"></i>
                        <span>Delete this page break</span>
                    </div>
                `;
                document.body.appendChild(menu);
            }

            // Hide menu helper
            const hideMenu = () => { menu.style.display = 'none'; };

            // Track current target page
            let targetPageNumber = null;

            // Right-click on a page number
            previewRoot.addEventListener('contextmenu', function(e) {
                const btn = e.target.closest('[data-page-number]');
                if (!btn) {
                    hideMenu();
                    return; // Not on page number button
                }
                e.preventDefault();
                const pageNumber = parseInt(btn.getAttribute('data-page-number'), 10) || 0;
                targetPageNumber = pageNumber;

                // Disable delete if page 1 (no break to delete)
                const deleteItem = document.getElementById('menuDeletePageBreak');
                if (pageNumber <= 1 || getBreakIndexForPage(pageNumber) < 0) {
                    deleteItem.classList.add('disabled');
                } else {
                    deleteItem.classList.remove('disabled');
                }

                // Position and show menu
                const x = e.pageX;
                const y = e.pageY;
                menu.style.left = x + 'px';
                menu.style.top = y + 'px';
                menu.style.display = 'block';
            });

            // Click action for delete
            menu.addEventListener('click', function(e) {
                const item = e.target.closest('#menuDeletePageBreak');
                if (!item) return;
                if (item.classList.contains('disabled')) return;
                if (typeof targetPageNumber === 'number' && targetPageNumber > 1) {
                    deletePageBreakForPage(targetPageNumber);
                }
                hideMenu();
            });

            // Hide on outside click or ESC
            document.addEventListener('click', function(e) {
                if (!menu.contains(e.target)) hideMenu();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') hideMenu();
            });
            window.addEventListener('scroll', hideMenu, { passive: true });
            window.addEventListener('resize', hideMenu);
        }

        // Show add section modal
        function showAddSectionModal() {
            console.log('showAddSectionModal called');
            editingSectionIndex = -1;
            const modalElement = document.getElementById('addSectionModal');
            console.log('Modal element:', modalElement);
            
            if (modalElement) {
                // Remove any existing modal instances
                const existingModal = bootstrap.Modal.getInstance(modalElement);
                if (existingModal) {
                    existingModal.dispose();
                }
                
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
                
                // Add event listeners for proper cleanup
                modalElement.addEventListener('hidden.bs.modal', function() {
                    console.log('Modal hidden event triggered');
                    // Reset form
                    document.getElementById('sectionTitle').value = '';
                    document.getElementById('sectionContent').innerHTML = '';
                    document.getElementById('section-form').style.display = 'none';
                    document.querySelectorAll('.section-type-card').forEach(card => card.classList.remove('selected'));
                    selectedSectionType = '';
                    
                    // Additional cleanup
                    cleanupModal();
                });
                
                modal.show();
                document.getElementById('section-form').style.display = 'none';
                document.querySelectorAll('.section-type-card').forEach(card => card.classList.remove('selected'));
                
                // Setup event listeners after modal is shown
                setTimeout(() => {
                    setupSectionTypeListeners();
                    setupFileUpload();
                }, 100);
                
                console.log('Modal should be showing now');
            } else {
                console.error('Modal element not found');
            }
        }

        // Handle section type selection
        function setupSectionTypeListeners() {
            console.log('Setting up section type listeners');
            const cards = document.querySelectorAll('.section-type-card');
            console.log('Found section type cards:', cards.length);
            
            cards.forEach((card, index) => {
                console.log(`Card ${index}:`, card, 'Type:', card.dataset.type);
                // Remove existing listeners first
                const oldHandler = card._sectionTypeHandler;
                if (oldHandler) {
                    card.removeEventListener('click', oldHandler);
                }
                // Create new handler that passes event
                const newHandler = function(e) {
                    handleSectionTypeClick.call(this, e);
                };
                card._sectionTypeHandler = newHandler;
                // Add new listener
                card.addEventListener('click', newHandler);
            });
        }

        function handleSectionTypeClick(e) {
            // Prevent duplicate execution from event delegation
            if (e && e.stopPropagation) {
                e.stopPropagation();
            }
            
            console.log('Section type clicked:', this.dataset.type);
            document.querySelectorAll('.section-type-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            selectedSectionType = this.dataset.type;
            
            console.log('Selected section type:', selectedSectionType);
            
            // For break sections, immediately add without showing form
            if (selectedSectionType === 'break') {
                const section = {
                    type: 'break',
                    title: 'New Page',
                    content: '<div class="page-break-section"><i class="fas fa-page-break"></i> Page Break</div>',
                    order: courseData.sections.length,
                    files: [],
                    publishDate: null,
                    unpublishDate: null,
                    visibilityMode: 'immediate'
                };
                
                courseData.sections.push(section);
                renderCoursePreview();
                renderSectionsList();
                
                // Close modal
                const modalElement = document.getElementById('addSectionModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
                
                // Show success message
                showNotification('New page section added successfully!', 'success');
                return;
            }
            
            document.getElementById('section-form').style.display = 'block';
            document.getElementById('textToolbar').style.display = selectedSectionType === 'text' ? 'block' : 'none';
            document.getElementById('linkInputContainer').style.display = selectedSectionType === 'text' ? 'block' : 'none';
            document.getElementById('fileUploadArea').style.display = ['video', 'audio', 'image', 'file'].includes(selectedSectionType) ? 'block' : 'none';
            
            // Set file input accept attribute based on section type
            const fileInput = document.getElementById('fileInput');
            switch(selectedSectionType) {
                case 'image':
                    fileInput.accept = 'image/*';
                    break;
                case 'video':
                    fileInput.accept = 'video/*';
                    break;
                case 'audio':
                    fileInput.accept = 'audio/*';
                    break;
                case 'file':
                    fileInput.accept = '.pdf,.doc,.docx,.txt,.rtf,.ppt,.pptx,.xls,.xlsx';
                    break;
                default:
                    fileInput.accept = '';
            }
            
            // Setup section visibility controls
            setupSectionVisibilityControls();
        }
        
        function setupSectionVisibilityControls() {
            const visibilityMode = document.getElementById('sectionVisibilityMode');
            const publishContainer = document.getElementById('sectionPublishDateContainer');
            const unpublishContainer = document.getElementById('sectionUnpublishDateContainer');
            
            if (visibilityMode) {
                visibilityMode.addEventListener('change', function() {
                    if (this.value === 'scheduled') {
                        publishContainer.style.display = 'block';
                        unpublishContainer.style.display = 'block';
                    } else {
                        publishContainer.style.display = 'none';
                        unpublishContainer.style.display = 'none';
                    }
                });
            }
        }

        // Add section
        function addSection() {
            console.log('addSection called');
            console.log('Selected section type:', selectedSectionType);
            
            const title = document.getElementById('sectionTitle').value;
            const content = document.getElementById('sectionContent').innerHTML;
            
            console.log('Title:', title);
            console.log('Content:', content);
            
            if (!selectedSectionType) {
                alert('Please select a section type');
                return;
            }
            
            // For break sections, only title is required
            if (selectedSectionType === 'break') {
                if (!title) {
                    alert('Please enter a title for the break section');
                return;
                }
            } else {
                if (!title || !content) {
                    alert('Please fill in all required fields');
                    return;
                }
            }
            
            // Section visibility must always be immediate
            const visibilityMode = 'immediate';
            const publishDate = null;
            const unpublishDate = null;
            
            const section = {
                type: selectedSectionType,
                title: title,
                content: selectedSectionType === 'break' ? '<div class="page-break-section"><i class="fas fa-page-break"></i> Page Break</div>' : content,
                order: courseData.sections.length,
                files: selectedSectionType === 'break' ? [] : (window.uploadedFiles || []),
                publishDate: publishDate,
                unpublishDate: unpublishDate,
                visibilityMode: visibilityMode
            };
            
            console.log('Creating section:', section);
            
            if (editingSectionIndex >= 0) {
                courseData.sections[editingSectionIndex] = section;
            } else {
                courseData.sections.push(section);
            }
            
            // Clear uploaded files after adding section
            window.uploadedFiles = [];
            
            // Clear file upload area
            const filesContainer = document.getElementById('uploadedFilesContainer');
            if (filesContainer) {
                filesContainer.innerHTML = '';
            }
            
            renderCoursePreview();
            renderSectionsList();
            
            // Close modal
            const modalElement = document.getElementById('addSectionModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            
            // Show success message
            showNotification('Section added successfully!', 'success');
        }

        // Render sections list
        function renderSectionsList() {
            const list = document.getElementById('sections-list');
            list.innerHTML = '';
            
            // Calculate pagination for sections list based on break sections
            const breakSections = courseData.sections.filter(section => section.type === 'break');
            const totalPages = breakSections.length + 1; // +1 for content before first break
            
            let sectionsToShow = [];
            if (currentSectionsPage === 1) {
                // First page: show sections from start until first break
                const firstBreakIndex = courseData.sections.findIndex(section => section.type === 'break');
                sectionsToShow = firstBreakIndex === -1 ? courseData.sections : courseData.sections.slice(0, firstBreakIndex);
            } else {
                // Subsequent pages: show sections between breaks
                const breakIndices = courseData.sections.map((section, index) => section.type === 'break' ? index : -1).filter(index => index !== -1);
                const startBreakIndex = breakIndices[currentSectionsPage - 2]; // Previous break
                const endBreakIndex = breakIndices[currentSectionsPage - 1]; // Current break
                
                if (startBreakIndex !== undefined && endBreakIndex !== undefined) {
                    sectionsToShow = courseData.sections.slice(startBreakIndex + 1, endBreakIndex);
                } else if (startBreakIndex !== undefined) {
                    // Last page: from last break to end
                    sectionsToShow = courseData.sections.slice(startBreakIndex + 1);
                }
            }
            
            // If this page has 0 sections, show a delete page button
            if (sectionsToShow.filter(s => s.type !== 'break').length === 0 && currentSectionsPage > 1) {
                const emptyAlert = document.createElement('div');
                emptyAlert.className = 'alert alert-warning d-flex justify-content-between align-items-center';
                emptyAlert.innerHTML = `
                    <div>
                        <i class="fas fa-info-circle me-2"></i>
                        This page has 0 sections.
                    </div>
                    <button class="btn btn-sm btn-danger" id="deleteEmptyPageBtn">
                        <i class="fas fa-trash me-1"></i>Delete This Page
                    </button>
                `;
                list.appendChild(emptyAlert);
                // Wire delete button
                setTimeout(() => {
                    const btn = document.getElementById('deleteEmptyPageBtn');
                    if (btn) {
                        btn.addEventListener('click', function() {
                            deleteSectionsPage(currentSectionsPage);
                        });
                    }
                }, 0);
            }
            
            sectionsToShow.forEach((section, pageIndex) => {
                const originalIndex = courseData.sections.findIndex(s => s === section);
                const sectionDiv = document.createElement('div');
                sectionDiv.className = section.type === 'break' ? 'section-item break-section' : 'section-item';
                sectionDiv.dataset.index = originalIndex;
                
                // Get scheduling status
                let schedulingStatus = '';
                if (section.visibilityMode === 'hidden') {
                    schedulingStatus = '<span class="badge bg-secondary">Hidden</span>';
                } else if (section.visibilityMode === 'scheduled') {
                    const now = new Date();
                    const publishDate = section.publishDate ? new Date(section.publishDate) : null;
                    const unpublishDate = section.unpublishDate ? new Date(section.unpublishDate) : null;
                    
                    if (publishDate && now < publishDate) {
                        schedulingStatus = `<span class="badge bg-warning">Scheduled (${publishDate.toLocaleDateString()})</span>`;
                    } else if (unpublishDate && now > unpublishDate) {
                        schedulingStatus = '<span class="badge bg-danger">Expired</span>';
                    } else {
                        schedulingStatus = '<span class="badge bg-success">Published</span>';
                    }
                } else {
                    schedulingStatus = '<span class="badge bg-primary">Immediate</span>';
                }
                
                sectionDiv.innerHTML = `
                    <div class="section-controls">
                        <div class="section-order-controls">
                            <button class="btn btn-sm btn-outline-secondary" onclick="moveSection(${originalIndex}, 'up')" ${originalIndex === 0 ? 'disabled' : ''} title="Move Up">
                                <i class="fas fa-chevron-up"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="moveSection(${originalIndex}, 'down')" ${originalIndex === courseData.sections.length - 1 ? 'disabled' : ''} title="Move Down">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                        <h6 class="section-title">${section.title}</h6>
                        <div class="section-actions">
                            <button class="btn btn-sm btn-outline-primary" onclick="editSection(${originalIndex})" title="Edit Section">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteSection(${originalIndex})" title="Delete Section">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">${section.type.charAt(0).toUpperCase() + section.type.slice(1)}</small>
                        ${schedulingStatus}
                    </div>
                `;
                
                // Add drag and drop functionality
                sectionDiv.draggable = true;
                sectionDiv.addEventListener('dragstart', handleDragStart);
                sectionDiv.addEventListener('dragover', handleDragOver);
                sectionDiv.addEventListener('drop', handleDrop);
                sectionDiv.addEventListener('dragend', handleDragEnd);
                
                list.appendChild(sectionDiv);
            });

            // Add pagination controls for sections list if there are multiple pages
            if (totalPages > 1) {
                const paginationDiv = document.createElement('div');
                paginationDiv.className = 'sections-pagination mt-3';
                const pageNumbers = generatePaginationNumbers(currentSectionsPage, totalPages, 7);
                const pageNumbersHtml = pageNumbers.map(pageNum => {
                    if (pageNum === 'ellipsis-start' || pageNum === 'ellipsis-end') {
                        return `<span class="ellipsis">...</span>`;
                    }
                    const isActive = pageNum === currentSectionsPage;
                    return `<button class="btn btn-sm ${isActive ? 'btn-primary' : 'btn-outline-primary'}" 
                                   onclick="changeSectionsPage(${pageNum})">${pageNum}</button>`;
                }).join('');

                paginationDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="pagination-info">
                            <span class="text-muted small">
                                Page ${currentSectionsPage} of ${totalPages} (${sectionsToShow.length} sections on this page)
                            </span>
                        </div>
                        <div class="pagination-buttons">
                            <button class="btn btn-outline-primary btn-nav" 
                                    onclick="changeSectionsPage(${currentSectionsPage - 1})" 
                                    ${currentSectionsPage === 1 ? 'disabled' : ''}>
                                <i class="fas fa-chevron-left"></i>
                                <span>Previous</span>
                            </button>
                            <span class="page-numbers">
                                ${pageNumbersHtml}
                            </span>
                            <button class="btn btn-outline-primary btn-nav" 
                                    onclick="changeSectionsPage(${currentSectionsPage + 1})" 
                                    ${currentSectionsPage === totalPages ? 'disabled' : ''}>
                                <span>Next</span>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                `;
                list.appendChild(paginationDiv);
            }
        }
        
        // Helpers to delete a sections page by removing its break
        function getBreakIndicesForSections() {
            const indices = [];
            courseData.sections.forEach((s, i) => { if (s.type === 'break') indices.push(i); });
            return indices;
        }
        function getBreakIndexForSectionsPage(pageNumber) {
            if (pageNumber <= 1) return -1;
            const breaks = getBreakIndicesForSections();
            const idx = pageNumber - 2;
            return breaks[idx] !== undefined ? breaks[idx] : -1;
        }
        function deleteSectionsPage(pageNumber) {
            if (pageNumber <= 1) return;
            const breakIndex = getBreakIndexForSectionsPage(pageNumber);
            if (breakIndex < 0) {
                showNotification('No page break found for this page.', 'warning');
                return;
            }
            const confirmMsg = 'Delete this empty page? Content will be merged into the previous page if any.';
            if (!confirm(confirmMsg)) return;
            courseData.sections.splice(breakIndex, 1);
            currentSectionsPage = Math.max(1, pageNumber - 1);
            renderCoursePreview();
            renderSectionsList();
            showNotification('Page deleted successfully.', 'success');
        }

        // Edit section
        function editSection(index) {
            editingSectionIndex = index;
            const section = courseData.sections[index];
            
            document.getElementById('sectionTitle').value = section.title;
            document.getElementById('sectionContent').innerHTML = section.content;
            
            // Populate scheduling fields
            document.getElementById('sectionVisibilityMode').value = section.visibilityMode || 'immediate';
            document.getElementById('sectionPublishDate').value = section.publishDate || '';
            document.getElementById('sectionUnpublishDate').value = section.unpublishDate || '';
            
            // Show/hide scheduling containers based on visibility mode
            const publishContainer = document.getElementById('sectionPublishDateContainer');
            const unpublishContainer = document.getElementById('sectionUnpublishDateContainer');
            if (section.visibilityMode === 'scheduled') {
                publishContainer.style.display = 'block';
                unpublishContainer.style.display = 'block';
            } else {
                publishContainer.style.display = 'none';
                unpublishContainer.style.display = 'none';
            }
            
            // Show existing files for image/video/audio/file sections
            if (section.files && section.files.length > 0) {
                const filesContainer = document.getElementById('uploadedFilesContainer');
                if (filesContainer) {
                    filesContainer.innerHTML = '';
                    section.files.forEach((file, index) => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'file-item';
                        const fileUrl = `<?php echo getImageUrl(''); ?>${file.filePath}`;
                        const isImage = (file.fileType === 'image') || (/\.(jpe?g|png|gif|webp)$/i).test(file.fileName || '');
                        const previewHtml = isImage 
                            ? `<img src="${fileUrl}" alt="${file.fileName}" style="max-width: 120px; max-height: 90px; margin-right: 10px; border-radius: 6px;">`
                            : `<i class="fas fa-file text-primary me-2"></i>`;
                        fileItem.innerHTML = `
                            <div class="d-flex align-items-center">
                                ${previewHtml}
                                <div>
                                    <div class="fw-bold">${file.fileName}</div>
                                    <small class="text-muted">${(file.fileSize / 1024 / 1024).toFixed(2)} MB - Existing</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 6px;">
                                <a class="btn btn-sm btn-outline-primary" href="${fileUrl}" target="_blank" title="View File">
                                    <i class="fas fa-eye"></i>
                                </a>
                                ${file.fileId ? `
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteUploadedFile(${file.fileId}, '${file.fileName}')" title="Delete File">
                                    <i class="fas fa-trash"></i>
                                </button>` : `
                                <button class="btn btn-sm btn-outline-danger" onclick="removeExistingFile(${index})" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>`}
                            </div>
                        `;
                        filesContainer.appendChild(fileItem);
                    });
                    
                    // Store existing files for editing
                    window.uploadedFiles = section.files;
                }
            }
            
            // Show modal and select the section type
            const modalElement = document.getElementById('addSectionModal');
            
            // Remove any existing modal instances
            const existingModal = bootstrap.Modal.getInstance(modalElement);
            if (existingModal) {
                existingModal.dispose();
            }
            
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
            
            modal.show();
            
            // Setup event listeners after modal is shown
            setTimeout(() => {
                setupSectionTypeListeners();
                setupFileUpload();
                
                // Select the section type card
                document.querySelectorAll('.section-type-card').forEach(c => c.classList.remove('selected'));
                const typeCard = document.querySelector(`[data-type="${section.type}"]`);
                if (typeCard) {
                    typeCard.classList.add('selected');
                    selectedSectionType = section.type;
                    document.getElementById('section-form').style.display = 'block';
                    document.getElementById('textToolbar').style.display = selectedSectionType === 'text' ? 'block' : 'none';
                    document.getElementById('linkInputContainer').style.display = selectedSectionType === 'text' ? 'block' : 'none';
                    document.getElementById('fileUploadArea').style.display = ['video', 'audio', 'image', 'file'].includes(selectedSectionType) ? 'block' : 'none';
                    
                    // Set file input accept attribute based on section type
                    const fileInput = document.getElementById('fileInput');
                    switch(selectedSectionType) {
                        case 'image':
                            fileInput.accept = 'image/*';
                            break;
                        case 'video':
                            fileInput.accept = 'video/*';
                            break;
                        case 'audio':
                            fileInput.accept = 'audio/*';
                            break;
                        case 'file':
                            fileInput.accept = '.pdf,.doc,.docx,.txt,.rtf,.ppt,.pptx,.xls,.xlsx';
                            break;
                        default:
                            fileInput.accept = '';
                    }
                }
            }, 100);
        }

        // Delete section
        function deleteSection(index) {
            if (confirm('Are you sure you want to delete this section? This will also delete all associated files.')) {
                const section = courseData.sections[index];
                
                // Delete associated files if any
                if (section.files && section.files.length > 0) {
                    section.files.forEach(file => {
                        if (file.fileId) {
                            // Delete file from server and database
                            fetch('php/delete-course-file.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    fileId: file.fileId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (!data.success) {
                                    console.error('Error deleting file:', data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error deleting file:', error);
                            });
                        }
                    });
                }
                
                // Remove section from course data
                courseData.sections.splice(index, 1);
                renderCoursePreview();
                renderSectionsList();
                showNotification('Section and associated files deleted successfully!', 'success');
            }
        }

        // Move section up or down
        function moveSection(index, direction) {
            if (direction === 'up' && index > 0) {
                // Move up
                const temp = courseData.sections[index];
                courseData.sections[index] = courseData.sections[index - 1];
                courseData.sections[index - 1] = temp;
            } else if (direction === 'down' && index < courseData.sections.length - 1) {
                // Move down
                const temp = courseData.sections[index];
                courseData.sections[index] = courseData.sections[index + 1];
                courseData.sections[index + 1] = temp;
            }
            
            renderCoursePreview();
            renderSectionsList();
            showNotification('Section order updated!', 'success');
        }

        // Drag and drop functionality
        let draggedElement = null;

        function handleDragStart(e) {
            draggedElement = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            return false;
        }

        function handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }

            if (draggedElement !== this) {
                const draggedIndex = parseInt(draggedElement.dataset.index);
                const targetIndex = parseInt(this.dataset.index);
                
                // Move the section in the array
                const draggedSection = courseData.sections[draggedIndex];
                courseData.sections.splice(draggedIndex, 1);
                courseData.sections.splice(targetIndex, 0, draggedSection);
                
                // Re-render the sections
                renderCoursePreview();
                renderSectionsList();
                showNotification('Section order updated!', 'success');
            }

            return false;
        }

        function handleDragEnd(e) {
            this.classList.remove('dragging');
            draggedElement = null;
        }

        // Format text
        function formatText(command) {
            document.execCommand(command, false, null);
        }

        // Add link to content
        function addLinkToContent() {
            const linkUrl = document.getElementById('sectionLink').value.trim();
            if (!linkUrl) {
                alert('Please enter a valid URL');
                return;
            }
            
            // Validate URL
            try {
                new URL(linkUrl);
            } catch (e) {
                alert('Please enter a valid URL (e.g., https://example.com)');
                return;
            }
            
            const contentEditor = document.getElementById('sectionContent');
            const linkText = prompt('Enter link text (optional):', linkUrl);
            
            if (linkText !== null) {
                const linkHtml = `<a href="${linkUrl}" target="_blank" rel="noopener noreferrer">${linkText || linkUrl}</a>`;
                
                // Focus on the content editor first
                contentEditor.focus();
                
                // Insert at cursor position or append
                const selection = window.getSelection();
                if (selection.rangeCount > 0) {
                    // Check if cursor is inside the content editor
                    const range = selection.getRangeAt(0);
                    if (contentEditor.contains(range.commonAncestorContainer)) {
                        // Insert at cursor position
                        range.deleteContents();
                        const linkNode = document.createElement('div');
                        linkNode.innerHTML = linkHtml;
                        range.insertNode(linkNode.firstChild);
                        range.collapse(false);
                        selection.removeAllRanges();
                        selection.addRange(range);
                    } else {
                        // Cursor not in editor, append to end
                        const separator = contentEditor.innerHTML ? '<br><br>' : '';
                        contentEditor.innerHTML += separator + linkHtml;
                    }
                } else {
                    // No selection, append to end
                    const separator = contentEditor.innerHTML ? '<br><br>' : '';
                    contentEditor.innerHTML += separator + linkHtml;
                }
                
                // Clear the link input
                document.getElementById('sectionLink').value = '';
                
                // Show success message
                showNotification('Link added successfully!', 'success');
            }
        }

        // Prompt for a link and insert at cursor (toolbar button)
        function promptAndInsertLink() {
            const url = prompt('Enter the URL (e.g., https://example.com):', 'https://');
            if (!url) return;
            try {
                new URL(url);
            } catch (e) {
                alert('Please enter a valid URL (e.g., https://example.com)');
                return;
            }
            const contentEditor = document.getElementById('sectionContent');
            const linkText = prompt('Enter link text (optional):', url);
            const linkHtml = `<a href="${url}" target="_blank" rel="noopener noreferrer">${linkText || url}</a>`;
            contentEditor.focus();
            const selection = window.getSelection();
            if (selection && selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                if (contentEditor.contains(range.commonAncestorContainer)) {
                    range.deleteContents();
                    const linkNode = document.createElement('div');
                    linkNode.innerHTML = linkHtml;
                    range.insertNode(linkNode.firstChild);
                    range.collapse(false);
                    selection.removeAllRanges();
                    selection.addRange(range);
                } else {
                    const separator = contentEditor.innerHTML ? '<br><br>' : '';
                    contentEditor.innerHTML += separator + linkHtml;
                }
            } else {
                const separator = contentEditor.innerHTML ? '<br><br>' : '';
                contentEditor.innerHTML += separator + linkHtml;
            }
            showNotification('Link inserted.', 'success');
        }

        // Insert media by URL helpers
        function insertImageUrl() {
            const input = document.getElementById('imageUrlInput');
            const url = (input && input.value.trim()) || '';
            if (!url) { alert('Enter an image URL'); return; }
            try { new URL(url); } catch(e) { alert('Enter a valid URL'); return; }
            const html = `<div style="margin: 6px 0;"><img src="${url}" alt="" style="width: 100%; max-width: 480px; height: auto; border-radius: 8px;"></div>`;
            insertHtmlIntoEditor(html);
            input.value = '';
        }
        function insertVideoUrl() {
            const input = document.getElementById('videoUrlInput');
            const url = (input && input.value.trim()) || '';
            if (!url) { alert('Enter a direct video URL (.mp4, .webm)'); return; }
            try { new URL(url); } catch(e) { alert('Enter a valid URL'); return; }
            const type = url.endsWith('.webm') ? 'video/webm' : 'video/mp4';
            const html = `<div style="margin: 6px 0;"><video controls style="width: 100%; max-width: 480px; border-radius: 8px;"><source src="${url}" type="${type}">Your browser does not support the video tag.</video></div>`;
            insertHtmlIntoEditor(html);
            input.value = '';
        }
        function insertAudioUrl() {
            const input = document.getElementById('audioUrlInput');
            const url = (input && input.value.trim()) || '';
            if (!url) { alert('Enter a direct audio URL (.mp3, .wav, .ogg)'); return; }
            try { new URL(url); } catch(e) { alert('Enter a valid URL'); return; }
            let audioType = 'audio/mpeg';
            if (url.endsWith('.wav')) audioType = 'audio/wav';
            else if (url.endsWith('.ogg')) audioType = 'audio/ogg';
            const html = `<div style="margin: 6px 0; max-width: 480px;"><audio controls style="width: 100%;"><source src="${url}" type="${audioType}">Your browser does not support the audio element.</audio></div>`;
            insertHtmlIntoEditor(html);
            input.value = '';
        }
        function insertFileUrl() {
            const input = document.getElementById('fileUrlInput');
            const url = (input && input.value.trim()) || '';
            if (!url) { alert('Enter a file URL'); return; }
            try { new URL(url); } catch(e) { alert('Enter a valid URL'); return; }
            const html = `<div style="margin: 6px 0;"><a href="${url}" target="_blank" rel="noopener noreferrer"><i class="fas fa-file me-1"></i>Open file</a></div>`;
            insertHtmlIntoEditor(html);
            input.value = '';
        }
        function insertHtmlIntoEditor(html) {
            const contentEditor = document.getElementById('sectionContent');
            if (!contentEditor) return;
            contentEditor.focus();
            const selection = window.getSelection();
            if (selection && selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                if (contentEditor.contains(range.commonAncestorContainer)) {
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = html;
                    const node = wrapper.firstChild;
                    range.insertNode(node);
                    range.setStartAfter(node);
                    range.collapse(true);
                    selection.removeAllRanges();
                    selection.addRange(range);
                    return;
                }
            }
            contentEditor.innerHTML += html;
        }

        // Remove existing file
        function removeExistingFile(index) {
            if (window.uploadedFiles && window.uploadedFiles.length > index) {
                window.uploadedFiles.splice(index, 1);
                
                // Update the display
                const filesContainer = document.getElementById('uploadedFilesContainer');
                if (filesContainer) {
                    filesContainer.innerHTML = '';
                    if (window.uploadedFiles.length > 0) {
                        window.uploadedFiles.forEach((file, idx) => {
                            const fileItem = document.createElement('div');
                            fileItem.className = 'file-item';
                            const fileUrl = `<?php echo getImageUrl(''); ?>${file.filePath}`;
                            const isImage = (file.fileType === 'image') || (/\.(jpe?g|png|gif|webp)$/i).test(file.fileName || '');
                            const previewHtml = isImage 
                                ? `<img src="${fileUrl}" alt="${file.fileName}" style="max-width: 120px; max-height: 90px; margin-right: 10px; border-radius: 6px;">`
                                : `<i class="fas fa-file text-primary me-2"></i>`;
                            const descVal = file.description || '';
                            fileItem.innerHTML = `
                                <div class="d-flex align-items-center">
                                    ${previewHtml}
                                    <div>
                                        <div class="fw-bold">${file.fileName}</div>
                                        <small class="text-muted">${(file.fileSize / 1024 / 1024).toFixed(2)} MB - Existing</small>
                                        <div class="mt-1">
                                            <input type="text" class="form-control form-control-sm" placeholder="File description (optional)" value="${descVal.replace(/"/g,'&quot;')}" oninput="updateUploadedFileDescription(${idx}, this.value)">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center" style="gap: 6px;">
                                    <a class="btn btn-sm btn-outline-primary" href="${fileUrl}" target="_blank" title="View File">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" onclick="removeExistingFile(${idx})" title="Remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `;
                            filesContainer.appendChild(fileItem);
                        });
                    }
                }
            }
        }

        // Save course
        function saveCourse() {
            // Show saving indicator
            const saveButton = document.querySelector('button[onclick="saveCourse()"]');
            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            saveButton.disabled = true;
            
            // Update settings with current form values
            courseData.settings.visibilityMode = document.getElementById('visibilityMode').value;
            courseData.settings.publishDate = document.getElementById('publishDate').value || null;
            courseData.settings.unpublishDate = document.getElementById('unpublishDate').value || null;
            
            // Update the hidden input with current course data
            document.getElementById('courseContentInput').value = JSON.stringify(courseData);
            
            // Submit the form
            document.getElementById('saveForm').submit();
            
            // Show success notification
            showNotification('Course saved successfully!', 'success');
            
            // Reset button after a short delay (in case of page reload)
            setTimeout(() => {
                saveButton.innerHTML = originalText;
                saveButton.disabled = false;
            }, 2000);
        }

        // Link management functions
        function showAddLinkModal() {
            console.log('showAddLinkModal called');
            editingLinkIndex = -1;
            document.getElementById('linkTitle').value = '';
            document.getElementById('linkUrl').value = '';
            document.getElementById('linkDescription').value = '';
            document.getElementById('linkIcon').value = '';
            
            const modalElement = document.getElementById('addLinkModal');
            if (modalElement) {
                // Remove any existing modal instances
                const existingModal = bootstrap.Modal.getInstance(modalElement);
                if (existingModal) {
                    existingModal.dispose();
                }
                
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
                modal.show();
                console.log('Link modal should be showing now');
            } else {
                console.error('Link modal element not found');
            }
        }

        function addLink() {
            const title = document.getElementById('linkTitle').value.trim();
            const url = document.getElementById('linkUrl').value.trim();
            const description = document.getElementById('linkDescription').value.trim();
            const icon = document.getElementById('linkIcon').value.trim();

            if (!title || !url) {
                alert('Please fill in title and URL');
                return;
            }

            const link = {
                title: title,
                url: url,
                description: description,
                icon: icon || 'fas fa-external-link-alt'
            };

            if (editingLinkIndex >= 0) {
                courseData.links[editingLinkIndex] = link;
            } else {
                courseData.links.push(link);
            }

            renderLinksList();
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('addLinkModal'));
            modal.hide();
            
            showNotification('Link added successfully!', 'success');
        }

        function editLink(index) {
            editingLinkIndex = index;
            const link = courseData.links[index];
            
            document.getElementById('linkTitle').value = link.title;
            document.getElementById('linkUrl').value = link.url;
            document.getElementById('linkDescription').value = link.description || '';
            document.getElementById('linkIcon').value = link.icon || '';
            
            const modal = new bootstrap.Modal(document.getElementById('addLinkModal'));
            modal.show();
        }

        function deleteLink(index) {
            if (confirm('Are you sure you want to delete this link?')) {
                courseData.links.splice(index, 1);
                renderLinksList();
                showNotification('Link deleted successfully!', 'success');
            }
        }

        function renderLinksList() {
            const container = document.getElementById('linksList');
            container.innerHTML = '';
            
            courseData.links.forEach((link, index) => {
                const linkDiv = document.createElement('div');
                linkDiv.className = 'link-item mb-2 p-2 border rounded';
                linkDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${link.title}</strong>
                            <br><small class="text-muted">${link.url}</small>
                            ${link.description ? `<br><small class="text-muted">${link.description}</small>` : ''}
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" onclick="editLink(${index})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteLink(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(linkDiv);
            });
        }

        // Visibility controls
        document.getElementById('visibilityMode').addEventListener('change', function() {
            const publishContainer = document.getElementById('publishDateContainer');
            const unpublishContainer = document.getElementById('unpublishDateContainer');
            
            if (this.value === 'scheduled') {
                publishContainer.style.display = 'block';
                unpublishContainer.style.display = 'block';
            } else {
                publishContainer.style.display = 'none';
                unpublishContainer.style.display = 'none';
            }
        });

        // Initialize visibility controls
        document.addEventListener('DOMContentLoaded', function() {
            const visibilityMode = document.getElementById('visibilityMode');
            if (visibilityMode.value === 'scheduled') {
                document.getElementById('publishDateContainer').style.display = 'block';
                document.getElementById('unpublishDateContainer').style.display = 'block';
            }
        });

        // Preview course
        function previewCourse(viewMode = 'admin') {
            // Open preview in new window
            const previewWindow = window.open('', '_blank');
            
            // Build filtered sections (keep breaks as page boundaries)
            const filteredSectionsForPreview = courseData.sections.filter(section => {
                if (viewMode !== 'student') return true;
                const now = new Date();
                const sectionPublishDate = section.publishDate ? new Date(section.publishDate) : null;
                const sectionUnpublishDate = section.unpublishDate ? new Date(section.unpublishDate) : null;
                if (section.visibilityMode === 'hidden') return false;
                if (section.visibilityMode === 'scheduled') {
                    if (sectionPublishDate && now < sectionPublishDate) return false;
                    if (sectionUnpublishDate && now > sectionUnpublishDate) return false;
                }
                return true;
            });
            const totalPages = filteredSectionsForPreview.filter(s => s.type === 'break').length + 1;
            
            // Helper to render a specific page based on break sections
            function renderPreviewPage(pageNumber) {
                // Determine slice by break boundaries
                const original = filteredSectionsForPreview;
                const breakIndices = original
                    .map((s, i) => (s.type === 'break' ? i : -1))
                    .filter(i => i !== -1);

                let startIdx = 0;
                let endIdx = original.length;
                if (pageNumber === 1) {
                    endIdx = breakIndices.length > 0 ? breakIndices[0] : original.length;
                } else {
                    const prevBreak = breakIndices[pageNumber - 2];
                    const nextBreak = breakIndices[pageNumber - 1];
                    startIdx = prevBreak + 1;
                    endIdx = nextBreak !== undefined ? nextBreak : original.length;
                }

                const pageSections = original.slice(startIdx, endIdx).filter(s => s.type !== 'break');

                // Build sections HTML for this page
                const sectionsHtml = pageSections.map(section => {
                    let contentHtml = '';
                    if (section.type === 'image' && section.files && section.files.length > 0) {
                        contentHtml = '<div class="image-gallery">';
                        section.files.forEach(file => {
                            const imageUrl = `<?php echo getImageUrl(''); ?>${file.filePath}`;
                            contentHtml += `
                                <div class="image-item mb-3">
                                    <img src="${imageUrl}" alt="${file.fileName}" class="img-fluid rounded" style="max-width: 300px; height: auto;">
                                    <div class="text-muted small mt-1">${file.fileName}</div>
                                </div>
                            `;
                        });
                        contentHtml += '</div>';
                    } else if (section.type === 'video' && section.files && section.files.length > 0) {
                        contentHtml = '<div class="video-gallery">';
                        section.files.forEach(file => {
                            const videoUrl = `<?php echo getImageUrl(''); ?>${file.filePath}`;
                            contentHtml += `
                                <div class="video-item mb-3" style="text-align: center;">
                                    <video controls class="img-fluid rounded" style="max-width: 300px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                        <source src="${videoUrl}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="small mt-1" style="color: var(--text-secondary, #4b5563);">${file.fileName}</div>
                                </div>
                            `;
                        });
                        contentHtml += '</div>';
                    } else if (section.type === 'audio' && section.files && section.files.length > 0) {
                        contentHtml = '<div class="audio-gallery">';
                        section.files.forEach(file => {
                            const audioUrl = `<?php echo getImageUrl(''); ?>${file.filePath}`;
                            contentHtml += `
                                <div class="audio-item mb-3">
                                    <audio controls class="w-100">
                                        <source src="${audioUrl}" type="audio/mpeg">
                                        Your browser does not support the audio tag.
                                    </audio>
                                    <div class="text-muted small mt-1">${file.fileName}</div>
                                </div>
                            `;
                        });
                        contentHtml += '</div>';
                    } else if (section.type === 'file' && section.files && section.files.length > 0) {
                        contentHtml = '<div class="file-gallery">';
                        section.files.forEach(file => {
                            const fileUrl = `<?php echo getImageUrl(''); ?>${file.filePath}`;
                            contentHtml += `
                                <div class="file-item mb-2" style="display: inline-block; margin-right: 1rem; margin-bottom: 0.75rem;">
                                    <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm" style="font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 6px; transition: all 0.2s ease;">
                                        <i class="fas fa-download me-2"></i>${file.fileName}
                                    </a>
                                    <span class="small ms-2" style="color: var(--text-secondary, #4b5563);">(${(file.fileSize / 1024 / 1024).toFixed(2)} MB)</span>
                                </div>
                            `;
                        });
                        contentHtml += '</div>';
                    } else {
                        contentHtml = `<div style="font-family: ${courseData.theme.fontFamily}; font-size: ${courseData.theme.bodyFontSize}; line-height: 1.6;">${section.content}</div>`;
                    }

                    return `
                        <div class="section">
                            <h2 style="color: ${courseData.theme.primaryColor}; font-family: ${courseData.theme.fontFamily}; font-size: ${courseData.theme.headerFontSize};">${section.title}</h2>
                            ${contentHtml}
                        </div>
                    `;
                }).join('');

                const contentRoot = previewWindow.document.getElementById('courseContent');
                if (contentRoot) contentRoot.innerHTML = sectionsHtml;

                // Update pagination UI
                const currEl = previewWindow.document.getElementById('currentPage');
                const totalEl = previewWindow.document.getElementById('totalPages');
                const prevBtn = previewWindow.document.getElementById('prevBtn');
                const nextBtn = previewWindow.document.getElementById('nextBtn');
                if (currEl) currEl.textContent = String(pageNumber);
                if (totalEl) totalEl.textContent = String(totalPages);
                if (prevBtn) prevBtn.disabled = pageNumber <= 1;
                if (nextBtn) nextBtn.disabled = pageNumber >= totalPages;
                // Update page buttons active state
                const pageBtns = previewWindow.document.querySelectorAll('[data-page-btn]');
                pageBtns.forEach(btn => {
                    const p = parseInt(btn.getAttribute('data-page-btn'));
                    btn.className = `btn btn-sm me-1 ${p === pageNumber ? 'btn-primary' : 'btn-outline-primary'}`;
                });
            }

            previewWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Course Preview</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
                    <style>
                        body { 
                            font-family: ${courseData.theme.fontFamily}; 
                            background-color: #f8f9fa;
                            padding: 1rem;
                            font-size: 0.9rem;
                        }
                        h1, h2, h3 { color: ${courseData.theme.primaryColor}; }
                        h1 { font-size: 1.5rem; margin-bottom: 1rem; }
                        h2 { font-size: 1.2rem; margin-bottom: 0.75rem; }
                        .section { 
                            margin-bottom: 1rem; 
                            padding: 1rem; 
                            border: 1px solid #e9ecef; 
                            border-radius: 8px; 
                            background-color: white;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        }
                        .image-gallery .image-item {
                            text-align: center;
                            margin-bottom: 0.75rem;
                        }
                        .image-gallery .image-item img {
                            max-width: 250px !important;
                        }
                        .video-gallery .video-item {
                            text-align: center;
                            margin-bottom: 0.75rem;
                        }
                        .video-gallery .video-item video {
                            max-width: 300px;
                        }
                        .audio-gallery .audio-item {
                            margin-bottom: 0.75rem;
                        }
                        .file-gallery .file-item {
                            display: inline-block;
                            margin-right: 0.75rem;
                            margin-bottom: 0.5rem;
                        }
                        .file-gallery .btn {
                            font-size: 0.8rem;
                            padding: 0.25rem 0.5rem;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1 class="text-center mb-4">${courseData.sections.length > 0 ? courseData.sections[0].title : 'Course Preview'}</h1>
                        <div id="courseContent"></div>

                        ${totalPages > 1 ? `
                        <div class="pagination-controls mt-4" style="
                            background: rgba(255,255,255,0.9);
                            border: 1px solid rgba(255,255,255,0.3);
                            border-radius: 12px;
                            padding: 1rem;
                            backdrop-filter: blur(10px);
                            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                        ">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="pagination-info">
                                    <span style="color: #4b5563;">
                                        Page <span id="currentPage">1</span> of <span id="totalPages">${totalPages}</span>
                                    </span>
                                </div>
                                <div class="pagination-buttons">
                                    <button id="prevBtn" class="btn btn-outline-primary btn-sm me-2">\n                                        <i class="fas fa-chevron-left me-1"></i>Previous\n                                    </button>
                                    <span class="page-numbers">
                                        ${Array.from({length: totalPages}, (_, i) => {
                                            const pageNum = i + 1;
                                            return `<button class="btn btn-sm me-1 ${pageNum === 1 ? 'btn-primary' : 'btn-outline-primary'}" data-page-btn="${pageNum}">${pageNum}</button>`;
                                        }).join('')}
                                    </span>
                                    <button id="nextBtn" class="btn btn-outline-primary btn-sm ms-2">\n                                        Next<i class="fas fa-chevron-right ms-1"></i>\n                                    </button>
                                </div>
                            </div>
                        </div>` : ''}
                    </div>
                </body>
                </html>
            `);

            // Wire up pagination handlers inside the preview window
            previewWindow.document.close();
            previewWindow.addEventListener('load', function() {
                let current = 1;
                const goTo = (p) => {
                    if (p < 1 || p > totalPages) return;
                    current = p;
                    renderPreviewPage(current);
                };
                const prevBtn = previewWindow.document.getElementById('prevBtn');
                const nextBtn = previewWindow.document.getElementById('nextBtn');
                if (prevBtn) prevBtn.onclick = () => goTo(current - 1);
                if (nextBtn) nextBtn.onclick = () => goTo(current + 1);
                const pageBtns = previewWindow.document.querySelectorAll('[data-page-btn]');
                pageBtns.forEach(btn => btn.addEventListener('click', () => goTo(parseInt(btn.getAttribute('data-page-btn')))));

                // Initial render
                renderPreviewPage(current);
            });
        }

        // Theme controls
        document.getElementById('primaryColor').addEventListener('change', function() {
            courseData.theme.primaryColor = this.value;
            renderCoursePreview();
        });

        document.getElementById('secondaryColor').addEventListener('change', function() {
            courseData.theme.secondaryColor = this.value;
            renderCoursePreview();
        });

        document.getElementById('fontFamily').addEventListener('change', function() {
            courseData.theme.fontFamily = this.value;
            renderCoursePreview();
        });

        document.getElementById('headerFontSize').addEventListener('input', function() {
            courseData.theme.headerFontSize = this.value + 'rem';
            document.querySelector('small').textContent = this.value + 'rem';
            renderCoursePreview();
        });

        document.getElementById('bodyFontSize').addEventListener('input', function() {
            courseData.theme.bodyFontSize = this.value + 'rem';
            document.querySelectorAll('small')[1].textContent = this.value + 'rem';
            renderCoursePreview();
        });

        // Pagination Settings removed: no listener for sectionsPerPageListSelect

        // File upload functionality
        function setupFileUpload() {
            const fileUploadArea = document.getElementById('fileUploadArea');
            const fileInput = document.getElementById('fileInput');
            
            if (fileUploadArea && fileInput) {
                if (fileUploadArea.dataset.bound === '1') {
                    console.log('File upload already initialized; skipping re-binding.');
                    return;
                }
                fileUploadArea.dataset.bound = '1';
                console.log('Setting up file upload functionality (bound listeners).');
                
                // Click to upload
                fileUploadArea.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('File upload area clicked');
                    fileInput.click();
                });
                
                // File selection
                fileInput.addEventListener('change', function(e) {
                    console.log('Files selected:', e.target.files);
                    handleFileSelection(e.target.files);
                });
                
                // Drag and drop functionality
                fileUploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.add('dragover');
                });
                
                fileUploadArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.remove('dragover');
                });
                
                fileUploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.remove('dragover');
                    
                    const files = e.dataTransfer.files;
                    console.log('Files dropped:', files);
                    handleFileSelection(files);
                });
            } else {
                console.error('File upload elements not found');
            }
        }
        
        function handleFileSelection(files) {
            if (files && files.length > 0) {
                console.log('Processing', files.length, 'files');
                
                // Create a container for uploaded files
                let filesContainer = document.getElementById('uploadedFilesContainer');
                if (!filesContainer) {
                    filesContainer = document.createElement('div');
                    filesContainer.id = 'uploadedFilesContainer';
                    filesContainer.className = 'uploaded-files mt-3';
                    document.getElementById('fileUploadArea').parentNode.appendChild(filesContainer);
                }
                
                // Clear previous files only if not editing
                if (editingSectionIndex === -1) {
                    filesContainer.innerHTML = '';
                    window.uploadedFiles = [];
                }
                
                // Process each file
                Array.from(files).forEach((file, index) => {
                    uploadFile(file, index, filesContainer);
                });
            }
        }

        // Get file type from file object
        function getFileTypeFromFile(file) {
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
        
        // Update description for uploaded file in memory
        function updateUploadedFileDescription(index, value) {
            if (!window.uploadedFiles) return;
            if (index < 0 || index >= window.uploadedFiles.length) return;
            window.uploadedFiles[index].description = value;
        }
        
        function uploadFile(file, index, container) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('courseId', <?php echo $courseId; ?>);
            
            // Auto-detect file type
            const fileType = getFileTypeFromFile(file);
            formData.append('fileType', fileType);
            
            // Show upload progress
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    <div>
                        <div class="fw-bold">${file.name}</div>
                        <small class="text-muted">Uploading...</small>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})" disabled>
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(fileItem);
            
            // Local preview for images before upload completes
            if (fileType === 'image') {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const previewImg = document.createElement('img');
                    previewImg.src = e.target.result;
                    previewImg.alt = file.name;
                    previewImg.style.maxWidth = '120px';
                    previewImg.style.maxHeight = '90px';
                    previewImg.style.marginRight = '10px';
                    previewImg.style.borderRadius = '6px';
                    const left = fileItem.querySelector('.d-flex.align-items-center');
                    if (left) {
                        const icon = left.querySelector('i');
                        if (icon) icon.remove();
                        left.insertBefore(previewImg, left.firstChild);
                    }
                };
                reader.readAsDataURL(file);
            }
            
            // Upload file
            fetch('php/upload-course-file.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                console.log('Upload response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.text().then(text => {
                    console.log('Upload response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Failed to parse JSON response:', e);
                        console.error('Response text:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                console.log('Upload response data:', data);
                
                if (data.success) {
                    const fileUrl = `<?php echo getImageUrl(''); ?>${data.filePath}`;
                    const isImage = (data.fileType === 'image');
                    // Store file info FIRST to get index for bindings
                    if (!window.uploadedFiles) window.uploadedFiles = [];
                    window.uploadedFiles.push({
                        fileId: data.fileId,
                        fileName: data.fileName,
                        filePath: data.filePath,
                        fileSize: data.fileSize,
                        fileType: data.fileType,
                        description: ''
                    });
                    const idx = window.uploadedFiles.length - 1;
                    const previewHtml = isImage 
                        ? `<img src="${fileUrl}" alt="${file.name}" style="max-width: 120px; max-height: 90px; margin-right: 10px; border-radius: 6px;">`
                        : `<i class="fas fa-check-circle text-success me-2"></i>`;
                    fileItem.innerHTML = `
                        <div class="d-flex align-items-center">
                            ${previewHtml}
                            <div>
                                <div class="fw-bold">${file.name}</div>
                                <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB - Uploaded</small>
                                <div class="mt-1">
                                    <input type="text" class="form-control form-control-sm" placeholder="File description (optional)" value="" oninput="updateUploadedFileDescription(${idx}, this.value)">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 6px;">
                            <a class="btn btn-sm btn-outline-primary" href="${fileUrl}" target="_blank" title="View File">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteUploadedFile(${data.fileId}, '${file.name}')" title="Delete File">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                    
                    console.log('File uploaded successfully:', data);
                } else {
                    console.error('Upload failed:', data.message);
                    fileItem.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle text-danger me-2"></i>
                            <div>
                                <div class="fw-bold">${file.name}</div>
                                <small class="text-danger">Upload failed: ${data.message}</small>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                fileItem.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle text-danger me-2"></i>
                        <div>
                            <div class="fw-bold">${file.name}</div>
                            <small class="text-danger">Upload failed: ${error.message}</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
            });
        }
        
        function removeFile(index) {
            // Remove from uploaded files array
            if (window.uploadedFiles && window.uploadedFiles.length > index) {
                window.uploadedFiles.splice(index, 1);
            }
            
            // Remove from selected files array
            if (window.selectedFiles) {
                const newFiles = Array.from(window.selectedFiles);
                newFiles.splice(index, 1);
                window.selectedFiles = newFiles;
            }
            
            // Re-render file list
            const filesContainer = document.getElementById('uploadedFilesContainer');
            if (filesContainer) {
                filesContainer.innerHTML = '';
                if (window.uploadedFiles && window.uploadedFiles.length > 0) {
                    window.uploadedFiles.forEach((file, idx) => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'file-item';
                        fileItem.innerHTML = `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <div>
                                    <div class="fw-bold">${file.fileName}</div>
                                    <small class="text-muted">${(file.fileSize / 1024 / 1024).toFixed(2)} MB - Uploaded</small>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeFile(${idx})" title="Delete File">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        filesContainer.appendChild(fileItem);
                    });
                }
            }
        }

        // Delete uploaded file from server and database
        function deleteUploadedFile(fileId, fileName) {
            if (confirm(`Are you sure you want to delete "${fileName}"? This action cannot be undone.`)) {
                // Send AJAX request to delete file
                fetch('php/delete-course-file.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        fileId: fileId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove from uploaded files array
                        if (window.uploadedFiles) {
                            window.uploadedFiles = window.uploadedFiles.filter(file => file.fileId !== fileId);
                        }
                        
                        // Re-render file list
                        const filesContainer = document.getElementById('uploadedFilesContainer');
                        if (filesContainer) {
                            filesContainer.innerHTML = '';
                            if (window.uploadedFiles && window.uploadedFiles.length > 0) {
                                window.uploadedFiles.forEach((file, idx) => {
                                    const fileItem = document.createElement('div');
                                    fileItem.className = 'file-item';
                                    fileItem.innerHTML = `
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <div>
                                                <div class="fw-bold">${file.fileName}</div>
                                                <small class="text-muted">${(file.fileSize / 1024 / 1024).toFixed(2)} MB - Uploaded</small>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUploadedFile(${file.fileId}, '${file.fileName}')" title="Delete File">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    `;
                                    filesContainer.appendChild(fileItem);
                                });
                            }
                        }
                        
                        showNotification('File deleted successfully!', 'success');
                    } else {
                        showNotification('Error deleting file: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error deleting file', 'error');
                });
            }
        }

        // Global modal cleanup function
        function cleanupModal() {
            const modalElement = document.getElementById('addSectionModal');
            if (modalElement) {
                // Remove all event listeners
                const newModalElement = modalElement.cloneNode(true);
                modalElement.parentNode.replaceChild(newModalElement, modalElement);
                
                // Remove any backdrop
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                
                // Remove modal classes
                modalElement.classList.remove('show', 'fade');
                modalElement.style.display = 'none';
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.removeAttribute('aria-modal');
                
                // Remove body classes
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                
                console.log('Modal cleanup completed');
            }
        }

        // Notification function
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.course-notification');
            existingNotifications.forEach(notification => notification.remove());
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `course-notification alert alert-${type} alert-dismissible fade show`;
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        }

        // Refresh course data from server
        function refreshCourseData() {
            fetch(`?refresh=1&courseId=<?php echo $courseId; ?>`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    courseData = data.courseData;
                    renderCoursePreview();
                    renderSectionsList();
                    showNotification('Course data refreshed!', 'info');
                }
            })
            .catch(error => {
                console.error('Error refreshing course data:', error);
            });
        }

        // Initialize
        renderCoursePreview();
        renderSectionsList();
        renderLinksList();

        // Add event listener to Add Section button
        document.getElementById('addSectionBtn').addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Add Section button clicked via event listener');
            showAddSectionModal();
        });

        // Add event delegation for section type cards (fallback)
        // Only use if direct listener is not already attached
        document.addEventListener('click', function(e) {
            if (e.target.closest('.section-type-card')) {
                const card = e.target.closest('.section-type-card');
                // Check if direct listener is attached (has _sectionTypeHandler)
                if (!card._sectionTypeHandler) {
                    console.log('Section type card clicked via delegation:', card.dataset.type);
                    handleSectionTypeClick.call(card, e);
                }
            }
        });

        // Add cleanup on page unload
        window.addEventListener('beforeunload', function() {
            cleanupModal();
        });

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>

</html>
