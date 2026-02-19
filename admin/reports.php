<?php
include 'header.php'; 

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');
    exit();
}

// Check if user has admin or stakeholder role
$user_role = $_SESSION['role'] ?? '';
if ($user_role !== 'admin' && $user_role !== 'stakeholder') {
    header('Location: ../index.php');
    exit();
}

include '../includes/dbcon.inc.php';
include '../includes/messages.php';
include '../includes/helpers.php';
include 'fn.inc.php';

// Check if user has admin role
if ($user_role !== 'admin') {
    ?>
        <script> alert('You do not have permission to access this page.'); window.location.href = '../index.php'; </script>
    <?php
}

// Get statistics for dashboard
$totalNominees = $pdo->query("SELECT COUNT(*) FROM nominees")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalCountries = $pdo->query("SELECT COUNT(*) FROM countries")->fetchColumn();
$totalVotes = $pdo->query("SELECT SUM(total_votes) FROM nominees")->fetchColumn() ?: 0;
$totalAmountRaised = $pdo->query("SELECT SUM(total_amount_raised) FROM nominees")->fetchColumn() ?: 0;

// Get top nominees by votes
$topNomineesStmt = $pdo->query("
    SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code as country_iso_code 
    FROM nominees n 
    LEFT JOIN categories c ON n.category_id = c.id 
    LEFT JOIN countries co ON n.country_id = co.id 
    ORDER BY n.total_votes DESC 
    LIMIT 10
");
$topNominees = $topNomineesStmt->fetchAll(PDO::FETCH_ASSOC);

// Get nominees by category
$categoryStatsStmt = $pdo->query("
    SELECT c.name as category_name, COUNT(n.id) as nominee_count, SUM(n.total_votes) as total_votes, SUM(n.total_amount_raised) as total_raised
    FROM categories c 
    LEFT JOIN nominees n ON c.id = n.category_id 
    GROUP BY c.id, c.name 
    ORDER BY total_votes DESC
");
$categoryStats = $categoryStatsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get nominees by country
$countryStatsStmt = $pdo->query("
    SELECT co.name as country_name, COUNT(n.id) as nominee_count, SUM(n.total_votes) as total_votes, SUM(n.total_amount_raised) as total_raised
    FROM countries co 
    LEFT JOIN nominees n ON co.id = n.country_id 
    GROUP BY co.id, co.name 
    ORDER BY total_votes DESC
");
$countryStats = $countryStatsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent activity (recently added nominees)
$recentNomineesStmt = $pdo->query("
    SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code as country_iso_code 
    FROM nominees n 
    LEFT JOIN categories c ON n.category_id = c.id 
    LEFT JOIN countries co ON n.country_id = co.id 
    ORDER BY n.created_at DESC 
    LIMIT 10
");
$recentNominees = $recentNomineesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - World Publications Awards</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>
<body>

<div style="margin-top:80px;"></div>

<!-- REPORTS DASHBOARD HERO -->
<section class="bg-dark text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold">Reports Dashboard</h1>
                <p class="lead">Comprehensive analytics and reports</p>
            </div>
            
        </div>
    </div>
</section>

<!-- REPORTS DASHBOARD CONTENT -->
<section class="py-5">
    <div class="container">
        <!-- STATS CARDS -->
        <div class="row g-4 mb-5">
            <div class="col-md-2">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h3 class="fw-bold"><?php echo $totalNominees; ?></h3>
                        <p class="mb-0">Nominees</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h3 class="fw-bold"><?php echo $totalCategories; ?></h3>
                        <p class="mb-0">Categories</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h3 class="fw-bold"><?php echo $totalCountries; ?></h3>
                        <p class="mb-0">Countries</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center bg-warning text-dark">
                    <div class="card-body">
                        <h3 class="fw-bold"><?php echo number_format($totalVotes); ?></h3>
                        <p class="mb-0">Total Votes</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center bg-secondary text-white">
                    <div class="card-body">
                        <h3 class="fw-bold">$<?php echo number_format($totalAmountRaised, 2); ?></h3>
                        <p class="mb-0">Total Raised</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- REPORTS SECTIONS -->
        <div class="row">
            <div class="col-lg-6">
                <!-- TOP NOMINEES -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Top Nominees by Votes</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="exportReport('top-nominees')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Name</th>
                                        <th>Country</th>
                                        <th>Category</th>
                                        <th>Votes</th>
                                        <th>Raised</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topNominees as $index => $nominee): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($nominee['name']); ?></td>
                                        <td><?php echo render_country_flag($nominee['country_id']) . ' ' . htmlspecialchars($nominee['country_name']); ?></td>
                                        <td><?php echo htmlspecialchars($nominee['category_name']); ?></td>
                                        <td><?php echo number_format($nominee['total_votes']); ?></td>
                                        <td>$<?php echo number_format($nominee['total_amount_raised'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- RECENT ACTIVITY -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Nominees</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="exportReport('recent-nominees')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Country</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentNominees as $nominee): ?>
                                    <tr>
                                        <td><?php echo date('M j, Y', strtotime($nominee['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($nominee['name']); ?></td>
                                        <td><?php echo htmlspecialchars($nominee['category_name']); ?></td>
                                        <td><?php echo render_country_flag($nominee['country_id']) . ' ' . htmlspecialchars($nominee['country_name']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <!-- STATISTICS BY CATEGORY -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Statistics by Category</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="exportReport('category-stats')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Nominees</th>
                                        <th>Votes</th>
                                        <th>Raised</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categoryStats as $stat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($stat['category_name']); ?></td>
                                        <td><?php echo $stat['nominee_count']; ?></td>
                                        <td><?php echo number_format($stat['total_votes']); ?></td>
                                        <td>$<?php echo number_format($stat['total_raised'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- STATISTICS BY COUNTRY -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Statistics by Country</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="exportReport('country-stats')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Country</th>
                                        <th>Nominees</th>
                                        <th>Votes</th>
                                        <th>Raised</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($countryStats as $stat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($stat['country_name']); ?></td>
                                        <td><?php echo $stat['nominee_count']; ?></td>
                                        <td><?php echo number_format($stat['total_votes']); ?></td>
                                        <td>$<?php echo number_format($stat['total_raised'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- EXPORT MODAL -->
        <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalLabel">Export Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Choose the format to export the report:</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="downloadCSV()" id="csvBtn">
                                <i class="fas fa-file-csv"></i> Download CSV
                            </button>
                            <button class="btn btn-success" onclick="downloadPDF()" id="pdfBtn">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>World Publications Awards</h5>
                <p class="mb-0">Recognizing excellence in global journalism.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> World Publications Awards. All rights reserved.</p>
                <p class="mb-0">Admin Panel</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentReportType = '';

function exportReport(reportType) {
    currentReportType = reportType;
    const exportModal = new bootstrap.Modal(document.getElementById('exportModal'));
    exportModal.show();
}

function downloadCSV() {
    // Close modal first
    const exportModal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
    exportModal.hide();
    
    // In a real implementation, this would generate a CSV file
    alert('CSV export functionality would be implemented here for: ' + currentReportType);
}

function downloadPDF() {
    // Close modal first
    const exportModal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
    exportModal.hide();
    
    // In a real implementation, this would generate a PDF
    alert('PDF export functionality would be implemented here for: ' + currentReportType);
}
</script>

</body>
</html>