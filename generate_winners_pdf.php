

<?php
// Generate Winners PDF for World Publications Awards
include 'includes/dbcon.inc.php';
include 'includes/helpers.php';
include 'includes/fn.inc.php';

// Get all categories
$allCategoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$allCategories = $allCategoriesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>World Publications Awards - Winners</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: white;
            color: black;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1a2a6c;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        .category-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .category-title {
            background-color: #1a2a6c;
            color: white;
            padding: 10px;
            margin: 0 0 15px 0;
            border-radius: 5px;
        }
        .winner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .position {
            font-weight: bold;
            font-size: 1.1em;
        }
        .first-place { background-color: #fff8e1; }
        .second-place { background-color: #f5f5f5; }
        .third-place { background-color: #e0f2f1; }
        .name {
            flex-grow: 1;
            margin: 0 15px;
        }
        .details {
            text-align: right;
            font-size: 0.9em;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            color: #666;
            font-size: 0.9em;
        }
        @media print {
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>World Publications Awards</h1>
        <p>Official Winners Report</p>
        <p>Date: <?php echo date('F j, Y'); ?></p>
    </div>

    <?php foreach ($allCategories as $category): ?>
        <?php
        // Get top 3 nominees in this category by votes
        $winnersStmt = $pdo->prepare(
            "SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code as country_iso_code 
            FROM nominees n 
            LEFT JOIN categories c ON n.category_id = c.id 
            LEFT JOIN countries co ON n.country_id = co.id 
            WHERE n.category_id = ? AND n.is_active = 1
            ORDER BY n.total_votes DESC
            LIMIT 3"
        );
        $winnersStmt->execute([$category['id']]);
        $winners = $winnersStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        
        <div class="category-section">
            <h2 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h2>
            
            <?php if (empty($winners)): ?>
                <p>No nominees in this category</p>
            <?php else: ?>
                <?php foreach ($winners as $index => $winner): 
                    $position = $index + 1;
                    $positionClass = $position === 1 ? 'first-place' : ($position === 2 ? 'second-place' : 'third-place');
                    $positionLabel = $position === 1 ? '🥇 1st Place' : ($position === 2 ? '🥈 2nd Place' : '🥉 3rd Place');
                ?>
                    <div class="winner <?php echo $positionClass; ?>">
                        <div class="position"><?php echo $positionLabel; ?> - <?php echo htmlspecialchars($winner['name']); ?></div>
                        <div class="name"><?php echo render_country_flag($winner['country_id']) . ' ' . htmlspecialchars($winner['country_name']); ?></div>
                        <div class="details">
                            Votes: <?php echo number_format($winner['total_votes']); ?><br>
                            Amount Raised: $<?php echo number_format($winner['total_amount_raised'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div class="footer">
        <p>Generated on: <?php echo date('F j, Y \a\t g:i A'); ?></p>
        <p>World Publications Awards - Celebrating Excellence in Global Journalism</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #1a2a6c; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Print/Save as PDF
        </button>
    </div>
</body>
</html>