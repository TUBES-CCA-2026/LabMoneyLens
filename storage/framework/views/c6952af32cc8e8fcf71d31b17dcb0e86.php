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

                <?php if($errors->any()): ?>
                    <div class="login-error"><?php echo e($errors->first()); ?></div>
                <?php endif; ?>

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

</body>
</html><?php /**PATH C:\yaya\LabMoneyLens\resources\views/login.blade.php ENDPATH**/ ?>