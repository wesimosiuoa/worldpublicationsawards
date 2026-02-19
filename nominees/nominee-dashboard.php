
<body>

    <?php include 'header.php'; ?>
    
    <!-- Dashboard Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="mb-4">Nominee Dashboard</h2>
                
                <?php if ($nominee): ?>
                    <!-- Nominee Profile Card -->

                    <?php 
                    
                        $shareUrl = urlencode("http://localhost/worldpublicationawards/vote.php?id=".$nominee['id']);
                        
                        $shareText = urlencode("We are officially nominated for the World Publication Awards user ".$nominee_stats['category_name']."! 🏆 Vote for us now!");

                    ?>
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-user-tie me-2"></i>Your Profile
                            </h4>
                        </div>
                        <div id="wpa-poster" class="poster">
                        <div class="poster-bg"></div>

                        <div class="poster-content">

                            <!-- Top Badge -->
                            <div class="poster-badge">
                                <img src="../assets/images/ChatGPT Image Jan 9, 2026, 12_49_34 AM.png" alt="WPA">
                                <span>OFFICIAL NOMINEE</span>
                            </div>

                            <!-- Nominee Image -->
                            <div class="poster-avatar">
                                <?php if ($nominee['logo']): ?>
                                    <img src="<?= '../assets/images/' . $nominee['logo'] ?>">
                                <?php else: ?>
                                    <div class="avatar-fallback">👤</div>
                                <?php endif; ?>
                            </div>

                            <!-- Nominee Info -->
                            <h1 class="poster-name"><?= htmlspecialchars($nominee['name']) ?></h1>

                            <p class="poster-category">
                                <?= htmlspecialchars($nominee_stats['category_name'] ?? 'Category') ?>
                                • <?= htmlspecialchars($nominee_stats['country_name'] ?? 'Country') ?>
                            </p>

                            <!-- CTA -->
                            <div class="poster-cta">
                                Vote for Me
                            </div>

                            <!-- Footer -->
                            <div class="poster-footer">
                                worldpublicationawards.com
                            </div>

                        </div>
                    </div>
                    <style>
                        .poster {
                        width: 1080px;
                        height: 1080px;
                        position: relative;
                        font-family: 'Poppins', sans-serif;
                        overflow: hidden;
                        border-radius: 28px;
                    }

                    /* Background */
                    .poster-bg {
                        position: absolute;
                        inset: 0;
                        background:
                            radial-gradient(circle at top, #1c3c60, #050b14),
                            linear-gradient(135deg, #0a1a2f, #000);
                        z-index: 1;
                    }

                    /* Content */
                    .poster-content {
                        position: relative;
                        z-index: 2;
                        height: 100%;
                        padding: 80px 60px;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        color: #fff;
                    }

                    /* Badge */
                    .poster-badge {
                        display: flex;
                        align-items: center;
                        gap: 14px;
                        background: rgba(255, 215, 0, 0.15);
                        border: 1px solid gold;
                        padding: 10px 18px;
                        border-radius: 50px;
                        font-weight: 600;
                        letter-spacing: 1px;
                    }

                    .poster-badge img {
                        height: 36px;
                    }

                    /* Avatar */
                    .poster-avatar {
                        width: 260px;
                        height: 260px;
                        margin: 70px 0 40px;
                        border-radius: 50%;
                        background: linear-gradient(135deg, gold, #fff);
                        padding: 6px;
                    }

                    .poster-avatar img,
                    .avatar-fallback {
                        width: 100%;
                        height: 100%;
                        border-radius: 50%;
                        background: #fff;
                        object-fit: contain;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 64px;
                    }

                    /* Name */
                    .poster-name {
                        font-size: 52px;
                        font-weight: 800;
                        text-align: center;
                        max-width: 900px;
                        line-height: 1.2;
                    }

                    /* Category */
                    .poster-category {
                        margin-top: 18px;
                        font-size: 22px;
                        opacity: 0.85;
                    }

                    /* CTA */
                    .poster-cta {
                        margin-top: auto;
                        background: gold;
                        color: #000;
                        padding: 18px 60px;
                        border-radius: 50px;
                        font-size: 28px;
                        font-weight: 800;
                        letter-spacing: 1px;
                    }

                    /* Footer */
                    .poster-footer {
                        margin-top: 30px;
                        font-size: 14px;
                        opacity: 0.8;
                    }

                    </style>
                    <button class="btn btn-warning mt-4" onclick="downloadPoster()">
                        Download Share Poster
                    </button>
<br>
                    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

                    <script>
                    function downloadPoster() {
                        html2canvas(document.getElementById('wpa-poster'), {
                            scale: 2,
                            backgroundColor: null
                        }).then(canvas => {
                            const link = document.createElement('a');
                            link.download = 'WPA-Official-Nominee.png';
                            link.href = canvas.toDataURL('image/png');
                            link.click();
                        });
                    }
                    </script>

                        




                    <!-- Nominee Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card text-white bg-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <h3 class="card-title"><?php echo number_format($nominee_stats['vote_count'] ?? 0); ?></h3>
                                    <p class="card-text">Total Votes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                    <h3 class="card-title">$<?php echo number_format($nominee_stats['total_raised'] ?? 0, 2); ?></h3>
                                    <p class="card-text">Amount Raised</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                                    <h3 class="card-title"><?php echo number_format($nominee['total_votes'] ?? 0); ?></h3>
                                    <p class="card-text">Total Votes (DB)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <a href="nominees.php" class="btn btn-primary w-100">
                                        <i class="fas fa-list me-2"></i>View All Nominees
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="categories.php" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-tags me-2"></i>Browse Categories
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="profile.php" class="btn btn-info w-100">
                                        <i class="fas fa-edit me-2"></i>Edit Profile
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="../includes/logout.php" class="btn btn-danger w-100">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- No nominee account found -->
                    <div class="alert alert-warning">
                        <h4 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>No Nominee Account Found</h4>
                        <p>We couldn't find a nominee account associated with your email address: <?php echo htmlspecialchars($email); ?></p>
                        <p>Please contact the administrators if you believe you should have a nominee account.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>