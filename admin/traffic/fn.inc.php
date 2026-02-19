<?php 



//trafic dashbooard 
function getParticipatingCountries() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT voter_country) AS participating_countries FROM votes");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['participating_countries'];
}

function getTotalVotes() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_votes FROM votes");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_votes'];
}
function getTotalRevenue() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(amount) AS total_revenue FROM votes");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_revenue'] ?? 0;
}
function getTotalUsers() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_users FROM users");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_users'];
}
function getTotalEarnings() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(amount) AS total_earnings FROM earnings");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_earnings'] ?? 0;
}
function getVotesByCountry() {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT voter_country, COUNT(*) AS total_votes
        FROM votes
        WHERE voter_country IS NOT NULL
        GROUP BY voter_country
        ORDER BY total_votes DESC
        LIMIT 10
    ");
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getTopVotingCountries($limit = 5) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT voter_country, COUNT(*) AS total_votes
        FROM votes
        WHERE voter_country IS NOT NULL
        GROUP BY voter_country
        ORDER BY total_votes DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getIpCountryAnomalies() {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT voter_ip, COUNT(DISTINCT voter_country) AS country_count
        FROM votes
        WHERE voter_ip IS NOT NULL AND voter_country IS NOT NULL
        GROUP BY voter_ip
        HAVING country_count > 1
        ORDER BY country_count DESC
    ");
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// High-frequency IP voting
function getHighFrequencyIps($threshold = 10) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT voter_ip, COUNT(*) AS vote_count
        FROM votes
        WHERE voter_ip IS NOT NULL
        GROUP BY voter_ip
        HAVING vote_count >= ?
        ORDER BY vote_count DESC
    ");
    $stmt->bindValue(1, (int)$threshold, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Country vote spikes (daily)
function getCountryVoteSpikes() {
    global $pdo;

    $stmt = $pdo->prepare("
         SELECT 
            d.voter_country,
            d.vote_date,
            d.daily_votes
        FROM (
            SELECT 
                voter_country,
                DATE(created_at) AS vote_date,
                COUNT(*) AS daily_votes
            FROM votes
            WHERE voter_country IS NOT NULL
            GROUP BY voter_country, DATE(created_at)
        ) d
        JOIN (
            SELECT 
                voter_country,
                AVG(daily_votes) AS avg_votes
            FROM (
                SELECT 
                    voter_country,
                    DATE(created_at) AS vote_date,
                    COUNT(*) AS daily_votes
                FROM votes
                WHERE voter_country IS NOT NULL
                GROUP BY voter_country, DATE(created_at)
            ) x
            GROUP BY voter_country
        ) a
        ON d.voter_country = a.voter_country
        WHERE d.daily_votes > a.avg_votes * 2
        ORDER BY d.daily_votes DESC
    ");
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>