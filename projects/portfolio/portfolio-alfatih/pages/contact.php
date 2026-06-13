
<?php
$pageTitle = "Contact";
require_once '../config/koneksi.php';
require_once '../templates/header.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);
            $stmt->execute();
            
            setAlert('success', 'Your message has been sent successfully. I will get back to you soon!');
            header('Location: ' . BASE_URL . '/pages/contact.php');
            exit;
        } catch (PDOException $e) {
            setAlert('error', 'An error occurred. Please try again later.');
        }
    } else {
        setAlert('error', implode('<br>', $errors));
    }
}
?>

<!-- Contact Section -->
<section class="contact section-padding" id="contact">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h1>Contact Me</h1>
            <p>Get in touch for collaboration or inquiries</p>
        </div>
        
        <div class="contact-content">
            <!-- Contact Info -->
            <div class="contact-info" data-aos="fade-right">
                <div class="info-item">
                    <div class="icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="text">
                        <h3>Email</h3>
                        <p><a href="mailto:<?= OWNER_EMAIL ?>"><?= OWNER_EMAIL ?></a></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="text">
                        <h3>WhatsApp</h3>
                        <p><a href="https://wa.me/<?= str_replace(['+', ' '], '', OWNER_WHATSAPP) ?>"><?= OWNER_WHATSAPP ?></a></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="icon">
                        <i class="fab fa-github"></i>
                    </div>
                    <div class="text">
                        <h3>GitHub</h3>
                        <p><a href="<?= OWNER_GITHUB ?>" target="_blank"><?= str_replace('https://github.com/', '@', OWNER_GITHUB) ?></a></p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="text">
                        <h3>Location</h3>
                        <p>Lumajang, East Java, Indonesia</p>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="contact-form" data-aos="fade-left">
                <form action="<?= BASE_URL ?>/pages/contact.php" method="POST" id="contactForm">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Your name" value="<?= $_POST['name'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Your email address" value="<?= $_POST['email'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="Subject of your message" value="<?= $_POST['subject'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Your message" rows="5" required><?= $_POST['message'] ?? '' ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once '../templates/footer.php'; ?>