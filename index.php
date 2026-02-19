<?php
// Main Index Page for World Publications Awards
$page_title = 'World Publications Awards';
include 'includes/header.php';
include 'includes/fn.inc.php';

/*
|--------------------------------------------------------------------------
| STEP 1: Recalculate nominee statistics from votes (MUST COME FIRST)
|--------------------------------------------------------------------------
*/
$updateNomineesStmt = $pdo->prepare(
    "UPDATE nominees n
     SET 
        total_votes = (
            SELECT COUNT(*) FROM votes v WHERE v.nominee_id = n.id
        ),
        total_amount_raised = (
            SELECT COALESCE(SUM(amount), 0) FROM votes v WHERE v.nominee_id = n.id
        )"
);
$updateNomineesStmt->execute();

/*
|--------------------------------------------------------------------------
| STEP 2: Fetch Featured Nominees
|--------------------------------------------------------------------------
*/
$featuredNomineesStmt = $pdo->prepare(
    "SELECT 
        n.*, 
        c.name AS category_name, 
        co.name AS country_name,
        co.iso_code AS country_iso_code
     FROM nominees n
     LEFT JOIN categories c ON n.category_id = c.id
     LEFT JOIN countries co ON n.country_id = co.id
     WHERE n.is_featured = 1 AND n.is_active = 1
     ORDER BY n.total_votes DESC
     LIMIT 6"
);
$featuredNomineesStmt->execute();
$featuredNominees = $featuredNomineesStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| STEP 3: Fetch Top 10 Nominees by Votes
|--------------------------------------------------------------------------
*/
$topNomineesStmt = $pdo->prepare(
    "SELECT 
        n.*, 
        c.name AS category_name, 
        co.name AS country_name,
        co.iso_code AS country_iso_code
     FROM nominees n
     LEFT JOIN categories c ON n.category_id = c.id
     LEFT JOIN countries co ON n.country_id = co.id
     ORDER BY n.total_votes DESC
     LIMIT 10"
);
$topNomineesStmt->execute();
$topNominees = $topNomineesStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| STEP 4: Fetch Top 3 Voted Nominees
|--------------------------------------------------------------------------
*/
$topVotedStmt = $pdo->prepare(
    "SELECT 
        n.*, 
        c.name AS category_name, 
        co.name AS country_name,
        co.iso_code AS country_iso_code
     FROM nominees n
     LEFT JOIN categories c ON n.category_id = c.id
     LEFT JOIN countries co ON n.country_id = co.id
     ORDER BY n.total_votes DESC
     LIMIT 4"
);
$topVotedStmt->execute();
$topVoted = $topVotedStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| STEP 5: Fetch Categories
|--------------------------------------------------------------------------
*/
$categoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| STEP 6: Fetch Statistics
|--------------------------------------------------------------------------
*/
$activeNominees = $pdo->query(
    "SELECT COUNT(*) FROM nominees WHERE is_active = 1"
)->fetchColumn();

$votingStatsStmt = $pdo->query(
    "SELECT 
        COUNT(*) AS total_votes_cast,
        COUNT(DISTINCT voter_email) AS unique_voters,
        COALESCE(SUM(amount), 0) AS total_amount_raised
     FROM votes"
);
$votingStats = $votingStatsStmt->fetch(PDO::FETCH_ASSOC);

$countryCount = $pdo->query(
    "SELECT COUNT(DISTINCT country_id) FROM nominees"
)->fetchColumn();

