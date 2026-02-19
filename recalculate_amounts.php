<?php
/**
 * Script to recalculate total_votes and total_amount_raised for nominees
 * based on actual vote records in the votes table
 */

include 'includes/dbcon.inc.php';

try {
    // First, reset total_votes and total_amount_raised to 0
    echo "Resetting all nominee totals...\n";
    $resetStmt = $pdo->prepare("UPDATE nominees SET total_votes = 0, total_amount_raised = 0.00");
    $resetStmt->execute();
    
    // Calculate votes and amounts from the votes table
    $voteData = $pdo->query(
        "SELECT nominee_id, COUNT(*) as vote_count, SUM(amount) as total_amount
        FROM votes 
        GROUP BY nominee_id
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Recalculating totals based on " . count($voteData) . " nominees with votes...\n";
        
    // Update each nominee's totals
    $updateStmt = $pdo->prepare(
        "UPDATE nominees 
        SET total_votes = ?, 
            total_amount_raised = ? 
        WHERE id = ?
    ");
        
    foreach ($voteData as $record) {
        $nominee_id = $record['nominee_id'];
        $vote_count = $record['vote_count'];
        $total_amount = $record['total_amount'];
            
        $updateStmt->execute([$vote_count, $total_amount, $nominee_id]);
            
        echo "Nominee ID $nominee_id: $vote_count votes, \$$total_amount raised\n";
    }
    
    echo "\nRecalculation complete! All nominee totals have been updated based on actual vote records.\n";
    
} catch (PDOException $e) {
    echo "Error recalculating amounts: " . $e->getMessage() . "\n";
}
?>