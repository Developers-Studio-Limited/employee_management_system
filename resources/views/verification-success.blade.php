<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            text-align: center;
            margin-top: 100px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        @if (Route::currentRouteName() === 'verification.success')
            <h1>Email Verification Success</h1>
            <p>Your email has been successfully verified.</p>
        @elseif (Route::currentRouteName() === 'verification.already-success')
            <h1>Email Already Verified</h1>
            <p>Your email is already verified</p>
        @else
            <h1>Default Content</h1>
            <p>This is the default content.</p>
        @endif
    </div>
    
</body>
</html>
