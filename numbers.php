<?php
// Numbers Page for World Publications Awards
$page_title = 'Numbers - World Publications Awards';
include 'includes/header.php';
include 'includes/fn.inc.php';

// Fetch statistics for display
$totalNomineesStmt = $pdo->query("SELECT COUNT(*) FROM nominees WHERE is_active = 1");
$totalNominees = $totalNomineesStmt->fetchColumn();

$totalCategoriesStmt = $pdo->query("SELECT COUNT(*) FROM categories");
$totalCategories = $totalCategoriesStmt->fetchColumn();

$totalCountriesStmt = $pdo->query("SELECT COUNT(DISTINCT country_id) FROM nominees");
$totalCountries = $totalCountriesStmt->fetchColumn();

$totalVotesStmt = $pdo->query("SELECT COALESCE(SUM(total_votes), 0) FROM nominees");
$totalVotes = $totalVotesStmt->fetchColumn();

// Fetch top nominees by votes
$topNomineesStmt = $pdo->prepare("SELECT n.*, c.name as category_name, co.name as country_name, co.id as country_id, co.iso_code as country_iso_code FROM nominees n LEFT JOIN categories c ON n.category_id = c.id LEFT JOIN countries co ON n.country_id = co.id WHERE n.is_active = 1 ORDER BY n.total_votes DESC LIMIT 10");
$topNominees = $topNomineesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch nominees by category
$categoryStatsStmt = $pdo->prepare("SELECT c.id, c.name, COUNT(n.id) as nominee_count FROM categories c LEFT JOIN nominees n ON c.id = n.category_id WHERE n.is_active = 1 GROUP BY c.id, c.name ORDER BY nominee_count DESC");
$categoryStats = $categoryStatsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch nominees by country
$countryStatsStmt = $pdo->prepare("SELECT co.id, co.name, COUNT(n.id) as nominee_count FROM countries co LEFT JOIN nominees n ON co.id = n.country_id WHERE n.is_active = 1 GROUP BY co.id, co.name ORDER BY nominee_count DESC LIMIT 10");
$countryStats = $countryStatsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total amount raised
$totalRaisedStmt = $pdo->query("SELECT COALESCE(SUM(total_amount_raised), 0) FROM nominees");
$totalRaised = $totalRaisedStmt->fetchColumn();

// Fetch unique voters
$uniqueVotersStmt = $pdo->query("SELECT COUNT(DISTINCT voter_email) FROM votes");
$uniqueVoters = $uniqueVotersStmt->fetchColumn();

// Fetch average votes per nominee
$avgVotesStmt = $pdo->query("SELECT AVG(total_votes) FROM nominees WHERE is_active = 1");
$avgVotes = $avgVotesStmt->fetchColumn();

// Fetch most voted nominee
$mostVotedStmt = $pdo->prepare("SELECT n.name, n.total_votes, c.name as category_name, co.name as country_name, co.id as country_id FROM nominees n LEFT JOIN categories c ON n.category_id = c.id LEFT JOIN countries co ON n.country_id = co.id WHERE n.is_active = 1 ORDER BY n.total_votes DESC LIMIT 1");
$mostVoted = $mostVotedStmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Numbers Hero Section -->
<section class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold">Numbers & Statistics</h1>
        <p class="lead mt-3">
            Insightful data about World Publications Awards
        </p>
    </div>
</section>

<!-- Stats Overview -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card text-center stats-box">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                        <h3 class="fw-bold"><?php echo number_format($totalNominees); ?></h3>
                        <p class="text-muted mb-0">Active Nominees</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center stats-box">
                    <div class="card-body">
                        <i class="fas fa-tasks fa-2x text-success mb-2"></i>
                        <h3 class="fw-bold"><?php echo number_format($totalCategories); ?></h3>
                        <p class="text-muted mb-0">Award Categories</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center stats-box">
                    <div class="card-body">
                        <i class="fas fa-globe-americas fa-2x text-info mb-2"></i>
                        <h3 class="fw-bold"><?php echo number_format($totalCountries); ?></h3>
                        <p class="text-muted mb-0">Countries</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card text-center stats-box">
                    <div class="card-body">
                        <i class="fas fa-vote-yea fa-2x text-warning mb-2"></i>
                        <h3 class="fw-bold"><?php echo number_format($totalVotes); ?></h3>
                        <p class="text-muted mb-0">Total Votes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Detailed Statistics -->
<?php
// Fetch all nominees for the list
$allNomineesStmt = $pdo->prepare("SELECT n.*, c.name as category_name, co.name as country_name, co.id as country_id FROM nominees n LEFT JOIN categories c ON n.category_id = c.id LEFT JOIN countries co ON n.country_id = co.id WHERE n.is_active = 1 ORDER BY n.name ASC");
$allNominees = $allNomineesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="py-5">
    <div class="container">
        <!-- Additional Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card text-center bg-light h-100">
                    <div class="card-body">
                        <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                        <h4 class="fw-bold text-success">$<?php echo number_format($totalRaised, 2); ?></h4>
                        <p class="text-muted mb-0">Total Raised</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-center bg-light h-100">
                    <div class="card-body">
                        <i class="fas fa-user-friends fa-2x text-primary mb-2"></i>
                        <h4 class="fw-bold text-primary"><?php echo number_format($uniqueVoters); ?></h4>
                        <p class="text-muted mb-0">Unique Voters</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-center bg-light h-100">
                    <div class="card-body">
                        <i class="fas fa-calculator fa-2x text-info mb-2"></i>
                        <h4 class="fw-bold text-info"><?php echo number_format($avgVotes); ?></h4>
                        <p class="text-muted mb-0">Avg. Votes/Nominee</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-center bg-light h-100">
                    <div class="card-body">
                        <i class="fas fa-crown fa-2x text-warning mb-2"></i>
                        <h4 class="fw-bold text-warning"><?php echo $mostVoted ? number_format($mostVoted['total_votes']) : '0'; ?></h4>
                        <p class="text-muted mb-0"><?php echo $mostVoted ? htmlspecialchars($mostVoted['name']) : 'No data'; ?></p>
                        <?php if ($mostVoted): ?>
                        <small class="text-muted"><?php echo htmlspecialchars($mostVoted['category_name']); ?><br><?php echo render_country_flag($mostVoted['country_id']) . ' ' . htmlspecialchars($mostVoted['country_name']); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        
        
    </div>
</section>

<?php include 'footer.php'; ?>