<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="{{ url_for('static', filename='styles.css') }}">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .login-container {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        input, button {
            
            width: 200px;
        }
        button {
        
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            display: block;
            margin-top: 10px;
            text-decoration: none;
            color: #007BFF;
        }
        p {
            padding: 1px;
            text-align: left;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
    <h2>Login</h2>

    <form method="post">
        <div>
        <p>Username</p>
        <input type="email" name="email" placeholder="Email" required>
        
        <p>Password</p>
        <input type="password" name="password" placeholder="Password" required>
        </div>
        <div>
        <button type="submit">Login</button></div>
        
    </form>
    <a href="{{ url_for('register') }}" style="font-size:15px">Register</a>
    </div>
    <script>
        // Automatically hide the flash message after 3 seconds
        setTimeout(function () {
            var flashMessage = document.getElementById('flashMessage');
            if (flashMessage) {
                flashMessage.classList.add('hide');
                setTimeout(() => flashMessage.style.display = 'none', 500);
            }
        }, 3000);
    </script>
</body>
</html>
