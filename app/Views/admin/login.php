<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Maithili Bikash Kosh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, rgba(26, 26, 26, 0.9) 0%, rgba(139, 0, 0, 0.8) 100%), 
                       url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 400"><defs><pattern id="mithila" patternUnits="userSpaceOnUse" width="100" height="100"><circle cx="50" cy="50" r="20" fill="%23ffffff" opacity="0.1"/><path d="M20,20 Q50,10 80,20 Q90,50 80,80 Q50,90 20,80 Q10,50 20,20" fill="none" stroke="%23ffffff" stroke-width="2" opacity="0.2"/></pattern></defs><rect width="1000" height="400" fill="url(%23mithila)"/></svg>'); 
            background-size: cover, 200px 200px; 
            background-position: center, center; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            padding: 1rem;
        }
        .login-container { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            padding: 2rem; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3); 
            width: 100%; 
            max-width: 400px;
            border: 1px solid rgba(139, 0, 0, 0.1);
        }
        @media (min-width: 768px) {
            .login-container { padding: 3rem; }
        }
        .login-header { 
            text-align: center; 
            margin-bottom: 2rem; 
        }
        .login-header h1 { 
            color: #8B0000; 
            margin-bottom: 0.5rem; 
            font-size: clamp(1.5rem, 4vw, 2rem);
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .login-header p { 
            color: #666; 
            font-size: clamp(0.9rem, 2vw, 1rem);
        }
        .form-group { 
            margin: 1.5rem 0; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 0.5rem; 
            font-weight: 500; 
            color: #1a1a1a;
            font-size: clamp(0.9rem, 2vw, 1rem);
        }
        .form-group input { 
            width: 100%; 
            padding: 0.8rem; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-size: clamp(0.9rem, 2vw, 1rem);
            transition: all 0.3s;
            background: white;
        }
        .form-group input:focus { 
            outline: none; 
            border-color: #8B0000; 
            box-shadow: 0 0 0 2px rgba(139,0,0,0.1);
        }
        .btn { 
            background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%); 
            color: white; 
            padding: 0.8rem 2rem; 
            border: none; 
            border-radius: 25px; 
            cursor: pointer; 
            width: 100%; 
            font-size: clamp(0.9rem, 2vw, 1rem); 
            font-weight: 500;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(139, 0, 0, 0.3);
        }
        .btn:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(139, 0, 0, 0.4);
            background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        }
        .btn:active {
            transform: translateY(0);
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 1rem; 
            border-radius: 8px; 
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
        }
        .credentials { 
            background: rgba(139, 0, 0, 0.1); 
            color: #8B0000; 
            padding: 1rem; 
            border-radius: 8px; 
            margin-top: 1rem; 
            font-size: clamp(0.8rem, 2vw, 0.9rem); 
            text-align: center;
            border: 1px solid rgba(139, 0, 0, 0.2);
        }
        .back-link {
            text-align: center;
            margin-top: 1rem;
        }
        .back-link a {
            color: #8B0000;
            text-decoration: none;
            font-size: clamp(0.8rem, 2vw, 0.9rem);
            transition: all 0.3s;
        }
        .back-link a:hover {
            color: #A52A2A;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
            <p>Maithili Bikash Kosh Gallery</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="loginForm">
            <?= CSRFProtection::getTokenField() ?>
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username:</label>
                <input type="text" name="username" required autocomplete="username" maxlength="50">
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password:</label>
                <input type="password" name="password" required autocomplete="current-password" minlength="6">
            </div>
            <button type="submit" class="btn" id="loginBtn">
                <span class="btn-text">Login</span>
                <span class="btn-loading" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Logging in...
                </span>
            </button>
        </form>
        
        <div class="credentials">
            <strong>Default Credentials:</strong><br>
            Username: admin | Password: admin123<br>
            <small style="color: #666; margin-top: 5px; display: block;">
                <i class="fas fa-info-circle"></i> Change default password after first login
            </small>
        </div>
        
        <div class="back-link">
            <a href="/gallery/"><i class="fas fa-arrow-left"></i> Back to Gallery</a>
        </div>
    </div>
    
    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            const btnText = btn.querySelector('.btn-text');
            const btnLoading = btn.querySelector('.btn-loading');
            
            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
        });
        
        // Auto-focus username field
        document.querySelector('input[name="username"]').focus();
    </script>
</body>
</html>