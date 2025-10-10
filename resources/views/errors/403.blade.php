<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - GGSS CRM</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .error-container {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #e74c3c;
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 1.5rem;
            color: #2c3e50;
            margin: 1rem 0;
        }
        .error-message {
            color: #7f8c8d;
            margin: 1rem 0 2rem;
            line-height: 1.6;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            transition: transform 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .icon {
            font-size: 4rem;
            color: #e74c3c;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon">🚫</div>
        <h1 class="error-code">403</h1>
        <h2 class="error-title">Access Denied</h2>
        <p class="error-message">
            You don't have permission to access this resource. 
            Please contact your administrator if you believe this is an error.
        </p>
        <a href="/" class="btn">Go Back Home</a>
    </div>
</body>
</html>
