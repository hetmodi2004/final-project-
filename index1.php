<?php
session_name('user');
session_start();
$isLoggedIn = isset($_SESSION['username']); // Check if user session is set
$username = $isLoggedIn ? htmlspecialchars($_SESSION['username']) : '';

// Database connection
include 'insert.php'; // Ensure this file contains your database connection code

// Fetch upcoming events
$events = [];
$sql = "SELECT title, event_date, description FROM event";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
} else {
    $events = []; // No events found
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decoration Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Georgia';
            font-size: 15px;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        .sidebar {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 10; /* Adjust z-index to ensure sidebar stays on top */
            top: 0;
            left: 0;
            background-color: #333;
            overflow-x: hidden;
            transition: 0.3s ease;
            padding-top: 60px;
        }

        .sidebar a {
            padding: 12px 30px;
            text-decoration: none;
            font-size: 20px;
            color: white;
            display: flex; /* Use flexbox for icon and text alignment */
            align-items: center; /* Center items vertically */
            transition: 0.3s ease;
            border-bottom: 1px solid #444;
        }

        /* Add margin to the left of icons */
        .sidebar a i {
            margin-right: 10px; /* Space between icon and text */
        }

        /* Sidebar Hover Effect */
        .sidebar a:hover {
            background-color: #444;
            color: white;
        }

        /* Logout Link Styling */
        .logout-link {
            background-color: red; /* Set background color to red */
            color: white; /* Ensure text color is white for contrast */
            padding: 12px 30px; /* Match padding with other links */
            text-decoration: none; /* Remove underline */
            display: flex; /* Use flexbox for icon and text alignment */
            align-items: center; /* Center items vertically */
            transition: background-color 0.3s ease; /* Smooth transition */
            margin-top: auto; /* Push the logout link to the bottom */
        }

        .logout-link:hover {
            background-color: darkred; /* Darker red on hover */
        }

        /* Hamburger Icon */
        .hamburger {
            font-size: 35px;
            color: black;
            cursor: pointer;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 20;
            transition: 0.3s;
        }

        .hamburger div {
            width: 35px;
            height: 5px;
            background-color: black;
            margin: 6px 0;
            transition: 0.4s;
        }

        .hamburger.open div {
            background-color: white;
        }

        .hamburger.open div:nth-child(1) {
            transform: rotate(-45deg);
            position: relative;
            top: 10px;
        }

        .hamburger.open div:nth-child(2) {
            opacity: 0;
        }

        .hamburger.open div:nth-child(3) {
            transform: rotate(45deg);
            position: relative;
            top: -10px;
        }

        /* Main Content Area */
        #main {
            transition: margin-left 0.3s ease;
            padding: 40px;
            margin-left: 60px;
        }

        #main.shifted {
            margin-left: 250px;
        }

        /* Header Styling */
        header {
            text-align: center;
            padding: 50px;
            background-color: #f7f7f7;
            margin-top: 40px;
            min-height: 200px; /* Set a minimum height for consistent spacing */
        }

        #t1 {
            font-size: 40px;
            color: #333;
        }

        .book-service-link {
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            font-size: 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .book-service-link:hover {
            background-color: #218838;
        }

        /* Special Offers Section */
        .special-offer {
            text-align: center;
            margin: 50px auto;
            padding: 40px 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .special-offer h2 {
            font-size: 36px;
            color: #333;
        }

        .special-offer p {
            font-size: 18px;
            color: #555;
        }

        .special-offer-timer {
            font-size: 24px;
            color: #d9534f;
            margin: 20px 0;
        }

        /* Showcase Section Styling */
        .showcase {
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .showcase-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .showcase-item {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .showcase-item img {
            width: 300px;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .showcase-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .showcase-text {
            color: #fff;
            font-size: 20px;
            text-align: center;
            padding: 10px;
        }

        .showcase-item:hover img {
            transform: scale(1.1);
        }

        .showcase-item:hover .showcase -overlay {
            opacity: 1;
        }
        .upcoming-events {
    text-align: center;
    margin: 50px auto;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa, #e3e6ea);
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    width: 80%;
    max-width: 600px;
    height: 400px;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.upcoming-events h2 {
    font-size: 28px;
    font-weight: bold;
    color: #333;
    margin: 0;
    padding-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 2px solid #007bff;
    display: inline-block;
}

.events-container {
    overflow: hidden; /* Hide scrollbar */
    height: 300px;
    position: relative;
    padding: 10px;
}

.upcoming-events ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
    animation: scrollEvents 10s linear infinite;
}

.upcoming-events li {
    font-size: 18px;
    color: #444;
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, background 0.3s ease;
}

.upcoming-events li:hover {
    transform: scale(1.05);
    background: #f0f0f0;
}

@keyframes scrollEvents {
    0% { transform: translateY(0); }
    100% { transform: translateY(-100%); } /* Scroll up */
}

/* Add glowing effect to the container */
.upcoming-events::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.2);
    box-shadow: 0 0 20px rgba(0, 123, 255, 0.5);
    z-index: -1;
    border-radius: 12px;
}


        .gallery img {
            width: 100%;
            height: auto; /* Maintain aspect ratio */
            display: none; /* Hide images by default */
        }

        .gallery img.active {
            display: block; /* Show the active image */
        }

        /* Logo Hover Tooltip Styling */
        .logo-container {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
        }

        .logo {
            margin-top: -10px;
            width: 220px;
            height: auto;
        }

        .tooltip {
            display: none;
            position: absolute;
            top: 100%; /* Appear just below the logo */
            right: 0;
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            width: 200px;
            font-size: 14px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            z-index: 15;
        }

        .logo-container:hover .tooltip {
            display: block;
        }

        /* Adjustments for consistent spacing */
        #main {
            padding-top: 100px; /* Ensure there's space for the logo */
        }

        .welcome-message {
            font-size: 36px; /* Increased font size */
            margin-bottom: 20px; /* Space below the heading */
        }

        .greeting-message {
            font-size: 20px; /* Optional: Increase the size of the greeting message */
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div id="mySidebar" class="sidebar">
        <a href="#" onclick="closeNav()"><i class="fas fa-home"></i> Home</a>
        <?php if ($isLoggedIn): ?>
            <a href="./about.php" onclick="closeNav()"><i class="fas fa-info-circle"></i> About Us</a>
            <a href="./package.php" onclick="closeNav()"><i class="fas fa-gift"></i> Services</a>
            <a href="contact.php" onclick="closeNav()"><i class="fas fa-envelope"></i> Contact</a>
            <a href="./data.php" onclick="closeNav()"><i class="fas fa-list-alt"></i> Show Bookings</a>
            <a href="logout.php" class="logout-link" onclick="closeNav()"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="login.php" onclick="closeNav()"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php endif; ?>
    </div>

    <!-- Hamburger Icon -->
    <div class="hamburger" onclick="toggleNav()">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <!-- Company Logo with Hover Info -->
    <div class="logo-container">
        <img src="./uploads/logo.png" alt="Company Logo" class="logo ">
        <div class="tooltip">
            <p>HM Decoration</p>
            <p>Creating Elegant Designs</p>
            <p>Contact: +91 6351709559</p>
            <p>Email: hmdecoration@gmail.com</p>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main">
        <h2 class="welcome-message">Welcome to HM Decoration</h2>
        <?php if ($isLoggedIn): ?>
            <p class="greeting-message">Hello, <?php echo $username; ?>!</p>
        <?php endif; ?>
    </div>

    <!-- Header Section -->
    <header>
        <h1 id="t1">Let us do the beauty designs,<br>that you never seen before.</h1>
        <div style="text-align: center; margin-top: 20px;">
            <?php if (!$isLoggedIn): ?>
                <a href="./login.php" class="book-service-link">LOGIN TO BOOK SERVICE</a>
            <?php else: ?>
                <a href="./package.php" class="book-service-link">BOOK SERVICE</a>
            <?php endif; ?>
            <a href="./review.php" class="book-service-link">READ REVIEWS</a>
        </div>
    </header>

    <!-- Showcase Section -->
    <div class="showcase">
        <h2 style="text-align: center; margin: 40px 0; color: #333;">Our Beautiful Creations</h2>
        <div class="showcase-container">
            <!-- Showcase Items -->
            <div class="showcase-item">
                <img src="./uploads/img1.png" alt="Showcase 1">
                <div class="showcase-overlay">
                    <div class="showcase-text">
                        Elegant Wedding Stage Design
                    </div>
                </div>
            </div>

            <div class="showcase-item">
                <img src="./img2.png" alt="Showcase 2">
                <div class="showcase-overlay">
                    <div class="showcase-text">
                        Outdoor Event Setup with Lights
                    </div>
                </div>
            </div>

            <div class="showcase-item">
                <img src="./img3.png" alt="Showcase 3">
                <div class="showcase-overlay">
                    <div class="showcase-text">
                        Birthday Party Theme Decoration
                    </div>
                </div>
            </div>
        </div>
    </div>

 <!-- Upcoming Events Section -->
<div class="upcoming-events">
    <h2>Upcoming Events</h2>
    <p>Join us for our exciting upcoming events!</p><br><br>
    <div class="events-container">
        <ul>
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($event['title']); ?></strong><br>
                        <em><?php echo htmlspecialchars($event['event_date']); ?></em><br>
                        <?php echo htmlspecialchars($event['description']); ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No upcoming events available.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

    <!-- Footer -->
    <footer style="background-color: #333; color: white; text-align: center; padding: 20px; margin-top: 40px;">
        <p>&copy; HM Decoration</p>
        <p>Developed by Het Modi</p>
    </footer>

    <script>
        // Sidebar Toggle Script
        let isOpen = false;

        function toggleNav() {
            if (isOpen) {
                closeNav();
            } else {
                openNav();
            }
            isOpen = !isOpen;
        }

        function openNav() {
            document.getElementById("mySidebar").style.width = "250px";
            document.getElementById("main").classList.add("shifted");
            document.querySelector(".hamburger").classList.add("open");
        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
            document.getElementById("main").classList.remove("shifted");
            document.querySelector(".hamburger").classList.remove("open");
        }

        // Automatic Gallery Slider
        let currentIndex = 0;
        const images = document.querySelectorAll('.gallery img');
        const totalImages = images.length;

        function showNextImage() {
            images[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % totalImages; // Loop back to the first image
            images[currentIndex].classList.add('active');
        }

        setInterval(showNextImage, 3000); // Change image every 3 seconds
    </script>
</body>

</html>