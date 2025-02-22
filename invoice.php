<?php
session_name('user');
session_start(); // Start the session to access session variables
include 'insert.php';

// Check if the booking_id is provided in the URL
if (isset($_GET['booking_id'])) {
    // Sanitize the booking_id parameter from the URL
    $booking_id = htmlspecialchars($_GET['booking_id']);

    // SQL query to fetch the booking details based on the booking_id
    $sql = "SELECT customer_name, package_name, event_date, status 
            FROM booking 
            WHERE package_id = ?"; // Use package_id as the unique identifier for the booking

    if ($stmt = $conn->prepare($sql)) {
        // Bind the booking_id parameter and execute the query
        $stmt->bind_param("s", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if booking details are found
        if ($result->num_rows > 0) {
            // Fetch the booking details
            $row = $result->fetch_assoc();
            $customer_name = htmlspecialchars($row['customer_name']);
            $package_name = htmlspecialchars($row['package_name']);
            $booking_date = htmlspecialchars($row['event_date']);
            $status = htmlspecialchars($row['status']);

            echo "<style>
                    body {
                        font-family: 'Arial', sans-serif;
                        margin: 20px;
                        background-color: #f4f4f4;
                    }
                    h1, h2 {
                        text-align: center;
                        color: #333;
                        margin-bottom: 20px;
                    }
                    .invoice-container {
                        width: 70%;
                        margin: 20px auto;
                        padding: 20px;
                        background-color: white;
                        border: 1px solid #ddd;
                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                    }
                    .invoice-details {
                        margin-bottom: 20px;
                    }
                    .invoice-details p {
                        font-size: 18px;
                        line-height: 1.6;
                    }
                    .invoice-footer {
                        text-align: center;
                        margin-top: 30px;
                        font-size: 16px;
                    }
                    .invoice-header {
                        text-align: center;
                        margin-bottom: 30px;
                    }
                    .invoice-header h2 {
                        color: #333;
                    }
                    .back-home {
                        text-decoration: none;
                        font-size: 18px;
                        color: #3498db;
                        display: inline-flex;
                        align-items: center;
                        margin-bottom: 20px;
                    }
                    .back-home:hover {
                        color: #2980b9;
                    }
                    .back-home svg {
                        margin-right: 8px;
                        fill: currentColor;
                    }
                  </style>";

            // Display the invoice header
            echo "<div class='invoice-container'>
                    <div class='invoice-header'>
                        <h2>Invoice for Package Booking</h2>
                    </div>";

            // Display the booking details
            echo "<div class='invoice-details'>
                    <p><strong>Customer Name:</strong> " . $customer_name . "</p>
                    <p><strong>Package Name:</strong> " . $package_name . "</p>
                    <p><strong>event Date:</strong> " . $booking_date . "</p>
                    <p><strong>Status:</strong> " . $status . "</p>
                  </div>";

            // Invoice footer with download and print options
            echo "<div class='invoice-footer'>
                    <p><a href='javascript:window.print()'>Print Invoice</a></p>
                    <p><a href='index1.php' class='back-home'>
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' viewBox='0 0 16 16'>
                            <path fill-rule='evenodd' d='M15 8a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z'/>
                        </svg>
                        Back to Home
                    </a></p>
                  </div>";

            echo "</div>"; // Close the invoice container
        } else {
            echo "<p>No booking details found for the given booking ID.</p>";
        }

        // Close the statement
        $stmt->close();
    } else {
        // Display error message if the statement fails
        echo "Error preparing the statement: " . $conn->error;
    }
} else {
    echo "<p>No booking ID provided.</p>";
}

// Close the connection
$conn->close();
?>
