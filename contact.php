<?php
session_start();
$page_title  = 'Contact';
$active_page = 'contact';
$sent = false; $err = '';

// Handle contact form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $cname    = trim($_POST['cname']    ?? '');
    $cemail   = trim($_POST['cemail']   ?? '');
    $csubject = trim($_POST['csubject'] ?? '');
    $cmsg     = trim($_POST['cmsg']     ?? '');

    if (empty($cname) || empty($cemail) || empty($csubject) || empty($cmsg)) {
        $err = 'Please fill in all fields.';
    } elseif (!filter_var($cemail, FILTER_VALIDATE_EMAIL)) {
        $err = 'Please enter a valid email address.';
    } else {
        // Save to DB
        $db = new mysqli("localhost","root","","koppee_db");
        if (!$db->connect_error) {
            $db->set_charset("utf8mb4");
            $db->query("CREATE TABLE IF NOT EXISTS `contact_messages`(
                `msg_id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(150) NOT NULL, `email` VARCHAR(200) NOT NULL,
                `subject` VARCHAR(255) NOT NULL, `message` TEXT NOT NULL,
                `is_read` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY(`msg_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $stmt = $db->prepare("INSERT INTO contact_messages(name,email,subject,message) VALUES(?,?,?,?)");
            $stmt->bind_param("ssss", $cname, $cemail, $csubject, $cmsg);
            $stmt->execute(); $stmt->close(); $db->close();
            $sent = true;
        } else {
            $err = 'Could not save message. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php include 'includes/head.php'; ?>
<?php include 'includes/auth_styles.php'; ?>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

    <!-- Page Header -->
    <div class="container-fluid page-header mb-5 position-relative overlay-bottom">
        <div class="d-flex flex-column align-items-center justify-content-center pt-0 pt-lg-5" style="min-height: 400px">
            <h1 class="display-4 mb-3 mt-0 mt-lg-5 text-white text-uppercase">Contact</h1>
            <div class="d-inline-flex mb-lg-5">
                <p class="m-0 text-white"><a class="text-white" href="index.php">Home</a></p>
                <p class="m-0 text-white px-2">/</p>
                <p class="m-0 text-white">Contact</p>
            </div>
        </div>
    </div>

    <!-- Contact -->
    <div class="container-fluid pt-5">
        <div class="container">
            <div class="section-title">
                <h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">Contact Us</h4>
                <h1 class="display-4">Feel Free To Contact</h1>
            </div>
            <div class="row px-3 pb-2">
                <div class="col-sm-4 text-center mb-3">
                    <i class="fa fa-2x fa-map-marker-alt mb-3 text-primary"></i>
                    <h4 class="font-weight-bold">Address</h4>
                    <p>Bavdhan, Pune, Maharashtra</p>
                </div>
                <div class="col-sm-4 text-center mb-3">
                    <i class="fa fa-2x fa-phone-alt mb-3 text-primary"></i>
                    <h4 class="font-weight-bold">Phone</h4>
                    <p>+91 8855039800</p>
                </div>
                <div class="col-sm-4 text-center mb-3">
                    <i class="far fa-2x fa-envelope mb-3 text-primary"></i>
                    <h4 class="font-weight-bold">Email</h4>
                    <p>dagadepranav21@gmail.com</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 pb-5">
                    <iframe style="width:100%;height:443px;border:0;" src="https://share.google/6ruOtwkdG1cGw9eNk" allowfullscreen aria-hidden="false" tabindex="0"></iframe>
                </div>
                <div class="col-md-6 pb-5">
                    <?php if ($sent): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle mr-2"></i>Thank you! Your message has been sent. We'll get back to you soon.
                    </div>
                    <?php elseif ($err): ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle mr-2"></i><?= htmlspecialchars($err) ?>
                    </div>
                    <?php endif; ?>
                    <div class="contact-form">
                        <form method="POST" novalidate>
                            <div class="control-group">
                                <input type="text" class="form-control bg-transparent p-4" name="cname"
                                    placeholder="Your Name" required value="<?= htmlspecialchars($_POST['cname'] ?? '') ?>">
                                <p class="help-block text-danger"></p>
                            </div>
                            <div class="control-group">
                                <input type="email" class="form-control bg-transparent p-4" name="cemail"
                                    placeholder="Your Email" required value="<?= htmlspecialchars($_POST['cemail'] ?? '') ?>">
                                <p class="help-block text-danger"></p>
                            </div>
                            <div class="control-group">
                                <input type="text" class="form-control bg-transparent p-4" name="csubject"
                                    placeholder="Subject" required value="<?= htmlspecialchars($_POST['csubject'] ?? '') ?>">
                                <p class="help-block text-danger"></p>
                            </div>
                            <div class="control-group">
                                <textarea class="form-control bg-transparent py-3 px-4" rows="5" name="cmsg"
                                    placeholder="Message" required><?= htmlspecialchars($_POST['cmsg'] ?? '') ?></textarea>
                                <p class="help-block text-danger"></p>
                            </div>
                            <div>
                                <button class="btn btn-primary font-weight-bold py-3 px-5" type="submit" name="send_message">
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
