
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Confirmation</title>
    <style>
        body, h1, p {
            margin: 0;
            padding: 0;
        }


        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        /* Header styles */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        /* OTP code styles */
        .otp-code {
            display: inline-block;
            padding: 10px;
            background-color: #f2f2f2;
            font-size: 24px;
            font-weight: bold;
            border-radius: 5px;
        }

        .instructions {
            margin-top: 20px;
        }

        .action-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h3>Admin Confirmation</h3>
    </div>
    <p>Hello {{ $user.name }},</p>
    <p></p>
    <p class="instructions">Your account has been created</p>
    <p>This is your information:</p>
    <p class="instructions">
        <a class="action-button">{{$user.email}}</a>
    </p>
    <p class="instructions">
        <a class="action-button">{{$user.password}}</a>
    </p>
</div>
</body>
</html>
