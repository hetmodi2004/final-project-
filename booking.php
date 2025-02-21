<?php
session_name('user');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'insert.php';

$package_name = isset($_GET['package_name']) ? urldecode($_GET['package_name']) : '';
$package_price = '';
$message = '';

// Fetch the price of the selected package
if ($package_name) {
    $stmt = $conn->prepare("SELECT package_price FROM package WHERE package_name = ?");
    if ($stmt === false) {
        die("Error in SQL query preparation: " . $conn->error);
    }

    $stmt->bind_param("s", $package_name);
    $stmt->execute();
    $stmt->bind_result($package_price);
    $stmt->fetch();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_number = $_POST['customer_number']; // New contact number field
    $event_date = $_POST['event_date']; // Updated field name
    $package_name = $_POST['package_name'];
    $payment_method = $_POST['payment_method'];

    // Simulate Payment Confirmation
    switch ($payment_method) {
        case "Cash on Delivery":
            $payment_status = "Cash on Delivery selected. Payment will be collected on delivery.";
            break;
        default:
            $payment_status = "Payment method not selected.";
    }

    // Save booking details in database
    $stmt = $conn->prepare("INSERT INTO booking (customer_name, customer_email, customer_number, event_date, package_name, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Error in SQL query preparation: " . $conn->error);
    }

    $stmt->bind_param("sssssss", $customer_name, $customer_email, $customer_number, $event_date, $package_name, $payment_method, $payment_status);

    if ($stmt->execute()) {
        $message = "<script>
                        alert('Booking successful! $payment_status');
                        setTimeout(function() {
                            window.location.href = 'index1.php';
                        }, 1000);
                    </script>";
    } else {
        $message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en" ng-app="bookingApp" ng-controller="BookingController">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    <title>Booking Form</title>
    <style>
        body {
            font-family: 'Georgia';
            font-size: 15px;
            margin: 20px;
            background-color: #f4f6f9;
        }

        .form-container {
            max-width: 700px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            color: #2c3e50;
        }

        .form-container label {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
            display: block;
        }

        .form-container input, 
        .form-container select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }

        .form-container input:focus,
        .form-container select:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
            outline: none;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #2980b9;
        }

        .back-home {
            text-decoration: none;
            font-size: 18px;
            color: #3498db;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .back-home:hover {
            color: #2980b9;
        }

        .back-home svg {
            margin-right: 8px;
            fill: currentColor;
        }

        .payment-section {
            background-color: #eaf4f4;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
        }

        .payment-section label {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
        }

        .payment-section .error {
            font-size: 14px;
            color: #e74c3c;
            margin-top: 5px;
        }

        .custom-radio {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .custom-radio input[type="radio"] {
            width: 10px;
            height: 10px;
            margin-right: 10px;
            appearance: none;
            border-radius: 30%;
            border: 2px solid #3498db;
            background-color: #fff;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .custom-radio input[type="radio"]:checked {
            background-color: #3498db;
            border-color: #3498db;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.3);
        }

        .custom-radio input[type="radio"]:checked + .radio-label {
            color: #3498db;
        }

        .custom-radio input[type="radio"]:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        .radio-label {
            font-size: 16px;
            color: #333;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="form-container">
    <a href="package.php" class="back-home">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
        </svg>
        Back to package
    </a>

    <h2>BOOK PACKAGE</h2>

    <?php if (isset($message)) echo $message; ?>

    <form name="bookingForm" method="POST" action="" novalidate>
        <label for="package_name">Selected Package:</label>
        <input type="text" id="package_name" name="package_name" value="<?php echo htmlspecialchars($package_name); ?>" readonly>

        <label for="package_price">Price:</label>
        <input type="text" id="package_price" name="package_price" value="<?php echo htmlspecialchars($package_price); ?>" readonly>

        <label for="customer_name">Your Name:</label>
        <input type="text" id="customer_name" name="customer_name" ng-model="formData.customer_name" required>
        <span class="error" ng-show="bookingForm.customer_name.$touched && bookingForm.customer_name.$invalid">Name is required.</span>

        <label for="customer_email">Your Email:</label>
        <input type="email" id="customer_email" name="customer_email" ng-model="formData.customer_email" required>
        <span class="error" ng-show=" bookingForm.customer_email.$touched && bookingForm.customer_email.$invalid">Valid email is required.</span>

        <label for="customer_number">Your Contact Number:</label>
        <input type="tel" id="customer_number" name="customer_number" ng-model="formData.customer_number" required pattern="[0-9]{10,15}" title="Please enter a valid phone number (10 to 15 digits)">
        <span class="error" ng-show="bookingForm.customer_number.$touched && bookingForm.customer_number.$invalid">Contact number is required.</span>

        <label for="event_date">Event Date:</label>
        <input type="date" id="event_date" name="event_date" ng-model="formData.event_date" required min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+1 month')); ?>">
        <span class="error" ng-show="bookingForm.event_date.$touched && bookingForm.event_date.$invalid">Event date is required and cannot be in the past.</span>

        <div class="payment-section">
            <label for="payment_method">Payment Method:</label>
            <div class="custom-radio">
                <input type="radio" id="cash_on_delivery" name="payment_method" value="Cash on Delivery" ng-model="formData.payment_method" required>
                <label for="cash_on_delivery" class="radio-label">Cash on Delivery</label>
            </div>
            <span class="error" ng-show="bookingForm.payment_method.$touched && bookingForm.payment_method.$invalid">Payment method is required.</span>
        </div>

        <button type="submit" ng-disabled="bookingForm.$invalid">Book Now</button>
    </form>
</div>

<script>
    var app = angular.module('bookingApp', []);
    app.controller('BookingController', function($scope) {
        $scope.formData = {};
    });
</script>

</body>
</html>