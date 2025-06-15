<?php
// /user-management/index.php
require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/includes/header.php';

// Nhúng Search
//require_once BASE_PATH . '/search.php';
?>
<!-- Body -->
<!-- Hero Section -->
<section class="hero-section position-relative">
    <div class="container py-5">
        <div class="row align-items-center min-vh-50">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold text-dark mb-4">Find Your Perfect Smartphone</h1>
                <p class="lead text-muted mb-4">Compare features, specs, and prices across all major brands to make the best choice for your needs.</p>
                <div class="d-flex flex-column flex-sm-row gap-3">
                    <a href="#compare" class="btn btn-primary btn-lg">Shop Now</a>
                    <a href="#deals" class="btn btn-outline-secondary btn-lg">View Deals</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="bg-white py-4 shadow-sm">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-md-3 text-center">
                    <p class="text-muted small mb-1">Phones Available</p>
                    <p class="h3 fw-bold text-dark mb-0 counter" data-target="500">0+</p>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <p class="text-muted small mb-1">Brands</p>
                    <p class="h3 fw-bold text-dark mb-0 counter" data-target="25">0+</p>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <p class="text-muted small mb-1">Daily Comparisons</p>
                    <p class="h3 fw-bold text-dark mb-0 counter" data-target="2500">0+</p>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <p class="text-muted small mb-1">Happy Users</p>
                    <p class="h3 fw-bold text-dark mb-0 counter" data-target="1000000">0+</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Brand Navigation -->
<?php
    // Nhúng Brand
    include BASE_PATH . '/brand/slider.php';
?>

<!-- Featured Products Section -->
<?php
    // Nhúng Products
    include BASE_PATH . '/product/featured-products.php';
?>


<!-- Deals Section -->
<section id="deals" class="py-5 bg-light">
<div class="container px-4">
     <h2 class="h2 fw-bold text-center mb-4">Hot Deals & Promotions</h2>
</div>
</section>

<!-- Newsletter Section -->
<section class="py-5 newsletter-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="h2 fw-bold mb-4">Stay Updated</h2>
                <p class="text-muted mb-4">Subscribe to our newsletter to receive updates on the latest smartphone releases, exclusive deals, and tech news.</p>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control newsletter-input" placeholder="Your email address">
                            <button class="btn btn-primary newsletter-button" type="button">Subscribe</button>
                        </div>
                    </div>
                </div>
                
                <p class="small text-muted">We respect your privacy. Unsubscribe at any time.</p>
            </div>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section id="about" class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="h2 fw-bold text-center mb-5">About Us</h2>
                
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <p class="text-muted mb-3">Wireless World is your ultimate destination for smartphone comparison and discovery. We help millions of users make informed decisions when purchasing a new mobile device.</p>
                        <p class="text-muted mb-3">Our mission is to simplify the phone buying process by providing comprehensive, unbiased comparisons and up-to-date information on the latest devices from all major manufacturers.</p>
                        <p class="text-muted mb-4">Founded in 2020, we've grown to become one of the most trusted resources for smartphone buyers worldwide, with over 1 million monthly active users.</p>
                        
                        <div class="d-flex gap-4">
                            <a href="#" class="text-primary text-decoration-none fw-medium">Learn more about us</a>
                            <a href="#contact" class="text-primary text-decoration-none fw-medium">Contact our team</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="h3 fw-bold text-primary mb-2">500+</div>
                                    <p class="text-muted mb-0">Phones in database</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="h3 fw-bold text-primary mb-2">25+</div>
                                    <p class="text-muted mb-0">Brands covered</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="h3 fw-bold text-primary mb-2">1M+</div>
                                    <p class="text-muted mb-0">Monthly users</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="h3 fw-bold text-primary mb-2">4.8/5</div>
                                    <p class="text-muted mb-0">User satisfaction</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

 <!-- Contact Section -->
<section id="contact" class="py-5 bg-light">
    <div class="container">
        <h2 class="h2 fw-bold text-center mb-5">Contact Us</h2>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="row g-0">
                        <div class="col-lg-6 p-4 p-lg-5">
                            <h3 class="h4 fw-semibold mb-4">Get in Touch</h3>
                            <p class="text-muted mb-4">Have questions or feedback? We'd love to hear from you. Fill out the form and our team will get back to you shortly.</p>
                            
                            <form>
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-medium">Your Name</label>
                                    <input type="text" class="form-control" id="name">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-medium">Email Address</label>
                                    <input type="email" class="form-control" id="email">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject" class="form-label fw-medium">Subject</label>
                                    <input type="text" class="form-control" id="subject">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="message" class="form-label fw-medium">Message</label>
                                    <textarea class="form-control" id="message" rows="4"></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </form>
                        </div>
                        
                        <div class="col-lg-6 bg-light p-4 p-lg-5">
                            <h3 class="h4 fw-semibold mb-4">Contact Information</h3>
                            
                            <div class="d-flex align-items-start mb-4">
                                <div class="contact-icon me-3">
                                    <i class="bi-geo-alt"></i>
                                </div>
                                <div>
                                    <h4 class="fw-medium mb-1">Address</h4>
                                    <p class="text-muted mb-0">285 Doi Can, Lieu Giai Ward, Ba Dinh District, Hanoi</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-4">
                                <div class="contact-icon me-3">
                                    <i class="bi-envelope"></i>
                                </div>
                                <div>
                                    <h4 class="fw-medium mb-1">Email</h4>
                                    <p class="text-muted mb-0">wirelessworld@gmail.com</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-4">
                                <div class="contact-icon me-3">
                                    <i class="bi-telephone"></i>
                                </div>
                                <div>
                                    <h4 class="fw-medium mb-1">Phone</h4>
                                    <p class="text-muted mb-0">+84 123456789</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-4">
                                <div class="contact-icon me-3">
                                    <i class="bi-clock"></i>
                                </div>
                                <div>
                                    <h4 class="fw-medium mb-1">Working Hours</h4>
                                    <p class="text-muted mb-0">Monday - Friday: 9AM - 6PM<br>Saturday: 10AM - 4PM</p>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h4 class="fw-medium mb-3">Follow Us</h4>
                                <div class="d-flex gap-3">
                                    <a href="#" class="social-icon">
                                        <i class="bi-facebook"></i>
                                    </a>
                                    <a href="#" class="social-icon">
                                        <i class="bi-twitter-x"></i>
                                    </a>
                                    <a href="#" class="social-icon">
                                        <i class="bi-instagram"></i>
                                    </a>
                                    <a href="#" class="social-icon">
                                        <i class="bi-linkedin"></i>
                                    </a>
                                </div>
                            </div>
                            <!-- Google Map -->
                            <div class="map-container mt-4">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.92312398634!2d105.81641017471458!3d21.035761787539037!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab0d127a01e7%3A0xab069cd4eaa76ff2!2zMjg1IFAuIMSQ4buZaSBD4bqlbiwgTGnhu4V1IEdpYWksIEJhIMSQw6xuaCwgSMOgIE7hu5lpIDEwMDAwMCwgVmlldG5hbQ!5e0!3m2!1sen!2s!4v1747289103783!5m2!1sen!2s" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Bản đồ vị trí công ty" width="100%" height="300" style="border:0;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Body -->

<?php require_once BASE_PATH . '/includes/footer.php'; ?>