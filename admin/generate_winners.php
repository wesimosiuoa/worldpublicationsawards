<?php
require_once '../includes/dbcon.inc.php';

// Check if user has admin role
session_start();
$user_role = $_SESSION['role'] ?? '';
if ($user_role !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Get all categories
$allCategoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$allCategories = $allCategoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// For each category, get top 3 nominees
$winnersByCategory = [];
foreach ($allCategories as $category) {
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
    $winnersByCategory[$category['id']] = [
        'category' => $category,
        'winners' => $winners
    ];
}

// Generate HTML content for the PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>World Publications Awards - Winners Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18pt;
            font-weight: bold;
        }
        .subtitle {
            font-size: 12pt;
            margin-top: 5px;
        }
        .category {
            margin-top: 20px;
            font-size: 14pt;
            font-weight: bold;
            background-color: #007bff;
            color: white;
            padding: 5px;
        }
        .winner {
            margin: 10px 0;
            padding: 8px;
            border-left: 3px solid #007bff;
        }
        .position {
            font-weight: bold;
            font-size: 10pt;
        }
        .name {
            font-size: 10pt;
        }
        .details {
            font-size: 9pt;
            color: #666;
        }
        @media print {
            body { 
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0;
                padding: 15mm;
            }
            .header {
                margin-bottom: 20px;
            }
        }
        @page {
            margin: 15mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">World Publications Awards - Winners Report</div>
        <div class="subtitle">Award Winners by Category</div>
        <div class="subtitle">Generated on: ' . date('Y-m-d H:i:s') . '</div>
    </div>';

foreach ($winnersByCategory as $categoryData) {
    $category = $categoryData['category'];
    $winners = $categoryData['winners'];
    
    $html .= '<div class="category">' . htmlspecialchars($category['name']) . '</div>';
    
    if (empty($winners)) {
        $html .= '<div class="winner">No nominees in this category</div>';
    } else {
        foreach ($winners as $index => $winner) {
            $position = $index + 1;
            $positionLabel = $position === 1 ? '🥇 1st Place' : ($position === 2 ? '🥈 2nd Place' : '🥉 3rd Place');
            
            $html .= '<div class="winner">';
            $html .= '<div class="position">' . $positionLabel . ' - ' . htmlspecialchars($winner['name']) . '</div>';
            $html .= '<div class="name">' . getCountryFlag($winner['country_iso_code']) . ' ' . htmlspecialchars($winner['country_name']) . '</div>';
                        
            $html .= '<div class="details">Votes: ' . number_format($winner['total_votes']) . ' | Amount Raised: $' . number_format($winner['total_amount_raised'], 2) . '</div>';
            $html .= '</div>';
        }
    }
}

$html .= '
<div style="text-align: center; margin-bottom: 20px;">
    <button onclick="window.print();" style="padding: 10px 20px; font-size: 16px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Print/Save as PDF</button>
</div>
<script>
    // Optionally trigger print dialog when page loads
    // Commenting out auto-print to let user decide
    // window.onload = function() {
    //     // Wait a bit for content to load, then show print dialog
    //     setTimeout(function() {
    //         window.print();
    //     }, 1000);
    // };
</script>
</body>
</html>';

// Output as HTML with print functionality
header('Content-Type: text/html; charset=utf-8');
echo $html;
?>