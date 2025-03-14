<?php
session_name('user');
session_start(); // Start the session to access session variables
include 'insert.php';  // Your database connection script

// Check if the booking_id is passed through the URL
if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    // Check if the session variable for the username is set
    if (isset($_SESSION['username'])) {
        // Get the logged-in user's username from the session
        $logged_in_username = trim($_SESSION['username']);
    } else {
        header('Location: adminlog.php');
        exit();
    }
} else {
    echo "No booking ID found.";
    exit();
}

// Process the feedback submission when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate the feedback input
    $feedback = trim($_POST['feedback']);
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0; // Ensure the rating is an integer

    // Check if the feedback and rating are valid
    if (!empty($feedback) && $rating > 0 && $rating <= 5) {
        // SQL query to insert feedback into the database
        $sql = "INSERT INTO feedback (booking_id, username, feedback, rating) 
                VALUES (?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters and execute the statement
            $stmt->bind_param("issi", $booking_id, $logged_in_username, $feedback, $rating);
            if ($stmt->execute()) {
                // Redirect user after successful feedback submission
                header('Location: index1.php');  // Change 'thank_you.php' to your desired page
                exit();
            } else {
                echo "Error: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing the statement: " . $conn->error;
        }
    } else {
        echo "<p>Please provide both feedback and a rating between 1 and 5.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            width: 50%;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .star-rating {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 30px;
            color: #ddd;
            cursor: pointer;
        }
        .star-rating input:checked ~ label,
        .star-rating input:hover ~ label {
            color: #f39c12; /* Yellow color for selected stars */
        }
        .star-rating input:checked {
            color: #f39c12;
        }
        .star-rating input:hover {
            color: #f39c12;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>Leave Your Feedback</h2>

<form method="POST">
    <label for="rating">Rating (1 to 5):</label>
    <div class="star-rating">
        <input type="radio" name="rating" id="star5" value="5"><label for="star5">★</label>
        <input type="radio" name="rating" id="star4" value="4"><label for="star4">★</label>
        <input type="radio" name="rating" id="star3" value="3"><label for="star3">★</label>
        <input type="radio" name="rating" id="star2" value="2"><label for="star2">★</label>
        <input type="radio" name="rating" id="star1" value="1"><label for="star1">★</label>
    </div>

    <label for="feedback">Your Feedback:</label>
    <textarea id="feedback" name="feedback" required></textarea>

    <input type="submit" value="Submit Feedback">
</form>

</body>
</html>
