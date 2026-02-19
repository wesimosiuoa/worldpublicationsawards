<?php
// Start Voting Page for World Publications Awards
$page_title = 'Start Voting - World Publications Awards';
include 'includes/header.php';
include 'includes/fn.inc.php';

// Fetch all nominees with category and country information
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Build query based on search/filter
$sql = "SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code 
        FROM nominees n 
        LEFT JOIN categories c ON n.category_id = c.id 
        LEFT JOIN countries co ON n.country_id = co.id 
        WHERE n.is_active = 1";

$params = [];

if (!empty($search)) {
    $sql .= " AND (n.name LIKE ? OR n.description LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($category_filter > 0) {
    $sql .= " AND n.category_id = ?";
    $params[] = $category_filter;
}

$sql .= " ORDER BY n.total_votes DESC";

$nomineesStmt = $pdo->prepare($sql);
$nomineesStmt->execute($params);
$nominees = $nomineesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories for the filter dropdown
$categoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Get total votes for percentage calculation
$totalVotesQuery = $pdo->query("SELECT COALESCE(SUM(total_votes), 1) as total FROM nominees WHERE is_active = 1");
$totalVotes = $totalVotesQuery->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Voting - World Publications Awards</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body>
    <!-- Hero Section -->
    <section class="bg-dark text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold">Start Voting</h1>
                    <p class="lead">Select a nominee to vote for or search for specific nominees</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="index.php" class="btn btn-outline-light">Back to Home</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Search and Filter Section -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search nominees..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="" class="d-flex">
                        <select class="form-select me-2" name="category" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($search) || $category_filter > 0): ?>
                            <a href="start-voting.php" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Nominees Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <?php if (!empty($nominees)): ?>
                    <?php foreach ($nominees as $nominee): 
                        $votePercentage = $totalVotes > 0 ? round(($nominee['total_votes'] / $totalVotes) * 100, 1) : 0;
                    ?>
                        <div class="col-lg-4 col-md-6 mb-4">
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
                                            <?= render_country_flag($nominee['country_id']) ?> <?= htmlspecialchars($nominee['country_name']); ?>
                                        </small>
                                    </p>
                                    
                                    
                                    <div class="mt-auto pt-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge text-black"><?php echo htmlspecialchars($nominee['category_name']); ?></span>
                                            <small class="text-muted">
                                                <?php echo $votePercentage; ?>% of votes
                                            </small>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <?php
                                            require_once 'encryption/encoder.php';
                                                    $encodedId = salted_encode($nominee['id']);?>
                                              
                                            <a href="vote.php?id=<?php echo $encodedId; ?>" class="btn btn-primary w-100">
                                                <i class="fas fa-vote-yea me-2"></i>Vote Now
                                            </a>
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
                            <p class="text-muted">Try adjusting your search or filter criteria</p>
                            <a href="start-voting.php" class="btn btn-primary">Clear Search</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($search) || $category_filter > 0): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <p class="text-center text-muted">
                            Showing <?php echo count($nominees); ?> of <?php echo count($nominees); ?> nominees
                            <?php if (!empty($search)): ?>
                                for "<?php echo htmlspecialchars($search); ?>"
                            <?php endif; ?>
                            <?php if ($category_filter > 0): ?>
                                in <?php echo htmlspecialchars($categories[array_search($category_filter, array_column($categories, 'id'))]['name'] ?? ''); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>