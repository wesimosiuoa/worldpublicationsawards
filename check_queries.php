<?php
include 'includes/dbcon.inc.php';

echo "<h2>Checking Queries That Power the Index Page:</h2>";

// Check featured nominees query
echo "<h3>Featured Nominees Query:</h3>";
$featuredCheck = $pdo->prepare("SELECT n.*, c.name as category_name, co.name as country_name FROM nominees n LEFT JOIN categories c ON n.category_id = c.id LEFT JOIN countries co ON n.country_id = co.id WHERE n.is_featured = 1 ORDER BY n.total_votes DESC, n.id ASC LIMIT 6");
$featuredCheck->execute();
$featuredNomineesResult = $featuredCheck->fetchAll(PDO::FETCH_ASSOC);

echo "<p>Number of featured nominees returned: " . count($featuredNomineesResult) . "</p>";
foreach($featuredNomineesResult as $nom) {
    echo "<p>Nominee: {$nom['name']}, Votes: {$nom['total_votes']}, Featured: {$nom['is_featured']}</p>";
}

// Check top voted nominees query
echo "<h3>Top Voted Nominees Query:</h3>";
$topVotedCheck = $pdo->prepare("SELECT n.*, c.name as category_name, co.name as country_name FROM nominees n LEFT JOIN categories c ON n.category_id = c.id LEFT JOIN countries co ON n.country_id = co.id ORDER BY n.total_votes DESC, n.id ASC LIMIT 3");
$topVotedCheck->execute();
$topVotedResult = $topVotedCheck->fetchAll(PDO::FETCH_ASSOC);

echo "<p>Number of top voted nominees returned: " . count($topVotedResult) . "</p>";
foreach($topVotedResult as $nom) {
    echo "<p>Nominee: {$nom['name']}, Votes: {$nom['total_votes']}, Featured: {$nom['is_featured']}</p>";
}

// Check if votes table has data
echo "<h3>Votes Table Status:</h3>";
$voteCount = $pdo->query("SELECT COUNT(*) FROM votes")->fetchColumn();
echo "<p>Total votes in votes table: $voteCount</p>";

$voteSummary = $pdo->query("SELECT nominee_id, COUNT(*) as vote_count, SUM(amount) as total_amount FROM votes GROUP BY nominee_id ORDER BY vote_count DESC")->fetchAll();
echo "<p>Votes by nominee:</p>";
foreach($voteSummary as $vs) {
    echo "<p>Nominee ID: {$vs['nominee_id']}, Votes: {$vs['vote_count']}, Amount: \${$vs['total_amount']}</p>";
}

// Check nominee table after recalculation
echo "<h3>Current Nominee Table Status:</h3>";
$nomineeStatus = $pdo->query("SELECT id, name, is_featured, total_votes, total_amount_raised FROM nominees ORDER BY total_votes DESC, id ASC LIMIT 10")->fetchAll();
foreach($nomineeStatus as $nom) {
    echo "<p>ID: {$nom['id']}, Name: {$nom['name']}, Featured: {$nom['is_featured']}, Votes: {$nom['total_votes']}, Amount: \${$nom['total_amount_raised']}</p>";
}
?>