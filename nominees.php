<?php
// Nominees Page for World Publications Awards
$page_title = 'Nominees - World Publications Awards';
include 'includes/header.php';
include 'includes/fn.inc.php';

// Fetch all nominees with category and country information
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Get total votes for percentage calculation
$totalVotesQuery = $pdo->query("SELECT COALESCE(SUM(total_votes), 1) as total FROM nominees WHERE is_active = 1");
$totalVotes = $totalVotesQuery->fetch(PDO::FETCH_ASSOC)['total'];

if ($categoryId) {
    $nomineesStmt = $pdo->prepare("SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code FROM nominees n LEFT JOIN categories c ON n.category_id = c.id LEFT JOIN countries co ON n.country_id = co.id WHERE n.is_active = 1 AND n.category_id = ? ORDER BY n.total_votes DESC");
    $nomineesStmt->execute([$categoryId]);
} else {
    $nomineesStmt = $pdo->prepare("SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code FROM nominees n LEFT JOIN categories c ON n.category_id = c.id LEFT JOIN countries co ON n.country_id = co.id WHERE n.is_active = 1 ORDER BY n.total_votes DESC");
    $nomineesStmt->execute();
}
$nominees = $nomineesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories for filtering
$categoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Nominees Hero Section -->
<section class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold">Nominees</h1>
        <p class="lead mt-3">
            Browse our distinguished nominees across all categories
        </p>
    </div>
</section>

<!-- Filter Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>Filter by Category</h5>
                <select id="categoryFilter" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <h5>Search Nominees</h5>
                <input type="text" id="searchInput" class="form-control" placeholder="Search nominees...">
            </div>
        </div>
    </div>
</section>

<!-- Nominees Content -->
<section class="py-5">
    <div class="container">
        <div class="row" id="nomineesContainer">
            <?php if (!empty($nominees)): ?>
                <?php foreach ($nominees as $nominee): ?>
                <div class="col-lg-4 col-md-6 nominee-item mb-4" data-category="<?php echo $nominee['category_id']; ?>">
                    <div class="card nominee-card h-100">
                        <?php if (!empty($nominee['logo'])): ?>
                        <img src="assets/images/<?php echo htmlspecialchars($nominee['logo']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($nominee['name']); ?>" style="height: 200px; object-fit: contain;">
                        <?php else: ?>
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                            <i class="fas fa-newspaper fa-3x text-muted"></i>
                        </div>
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($nominee['name']); ?></h5>
                            <p class="card-text text-muted">
                                <small>
                                    <?php echo render_country_flag($nominee['country_id']) . ' ' . htmlspecialchars($nominee['country_name']); ?>
                                </small>
                            </p>
                            
                            
                            <div class="mt-auto pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge text-black"><?php echo htmlspecialchars($nominee['category_name']); ?></span>
                                    <small class="text-muted">
                                        <?php 
                                            $votePercentage = $totalVotes > 0 ? round(($nominee['total_votes'] / $totalVotes) * 100, 1) : 0;
                                            echo "<i class='fas fa-vote-yea text-primary'></i> " . $votePercentage . "% of votes<br>";
                                            // echo "<i class='fas fa-dollar-sign text-success'></i> $" . number_format($nominee['total_amount_raised'], 2) . " raised";
                                        ?>
                                    </small>
                                </div>
                                <div class="mt-3">

                                <?php require_once 'encryption/encoder.php'; $encodedId = salted_encode($nominee['id']); ?>
                                    <a href="vote.php?id=<?= $encodedId ?>" class="btn btn-primary w-100">Vote Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <h4>No nominees found</h4>
                        <p class="text-muted">Check back later for new nominees</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('categoryFilter');
    const searchInput = document.getElementById('searchInput');
    const nomineeItems = document.querySelectorAll('.nominee-item');
    
    // Filter by category
    categoryFilter.addEventListener('change', function() {
        const selectedCategoryId = this.value;
        
        nomineeItems.forEach(item => {
            const itemCategoryId = item.getAttribute('data-category');
            
            if (selectedCategoryId === '' || itemCategoryId === selectedCategoryId) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        nomineeItems.forEach(item => {
            const nomineeName = item.querySelector('.card-title').textContent.toLowerCase();
            
            if (nomineeName.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>