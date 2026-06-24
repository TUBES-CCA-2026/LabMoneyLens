<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LabMoneyLens - Login</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="login-card">
        
        <div class="left-side">
            <div class="logo-image-container">
                <img src="{{ asset('image/logo.png') }}?v={{ time() }}" alt="Logo MoneyLens" class="site-logo">
            </div>
        </div>

        <div class="right-side">
            <form action="#" method="POST" class="login-form">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="username">
                        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        Username
                    </label>
                    <input type="text" id="username" class="form-input" placeholder="USERNAME" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        Password
                    </label>
                    <input type="password" id="password" class="form-input" placeholder="PASSWORD" required>
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn-signin">SIGN IN</button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>