// Calculate total votes for percentage calculations
$totalVotesForPercentages = $votingStats['total_votes_cast'] ?? 1; // Use 1 to avoid division by zero
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">World Publications Awards <br> <?php echo date('Y'); ?></h1>
        <p class="lead">Celebrating Excellence in Global Journalism</p>
        <p class="mb-4">Vote for your favorite publications and journalists</p>
        <a href="#nominees" class="btn btn-light btn-lg">View Nominees</a>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <h3><?= number_format($activeNominees) ?></h3>
                <p>Active Nominees</p>
            </div>
            <div class="col-md-3 col-6">
                <h3><?= count($categories) ?></h3>
                <p>Award Categories</p>
            </div>
            <div class="col-md-3 col-6">
                <h3><?= number_format($countryCount) ?></h3>
                <p>Countries Represented</p>
            </div>
            <div class="col-md-3 col-6">
                <h3><?= number_format($votingStats['total_votes_cast']) ?></h3>
                <p>Votes Cast</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Nominees -->
<section id="nominees" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Featured Nominees</h2>
            <p class="lead">Our outstanding nominees leading the industry</p>
        </div>
        <style>
        .nominee-logo {
                height: 120px;              /* fixed visual area */
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            .nominee-logo img {
                max-height: 100%;
                max-width: 100%;
                object-fit: contain;        /* keeps full image visible */
            }

        </style>
        <?php if (!empty($featuredNominees)): ?>
            <div class="row g-4">
                <?php foreach ($featuredNominees as $nominee): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card nominee-card h-100">
                            <div class="card-body">
                                <h5><?= htmlspecialchars($nominee['name']) ?></h5>
                                <p class="text-muted">
                                    
                                    <?= render_country_flag($nominee['country_id']) ?>
                                    <?= htmlspecialchars($nominee['country_name']) ?>
                                </p>
                                <p class="nominee-logo">
                                    <img src="assets/images/<?= $nominee['logo']?>" 
                                        alt="<?= htmlspecialchars($nominee['name']) ?>">
                                </p>                                <div class="d-flex justify-content-between align-items-center">
                                    <?php 
                                        $votePercentage = $totalVotesForPercentages > 0 ? round(($nominee['total_votes'] / $totalVotesForPercentages) * 100, 1) : 0;
                                        echo "<span>" . $votePercentage . "% of votes<br>";
                                    ?>

                                    <?php 
                                        require_once 'encryption/encoder.php';
                                        $encodedId = salted_encode($nominee['id']);
                                    ?>
                                    <a href="vote.php?id=<?= $encodedId ?>" class="btn btn-primary btn-sm">Vote</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">No featured nominees available.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Top Voted Nominees -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Top Voted Nominees</h2>
            <p class="lead">Most popular nominees based on votes</p>
        </div>

        <?php if (!empty($topVoted)): ?>
            <div class="row g-4">
                <?php foreach ($topVoted as $index => $nominee): ?>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body d-flex">
                                <span class="badge bg-warning me-3 fs-5"><?= $index + 1 ?></span>
                                 <div>
                                    <h5><?= htmlspecialchars($nominee['name']) ?> </h5>
                                    <p class="text-muted">
                                        <?= render_country_flag($nominee['country_id']) ?>
                                        <?= htmlspecialchars($nominee['country_name']) ?>
                                    </p>
                                    <p class="nominee-logo">
                                    <img src="assets/images/<?= $nominee['logo']?>" 
                                        alt="<?= htmlspecialchars($nominee['name']) ?>">
                                </p>
                                    <?php 
                                                                            $votePercentage = $totalVotesForPercentages > 0 ? round(($nominee['total_votes'] / $totalVotesForPercentages) * 100, 1) : 0;
                                                                            echo $votePercentage . "% of votes<br>";
                                                                        ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">No nominees available.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Categories -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Award Categories</h2>
        </div>

        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <div class="col-md-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($category['name']) ?> </h5>
                            <p class="nominee-logo">
                                    <img src="assets/images/<?= $nominee['logo']?>" 
                                        alt="<?= htmlspecialchars($nominee['name']) ?>">
                                </p>


                            <?php
                                require_once 'encryption/encoder.php';
                                $encodedId = salted_encode($category['id']);
                            ?>
                            <a href="categories.php?id=<?= $encodedId ?>" class="btn btn-outline-primary">View Nominees</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
