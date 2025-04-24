<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Completion Notification</title>
    <style>
        body {
            background-color: #f7fafc; /* gray-100 */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 32px auto; /* my-8 */
            padding: 24px; /* p-6 */
            background-color: #ffffff; /* white */
            border-radius: 8px; /* rounded-lg */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* shadow-md */
        }
        h2 {
            font-size: 24px; /* text-2xl */
            font-weight: bold; /* font-bold */
            margin-bottom: 16px; /* mb-4 */
        }
        p {
            color: #4a5568; /* text-gray-700 */
            margin-bottom: 16px; /* mb-4 */
        }
        a {
            display: inline-block;
            background-color: #38a169; /* bg-green-500 */
            color: #ffffff; /* text-white */
            font-weight: 600; /* font-semibold */
            padding: 10px 16px; /* py-2 px-4 */
            border-radius: 8px; /* rounded */
            text-decoration: none; /* no underline */
            transition: background-color 0.2s; /* transition duration-200 */
        }
        a:hover {
            background-color: #3182ce; /* hover:bg-blue-600 */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {{ $details->client_name }}!</h2>
        <p>We are happy to inform you that your service <strong>{{ $details->description }}</strong> with <strong>{{ $details->pilot_name }}</strong> has been completed successfully.</p>
        <p>Thank you for choosing our service! You can now leave a review for the service you received.</p>
        <p>To leave a review, please click the link below:</p>
        <a href="{{ env('APP_FRONTEND_URL') }}/review/{{ $details->id }}">Leave a Review</a>
        <hr> <br>
        <p>If you did not request this service, no further action is required.</p>
        <p>Best Regards,</p>
        <p>QuestProxy Team</p>
    </div>
</body>
</html>
