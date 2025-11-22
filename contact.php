<?php
require_once 'config.php';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contact'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Get user ID if logged in
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    
    $sql = "INSERT INTO feedbacks (user_id, subject, message) VALUES ('$user_id', '$subject', '$message')";
    if (mysqli_query($conn, $sql)) {
        $contact_success = "Thank you for your feedback! We'll get back to you soon.";
    } else {
        $contact_error = "Error: " . mysqli_error($conn);
    }
}

// Handle subscription
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subscribe'])) {
    // Handle premium subscription logic here
    $subscription_success = "Subscription processed successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - mymelody</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add your CSS styles from contact.html here */
        :root {
            --primary: #ff9ec8;
            --secondary: #b5e8ff;
            --accent: #ffd166;
            --text: #5a5a5a;
            --light: #fff9fc;
            --card-bg: #ffffff;
            --shadow: rgba(255, 158, 200, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light);
            color: var(--text);
            line-height: 1.6;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px var(--shadow);
            border-radius: 0 0 25px 25px;
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 20px;
        }
        
        .search-form {
            display: flex;
            width: 100%;
            max-width: 500px;
            margin-top: 10px;
        }
        
        main {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .contact-info, .contact-form {
            background-color: var(--card-bg);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 15px var(--shadow);
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .contact-icon {
            background-color: var(--accent);
            color: white;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.2rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }
        
        footer {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
            border-radius: 25px 25px 0 0;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-music"></i>
                <h1>mymelody</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="main.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="hits.php"><i class="fas fa-chart-line"></i> Monthly Hits</a></li>
                    <li><a href="contact.php" class="active"><i class="fas fa-envelope"></i> Contact</a></li>
                </ul>
            </nav>
            <form class="search-form" action="main.php" method="POST">
                <input type="text" name="query" placeholder="Search for songs, artists, or playlists...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </header>
    
    <main>
        <div class="page-title">
            <i class="fas fa-envelope"></i>
            <h2>Contact Us</h2>
        </div>
        
        <div class="contact-container">
            <section class="contact-info">
                <h3><i class="fas fa-info-circle"></i> Get In Touch</h3>
                <div class="contact-details">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Our Location</h4>
                            <p>123 Melody Street<br>Music City, MC 12345</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Phone Number</h4>
                            <p>+1 (555) 123-4567</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Email Address</h4>
                            <p>hello@mymelody.com</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <section class="contact-form">
                <h3><i class="fas fa-paper-plane"></i> Send Us a Message</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter your name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" placeholder="What is this regarding?" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" class="form-control" placeholder="Tell us how we can help you..." required></textarea>
                    </div>
                    
                    <button type="submit" name="contact" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
                <?php if (isset($contact_success)): ?>
                    <p style="color: green; text-align: center; margin-top: 10px;"><?php echo $contact_success; ?></p>
                <?php endif; ?>
                <?php if (isset($contact_error)): ?>
                    <p style="color: red; text-align: center; margin-top: 10px;"><?php echo $contact_error; ?></p>
                <?php endif; ?>
            </section>
        </div>
        
        <section class="subscription-section">
            <div class="subscription-header">
                <h3><i class="fas fa-crown"></i> Join mymelody Premium</h3>
                <p>Unlock unlimited music, ad-free listening, and exclusive features</p>
            </div>
            
            <form class="subscription-form" method="POST">
                <div class="subscription-options">
                    <div class="subscription-card selected">
                        <h4><i class="fas fa-star"></i> Premium Individual</h4>
                        <div class="price">$9.99/month</div>
                        <ul class="features">
                            <li><i class="fas fa-check"></i> Ad-free music listening</li>
                            <li><i class="fas fa-check"></i> Download to listen offline</li>
                            <li><i class="fas fa-check"></i> Play songs in any order</li>
                        </ul>
                        <div class="checkbox-group">
                            <input type="radio" name="subscription" id="individual" value="individual" checked>
                            <label for="individual">Select this plan</label>
                        </div>
                    </div>
                </div>
                
                <div class="subscription-details">
                    <h3>Subscription Details</h3>
                    <div class="form-group">
                        <label for="sub-email">Email Address</label>
                        <input type="email" id="sub-email" name="sub_email" class="form-control" placeholder="Your email address" required>
                    </div>
                    
                    <button type="submit" name="subscribe" class="submit-btn">
                        <i class="fas fa-crown"></i> Start Your Premium Subscription
                    </button>
                </div>
            </form>
            <?php if (isset($subscription_success)): ?>
                <p style="color: green; text-align: center; margin-top: 10px;"><?php echo $subscription_success; ?></p>
            <?php endif; ?>
        </section>
    </main>
    
    <footer>
        <div class="footer-content">
            <div class="social-icons">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-facebook"></i></a>
            </div>
            <p>&copy; 2024 mymelody. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>