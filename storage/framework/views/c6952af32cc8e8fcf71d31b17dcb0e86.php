<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LabMoneyLens - Login</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')('resources/css/login.css'); ?>
</head>
<body>

    <div class="login-card">
        
        <div class="left-side">
            <div class="logo-image-container">
                <img src="<?php echo e(asset('image/logo.png')); ?>?v=<?php echo e(time()); ?>" alt="Logo MoneyLens" class="site-logo">
            </div>
        </div>

        <div class="right-side">
            <form action="<?php echo e(route('login.post')); ?>" method="POST" class="login-form">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label class="form-label" for="identifier">
                        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        Username / Email
                    </label>
                    <input type="text" id="identifier" name="identifier" class="form-input" placeholder="USERNAME atau EMAIL" value="<?php echo e(old('identifier')); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        Password
                    </label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="PASSWORD" required>
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn-signin">SIGN IN</button>
                </div>
            </form>
        </div>

    </div>

    <!-- Error Modal: Username Not Found -->
    <div id="usernameModal" class="modal" style="display: none;">
        <div class="modal-content modal-error-username">
            <div class="modal-header">
                <h2>Username Tidak Ditemukan</h2>
                <button type="button" class="modal-close" onclick="closeModal('usernameModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p>Username atau email yang Anda masukkan tidak terdaftar dalam sistem. Silakan periksa kembali atau hubungi administrator.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn" onclick="closeModal('usernameModal')">OK</button>
            </div>
        </div>
    </div>

    <!-- Error Modal: Password Invalid -->
    <div id="passwordModal" class="modal" style="display: none;">
        <div class="modal-content modal-error-password">
            <div class="modal-header">
                <h2>Password Salah</h2>
                <button type="button" class="modal-close" onclick="closeModal('passwordModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p>Password yang Anda masukkan tidak sesuai. Silakan coba lagi dengan password yang benar.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn" onclick="closeModal('passwordModal')">OK</button>
            </div>
        </div>
    </div>

    <script>
        // Show appropriate modal based on error type
        <?php if($errors->has('type')): ?>
            <?php if($errors->first('type') === 'username_not_found'): ?>
                showModal('usernameModal');
            <?php elseif($errors->first('type') === 'password_invalid'): ?>
                showModal('passwordModal');
            <?php endif; ?>
        <?php endif; ?>

        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.style.display = 'none';
                });
            }
        });
    </script>

</body>
</html><?php /**PATH C:\yaya\LabMoneyLens\resources\views/login.blade.php ENDPATH**/ ?>