<?php
// Include necessary files for database connection and helpers
include 'includes/dbcon.inc.php';
include 'includes/helpers.php';

// Get the search query from the GET request
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Only proceed if search term is at least 2 characters
if (strlen($searchTerm) < 2) {
    echo json_encode([]);
    exit;
}

try {
    // Prepare the SQL query to search for nominees by name or category
    $stmt = $pdo->prepare(
        "SELECT n.name, n.id, c.name AS category, co.name AS country
        FROM nominees n
        LEFT JOIN categories c ON n.category_id = c.id
        LEFT JOIN countries co ON n.country_id = co.id
        WHERE (n.name LIKE :search OR c.name LIKE :search)
        AND n.is_active = 1
        ORDER BY n.total_votes DESC
        LIMIT 10
    ");
    
    $searchParam = '%' . $searchTerm . '%';
    $stmt->bindParam(':search', $searchParam);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the results as JSON
    echo json_encode($results);
} catch (PDOException $e) {
    // Log the error and return an empty array
    error_log("Search error: " . $e->getMessage());
    echo json_encode([]);
}
?>