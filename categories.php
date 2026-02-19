<?php
include 'includes/header.php';
include 'includes/fn.inc.php';

/*
|--------------------------------------------------------------------------
| Fetch all categories with nominees (INCLUDING iso_code)
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT 
        c.id AS category_id,
        c.name AS category_name,
        c.description AS category_description,

        n.id AS nominee_id,
        n.name AS nominee_name,
        n.total_votes,
        n.total_amount_raised,
        n.logo,
        n.description AS nominee_description,
        n.nominee_type,
        n.is_active,
        n.is_featured,

        co.name AS country_name,
        co.iso_code

    FROM categories c
    LEFT JOIN nominees n ON c.id = n.category_id AND n.is_active = 1
    LEFT JOIN countries co ON n.country_id = co.id
    ORDER BY c.name ASC, n.total_votes DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total votes for percentage calculation
$totalVotesQuery = $pdo->query("SELECT COALESCE(SUM(total_votes), 1) as total FROM nominees WHERE is_active = 1");
$totalVotes = $totalVotesQuery->fetch(PDO::FETCH_ASSOC)['total'];

/*
|--------------------------------------------------------------------------
| Group nominees under categories
|--------------------------------------------------------------------------
*/
$categories = [];

foreach ($results as $row) {
    $categoryId = $row['category_id'];

    if (!isset($categories[$categoryId])) {
        $categories[$categoryId] = [
            'id' => $categoryId,
            'name' => $row['category_name'],
            'description' => $row['category_description'],
            'nominees' => []
        ];
    }

    if (!empty($row['nominee_id'])) {
        $categories[$categoryId]['nominees'][] = [
            'id' => $row['nominee_id'],
            'name' => $row['nominee_name'],
            'country_name' => $row['country_name'],
            'iso_code' => $row['iso_code'],
            'total_votes' => $row['total_votes'],
            'total_amount_raised' => $row['total_amount_raised'],
            'logo' => $row['logo'],
            'description' => $row['nominee_description'],
            'nominee_type' => $row['nominee_type'],
            'is_featured' => $row['is_featured']
        ];
    }
}
?>

<!-- HERO -->
<section class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold">Award Categories</h1>
        <p class="lead mt-3">
            Explore nominees across all categories and cast your vote.
        </p>
    </div>
</section>

<!-- CATEGORIES -->
<section class="py-5">
    <div class="container">

        <?php foreach ($categories as $category): ?>
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-sm">

                    <div class="card-header" style="background: linear-gradient(135deg, #1a2a6c, #b21f1f, #1a2a6c); color: white;">
                        <h3 class="fw-bold mb-0">
                            <a class="text-white text-decoration-none" href="#category_<?= $category['id'] ?>" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="category_<?= $category['id'] ?>">
                                <?= htmlspecialchars($category['name']) ?>
                                <i class="fas fa-chevron-down float-end"></i>
                            </a>
                        </h3>
                    </div>

                    <div class="collapse" id="category_<?= $category['id'] ?>">
                        <div class="card-body">

                            <?php if (!empty($category['nominees'])): ?>
                                <div class="row g-4">

                                    <?php foreach ($category['nominees'] as $nominee): ?>
                                    <div class="col-md-4">
                                        <div class="card h-100 text-center border-0 shadow-sm">
                                            <div class="card-body">

                                                <h5 class="fw-bold">
                                                    <?= htmlspecialchars($nominee['name']) ?>
                                                </h5>

                                                <p class="text-muted mb-1">
                                                    <?= render_country_flag($nominee['country_id'] ?? null) ?>
                                                    <?= htmlspecialchars($nominee['country_name'] ?? 'Unknown') ?>
                                                </p>

                                                <p class="fw-semibold">
                                                    <?php 
                                                        $votePercentage = $totalVotes > 0 ? round(($nominee['total_votes'] / $totalVotes) * 100, 1) : 0;
                                                        echo $votePercentage . "% of votes<br>";
                                                        //echo "$" . number_format($nominee['total_amount_raised'], 2) . " raised";
                                                    ?>
                                                </p>
                                                
                                                <?php 
                                                    require_once 'encryption/encoder.php';
                                                    $encodedId = salted_encode($nominee['id']);?>
                                                <a href="vote.php?id=<?= $encodedId ?>"
                                                   class="btn btn-warning btn-sm">
                                                    Vote
                                                </a>

                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>

                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <p class="text-muted">
                                        No nominees available in this category.
                                    </p>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- CTA -->
        <div class="row text-center mt-5">
            <div class="col">
                <a href="nominees.php" class="btn btn-primary btn-lg me-2">
                    View All Nominees
                </a>
                <a href="start-voting.php" class="btn btn-outline-secondary btn-lg">
                    Start Voting
                </a>
            </div>
        </div>

    </div>
</section>

<?php include 'footer.php'; ?>
