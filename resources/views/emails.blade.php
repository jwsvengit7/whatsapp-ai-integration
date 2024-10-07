
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Email</title>
    <style>
        /* Reset some default styles for better consistency */
        body, h1, p {
            margin: 0;
            padding: 0;
        }

        /* Basic styling for the email container */
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

        /* Instruction styles */
        .instructions {
            margin-top: 20px;
        }

        /* Button styles */
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
        <h3>One-Time Password (OTP) Verification</h3>
    </div>
    <p>Hello {{ $userName }},</p>
    <p></p>
    <p class="instructions">Please use the provided OTP code to complete your verification process.</p>
    <p>If you did not request this OTP, please ignore this email.</p>
    <p class="instructions">
        <a class="action-button">{{$otp}}</a>
    </p>
</div>
</body>
</html>
