<?php
// How It Works Page for World Publications Awards
$page_title = 'How It Works - World Publications Awards';
include 'includes/header.php';
?>

<!-- How It Works Hero Section -->
<section class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold">How It Works</h1>
        <p class="lead mt-3">
            Understanding the World Publications Awards Process
        </p>
    </div>
</section>

<!-- How It Works Steps -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-list fa-2x text-primary"></i>
                        </div>
                        <h4 class="card-title">Nomination</h4>
                        <p class="card-text">Publications and journalists are nominated across various categories by industry experts and peers. Nominations are carefully reviewed to ensure they meet the standards for recognition.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-vote-yea fa-2x text-success"></i>
                        </div>
                        <h4 class="card-title">Voting</h4>
                        <p class="card-text">The public votes for their favorite nominees during the designated voting period. Each vote contributes to the final outcome, making the awards truly representative of public appreciation.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-trophy fa-2x text-warning"></i>
                        </div>
                        <h4 class="card-title">Recognition</h4>
                        <p class="card-text">Winners are announced and celebrated for their outstanding contributions to journalism. Awards are presented in a ceremony honoring excellence in global media.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-light p-4">
                    <h3 class="text-center mb-4">Eligibility Criteria</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>For Publications:</h5>
                            <ul>
                                <li>Must be actively publishing content</li>
                                <li>Content must meet journalistic standards</li>
                                <li>Must demonstrate impact and reach</li>
                                <li>Commitment to ethical reporting</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>For Journalists:</h5>
                            <ul>
                                <li>Must have published significant work in the past year</li>
                                <li>Demonstrate excellence in reporting</li>
                                <li>Contribute to public discourse</li>
                                <li>Adhere to professional ethics</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>