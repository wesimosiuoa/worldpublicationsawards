<?php
$page_title = "Contact Us - World Publications Awards";
include 'includes/header.php';
?>

<!-- Contact Hero Section -->
<section class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold">Contact Us</h1>
        <p class="lead mt-3">
            Have questions or feedback? Reach out to us using the information below.
        </p>
    </div>
</section>

<!-- Contact Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="contact-content">
                    <h2 class="mb-4">Get in Touch</h2>
                    <p>If you have any questions about our awards, nomination process, voting procedures, or any other inquiries, please feel free to contact us using the information below.</p>
                    
                    <div class="row mt-5">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-envelope fa-2x mb-3 text-primary"></i>
                                    <h5>Email</h5>
                                    <p class="mb-0">info@worldpublicationsawards.com</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-map-marker-alt fa-2x mb-3 text-primary"></i>
                                    <h5>Address</h5>
                                    <p class="mb-0">123 Media Street<br>Johannesburg, South Africa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                    <div class="mt-5">
                        <h3 class="mb-4">Send us a Message</h3>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>