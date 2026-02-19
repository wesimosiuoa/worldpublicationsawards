<?php
// Category Page for World Publications Awards
include 'includes/header.php';
include 'includes/fn.inc.php';

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($category_id <= 0) {
    header('Location: index.php');
    exit();
}

// Fetch category details
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: index.php');
    exit();
}

// Get total votes for percentage calculation
$totalVotesQuery = $pdo->query("SELECT COALESCE(SUM(total_votes), 1) as total FROM nominees WHERE is_active = 1");
$totalVotes = $totalVotesQuery->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch nominees in this category
$nomineesStmt = $pdo->prepare("
    SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code 
    FROM nominees n 
    LEFT JOIN categories c ON n.category_id = c.id 
    LEFT JOIN countries co ON n.country_id = co.id 
    WHERE n.category_id = ? AND n.is_active = 1
    ORDER BY n.total_votes DESC
");
$nomineesStmt->execute([$category_id]);
$nominees = $nomineesStmt->fetchAll(PDO::FETCH_ASSOC);

// Get top 3 nominees as winners
$winnersStmt = $pdo->prepare("
    SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code 
    FROM nominees n 
    LEFT JOIN categories c ON n.category_id = c.id 
    LEFT JOIN countries co ON n.country_id = co.id 
    WHERE n.category_id = ? AND n.is_active = 1
    ORDER BY n.total_votes DESC
    LIMIT 3
");
$winnersStmt->execute([$category_id]);
$winners = $winnersStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> - World Publications Awards</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .category-header {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #1a2a6c);
            color: white;
            padding: 60px 0;
        }
        .nominee-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .nominee-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .winner-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .first-place {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #000;
        }
        .second-place {
            background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
            color: #000;
        }
        .third-place {
            background: linear-gradient(135deg, #CD7F32, #A56A2A);
            color: #fff;
        }
        .stats-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-trophy me-2"></i>World Publications Awards
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#categories">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#nominees">Nominees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Category Header -->
    <section class="category-header text-center">
        <div class="container">
            <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($category['name']); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($category['description']); ?></p>
            <div class="mt-3">
                <span class="badge bg-light text-dark"><?php echo count($nominees); ?> nominees</span>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Winners Section -->
        <?php if (!empty($winners)): ?>
        <section class="mb-5">
            <h2 class="text-center mb-4">Category Winners</h2>
            <div class="row g-4">
                <?php foreach ($winners as $index => $winner): 
                    $positionClass = $index === 0 ? 'first-place' : ($index === 1 ? 'second-place' : 'third-place');
                    $positionLabel = $index === 0 ? '1st Place 🥇' : ($index === 1 ? '2nd Place 🥈' : '3rd Place 🥉');
                ?>
                <div class="col-md-4">
                    <div class="card nominee-card position-relative">
                        <div class="winner-badge <?php echo $positionClass; ?>">
                            <?php echo $positionLabel; ?>
                        </div>
                        <?php if (!empty($winner['logo'])): ?>
                        <img src="assests/images/<?php echo htmlspecialchars($winner['logo']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($winner['name']); ?>" style="height: 200px; object-fit: contain;">
                        <?php else: ?>
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                            <i class="fas fa-newspaper fa-3x text-muted"></i>
                        </div>
                        <?php endif; ?>
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($winner['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo render_country_flag($winner['country_id']) . ' ' . htmlspecialchars($winner['country_name']); ?></p>
                            <div class="mt-3">
                                <p class="mb-1">
                                    <?php 
                                        $votePercentage = $totalVotes > 0 ? round(($winner['total_votes'] / $totalVotes) * 100, 1) : 0;
                                        echo "<i class='fas fa-vote-yea text-primary'></i> " . $votePercentage . "% of votes<br>";
                                        echo "<i class='fas fa-dollar-sign text-success'></i> $" . number_format($winner['total_amount_raised'], 2) . " raised";
                                    ?>
                                </p>
                            </div>
                            <a href="vote.php?id=<?php echo $winner['id']; ?>" class="btn btn-primary mt-2">Vote</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- All Nominees Section -->
        <section>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>All Nominees</h2>
                <div class="text-muted"><?php echo count($nominees); ?> nominees</div>
            </div>
            
            <?php if (!empty($nominees)): ?>
            <div class="row g-4">
                <?php foreach ($nominees as $nominee): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card nominee-card">
                        <?php if (!empty($nominee['logo'])): ?>
                        <img src="assests/images/<?php echo htmlspecialchars($nominee['logo']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($nominee['name']); ?>" style="height: 200px; object-fit: contain;">
                        <?php else: ?>
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                            <i class="fas fa-newspaper fa-3x text-muted"></i>
                        </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($nominee['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo render_country_flag($nominee['country_id']) . ' ' . htmlspecialchars($nominee['country_name']); ?></p>
                            <p class="card-text small"><?php echo truncateText(htmlspecialchars($nominee['description']), 100); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <?php 
                                        $votePercentage = $totalVotes > 0 ? round(($nominee['total_votes'] / $totalVotes) * 100, 1) : 0;
                                        echo "<i class='fas fa-vote-yea text-primary'></i> " . $votePercentage . "% of votes<br>";
                                        echo "<i class='fas fa-dollar-sign text-success'></i> $" . number_format($nominee['total_amount_raised'], 2) . " raised";
                                    ?>
                                </div>
                                <a href="vote.php?id=<?php echo $nominee['id']; ?>" class="btn btn-primary">Vote</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center">
                <p class="text-muted">No nominees available in this category yet.</p>
            </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
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
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>