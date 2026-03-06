<?php 
    include 'includes/header.php';
?>
<!-- FAQ Section - World Publications Awards -->

<style>

    .faq-section {
    padding: 60px 20px;
    background-color: #f9f9f9;
}

.page-title {
    text-align: center;
    font-size: 32px;
    font-weight: 600;
    margin-bottom: 10px;
}

.page-subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 40px;
}

.faq-container {
    max-width: 900px;
    margin: 0 auto;
}

.faq-item {
    border-bottom: 1px solid #ddd;
}

.faq-question {
    width: 100%;
    background: none;
    border: none;
    text-align: left;
    padding: 18px 0;
    font-size: 18px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.faq-question:hover {
    color: #0056b3;
}

.faq-icon {
    font-size: 20px;
    transition: transform 0.3s ease;
}

.faq-answer {
    display: none;
    padding-bottom: 20px;
    color: #444;
}

.faq-answer p,
.faq-answer ul {
    margin: 10px 0;
}

.faq-answer ul {
    padding-left: 20px;
}
</style>
<section class="faq-section">
    <div class="container">
        <h1 class="page-title">Frequently Asked Questions</h1>
        <p class="page-subtitle">
            Find answers to common questions about the World Publications Awards (WPA).
        </p>

        <div class="faq-container">

            <!-- FAQ Item 1 -->
            <div class="faq-item">
                <button class="faq-question">
                    What are the World Publications Awards (WPA)?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>
                        The World Publications Awards (WPA) are global digital awards recognizing 
                        outstanding publications and journalists from around the world. The awards 
                        celebrate excellence in journalism, innovation, audience impact, and digital publishing.
                    </p>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="faq-item">
                <button class="faq-question">
                    How are nominees selected?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>
                        Nominees are selected based on editorial excellence, impact, audience reach, 
                        innovation, and contribution to journalism. Each of the 9 award categories 
                        consists of 6 nominees from different regions around the world to ensure global representation.
                    </p>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="faq-item">
                <button class="faq-question">
                    Who can vote?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>
                        Anyone from anywhere in the world can vote. The WPA is a global digital 
                        awards platform, and voting is open internationally during the official voting period.
                    </p>
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="faq-item">
                <button class="faq-question">
                    How much does voting cost?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>Each vote costs <strong>$3 USD</strong>.</p>
                    <ul>
                        <li>$1.50 goes directly to the nominated journalist or publication.</li>
                        <li>The remaining amount covers applicable taxes, payment processing fees, and operational costs.</li>
                    </ul>
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="faq-item">
                <button class="faq-question">
                    Why is there a voting fee?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <ul>
                        <li>Direct financial support for nominees.</li>
                        <li>Sustainable operation of the awards platform.</li>
                        <li>Prevention of spam or automated voting.</li>
                        <li>Maintenance of a transparent and secure voting system.</li>
                    </ul>
                </div>
            </div>

            <!-- FAQ Item 6 -->
            <div class="faq-item">
                <button class="faq-question">
                    When does voting open and close?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>
                        Voting opens on <strong>1 April</strong> and closes on 
                        <strong>31 August</strong>. Votes submitted outside this period will not be counted.
                    </p>
                </div>
            </div>

            <!-- FAQ Item 7 -->
            <div class="faq-item">
                <button class="faq-question">
                    Will there be an awards ceremony?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>
                        There will be no physical or virtual awards ceremony. Official awards 
                        will be delivered directly to the winners after results are finalized and verified.
                    </p>
                </div>
            </div>

            <!-- FAQ Item 8 -->
            <div class="faq-item">
                <button class="faq-question">
                    How are winners determined?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-answer">
                    <p>
                        Winners are determined solely based on the total number of valid votes 
                        received during the official voting period. Once voting closes, results 
                        are audited and verified before winners are officially announced.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
document.querySelectorAll('.faq-question').forEach(button => {
    button.addEventListener('click', () => {
        const answer = button.nextElementSibling;
        const icon = button.querySelector('.faq-icon');

        if (answer.style.display === 'block') {
            answer.style.display = 'none';
            icon.textContent = '+';
        } else {
            answer.style.display = 'block';
            icon.textContent = '-';
        }
    });
});
</script>
<?php include 'footer.php'?>