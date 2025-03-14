<?php
session_start();
$form_filled = isset($_COOKIE['form_filled']) ? true : false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['dismiss'])) {
        setcookie('form_filled', 'true', time() + (86400 * 30), "/");
        $form_filled = true;
    } elseif (isset($_POST['continue'])) {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            header("Location: form.php");
            exit();
        } else {
            header("Location: auth.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Selection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .popoupsection {
            width: 100%;
            height: 100vh;
            /* background-color: black; */
            /* position: fixed; */
            font-family: 'Poppins', sans-serif;
            /* z-index: 2000000 !important; */
        }
        .popoupsection .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
            background: #fff9f0;
            animation: bounceIn 0.6s ease-out;
        }
        .popoupsection .modal-header {
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            border: none;
            position: relative;
        }
        .popoupsection .modal-header:after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 20px solid #fad0c4;
        }
        .popoupsection .modal-title {
            font-weight: 600;
            color: #fff;
            font-size: 1.5rem;
        }
        .popoupsection .modal-body {
            padding: 30px;
            text-align: center;
        }
        .popoupsection .emoji-bubble {
            font-size: 3rem;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        .popoupsection .btn-custom {
            padding: 12px 25px;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            margin: 0 10px;
        }
        .popoupsection .btn-primary {
            background: #4bc2c5;
            color: white;
        }
        .popoupsection .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(75, 194, 197, 0.4);
        }
        .popoupsection .btn-secondary {
            background: #ff7a7a;
            color: white;
        }
        .popoupsection .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 122, 122, 0.4);
        }
        @keyframes bounceIn {
            0% { transform: scale(0.5); opacity: 0; }
            60% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .popoupsection .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #ffd700;
            border-radius: 50%;
            animation: confetti 2s linear infinite;
        }
    </style>
<!-- </head> -->
<div class="popoupsection">

<div class="modal fade" id="courseFormModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-graduation-cap"></i> Course Matchmaker!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="emoji-bubble">🎓✨</div>
                <h4 class="mb-4" style="color: #4a4a4a;">Hey there!<br>Ready to find your perfect course match?</h4>
                <p class="text-muted mb-4" style="font-size: 0.9rem;">
                    We'd love to help you find the perfect course!<br>
                    Just 2 quick questions - promise it'll be fun! 🚀
                </p>
                <div class="d-flex justify-content-center">
                    <form method="POST" action="" class="d-flex gap-3">
                        <button type="submit" name="dismiss" class="btn btn-secondary btn-custom">
                            No thanks <i class="fas fa-heart-broken"></i>
                        </button>
                        <button type="submit" name="continue" class="btn btn-primary btn-custom">
                            Let's Go! <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    <?php if (!$form_filled): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('courseFormModal'), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
        
        // Add some dynamic confetti
        const modalContent = document.querySelector('.modal-content');
        for (let i = 0; i < 20; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.animationDelay = Math.random() * 2 + 's';
            confetti.style.background = `hsl(${Math.random() * 360}, 70%, 60%)`;
            modalContent.appendChild(confetti);
        }
    });
    <?php endif; ?>
</script>

</div>
</html>