<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">

<style>
.cookie-banner {
    display: none; /* Hidden by default */
    position: fixed;
    bottom: 0;
    width: 100%;
    background: #0f172a;
    color: #fff;
    padding: 18px 0;
    z-index: 9999;
    border-top: 3px solid #facc15;
}

/* Ensure buttons stay inline */
.cookie-buttons {
    display: flex;
    flex-wrap: nowrap;
}

/* On very small screens, allow wrap but still inline */
@media (max-width: 576px) {
    .cookie-buttons {
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>

<script>
function setCookie(name, value) {
    document.cookie = name + "=" + value + "; path=/; SameSite=Lax";
}
function getCookie(name) {
    let nameEQ = name + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
    }
    return null;
}



function logConsent(status) {

    console.log("Sending consent:", status);

    fetch("includes/log_cookie_consent.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "consent=" + encodeURIComponent(status)
    })
    .then(res => res.text())
    .then(data => console.log("Consent logged:", data))
    .catch(err => console.error("Consent log failed:", err));
}

function acceptCookies() {
    setCookie("wpa_cookie_consent", "accepted");
    logConsent("accepted");

    document.getElementById("cookie-banner").style.display = "none";

    loadAnalytics();
}

function rejectCookies() {
    setCookie("wpa_cookie_consent", "rejected");
    logConsent("rejected");

    document.getElementById("cookie-banner").style.display = "none";
}

function loadAnalytics() {
    // Load analytics only if accepted
    console.log("Analytics enabled");
}

document.addEventListener("DOMContentLoaded", function() {

    let consent = getCookie("wpa_cookie_consent");

    if (!consent) {
        document.getElementById("cookie-banner").style.display = "block";
    }

    if (consent === "accepted") {
        loadAnalytics();
    }

});
</script>

<!-- Cookie Banner -->
<div id="cookie-banner" class="cookie-banner shadow">

    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
        
        <div class="cookie-text mb-3 mb-md-0 text-center text-md-start">
            We use cookies to ensure secure voting, prevent fraud, and improve performance.
            By clicking “Accept”, you agree to our Cookies Policy.
        </div>

        <div class="cookie-buttons d-flex align-items-center gap-2">

            <button class="btn btn-outline-light btn-xs" onclick="rejectCookies()">
                <i class="bi bi-x-circle me-1"></i>
                Reject
            </button>

            <button class="btn btn-warning btn-xs" onclick="acceptCookies()">
                <i class="bi bi-check-circle me-1"></i>
                Accept
            </button>

        </div>

    </div>

</div>

<br>

<div class="container">

    <div class="row">

        <div class="col-md-6">
            <h5>World Publications Awards</h5>
            <p class="mb-0">Recognizing excellence in global journalism.</p>
        </div>

        <div class="col-md-6 text-md-end">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> World Publications Awards. All rights reserved.</p>
            <p class="mb-0">Supporting quality media worldwide</p>
        </div>

    </div>

    <div class="row mt-4">

        <div class="col-md-12">
            <div class="footer-links">

                <a href="terms.php" class="text-white me-3">Terms of Use</a>
                <a href="privacy.php" class="text-white me-3">Privacy Policy</a>
                <a href="cookies.php" class="text-white me-3">Cookies Policy</a>
                <a href="contact.php" class="text-white me-3">Contact Us</a>
                <a href="blog.php" class="text-white me-3">Blog</a>
                <a href="faq.php" class="text-white me-3">FAQ</a>

            </div>
        </div>

    </div>

    <div class="row mt-2">

        <div class="col-md-12 text-center">

            <div class="social-links">

                <a href="https://www.facebook.com/profile.php?id=61561106677127" class="text-white me-3" target="_blank">
                    <i class="fab fa-facebook-f"></i>
                    <span class="visually-hidden">Facebook</span>
                </a>

                <a href="https://www.twitter.com/worldpubawards" class="text-white" target="_blank">
                    <i class="fab fa-x-twitter"></i>
                    <span class="visually-hidden">X</span>
                </a>

            </div>

        </div>

    </div>

</div>

</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>