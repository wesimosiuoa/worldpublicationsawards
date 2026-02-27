<?php
// Voting Page for World Publications Awards
include 'includes/dbcon.inc.php';
include 'includes/helpers.php';
include 'includes/fn.inc.php';
require_once 'geolocation/GeoLocator.php';
require 'encryption/decoder.php';

$geo = GeoLocator::locateVoter();
$nominee_id = salted_decode($_GET['id']);

// if ($nominee_id <= 0) {
//     header('Location: index.php');
//     exit();
// }

// Fetch nominee details
$stmt = $pdo->prepare(
    "SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code 
    FROM nominees n 
    LEFT JOIN categories c ON n.category_id = c.id 
    LEFT JOIN countries co ON n.country_id = co.id 
    WHERE n.id = ? AND n.is_active = 1
");
$stmt->execute([$nominee_id]);
$nominee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$nominee) {
    header('Location: index.php');
    exit();
}

$message = '';
$message_type = '';

// Handle vote submission
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $initial_amount = 3.00;
    // $amount = floatval($initial_amount);
    // $voter_email = trim($_POST['email'] ?? '');
    
    // Validate input
    // if ($amount <= 0) {
    //     $message = 'Please enter a valid amount';
    //     $message_type = 'danger';
    // } elseif (!empty($voter_email) && !filter_var($voter_email, FILTER_VALIDATE_EMAIL)) {
    //     $message = 'Please enter a valid email address';
    //     $message_type = 'danger';
    // } else {
        // Check if this IP has voted recently (to prevent spam)
        // $ip_address = getClientIP();
        
        // try {
        //     // Record the vote
        //     // $voteStmt = $pdo->prepare("INSERT INTO votes (nominee_id, voter_ip, voter_email, amount) VALUES (?, ?, ?, ?)");
        //     // $voteResult = $voteStmt->execute([$nominee_id, $ip_address, $voter_email, $amount]);
            

        //     //add paypal mechanism here and only record the vote after successful payment
            
        //     $voteStmt = $pdo->prepare("
        //         INSERT INTO votes (nominee_id, voter_ip, voter_email, amount, voter_country, voter_country_code)
        //         VALUES (?, ?, ?, ?, ?, ?)
        //     ");

        //     $voteResult= $voteStmt->execute([
        //         $nominee_id,
        //         $geo['ip'],
        //         $voter_email,
        //         $amount,
        //         $geo['country'],
        //         $geo['country_code']
        //     ]);
        //     if ($voteResult) {
        //         // Update nominee's vote count and amount raised
        //         $updateStmt = $pdo->prepare("UPDATE nominees SET total_votes = total_votes + 1, total_amount_raised = total_amount_raised + ? WHERE id = ?");
        //         $updateResult = $updateStmt->execute([$amount, $nominee_id]);
                
        //         if ($updateResult) {
        //             $message = 'Thank you for your vote! Your contribution helps support quality journalism.';
        //             $message_type = 'success';
                    
        //             // Refresh nominee data
        //             $stmt = $pdo->prepare(
        //                 "SELECT n.*, c.name as category_name, co.name as country_name, co.iso_code 
        //                 FROM nominees n 
        //                 LEFT JOIN categories c ON n.category_id = c.id 
        //                 LEFT JOIN countries co ON n.country_id = co.id 
        //                 WHERE n.id = ? AND n.is_active = 1
        //             ");
        //             $stmt->execute([$nominee_id]);
        //             $nominee = $stmt->fetch(PDO::FETCH_ASSOC);
        //         } else {
        //             $message = 'Vote recorded but update failed. Please try again.';
        //             $message_type = 'warning';
        //         }
        //     } else {
        //         $message = 'Error recording your vote. Please try again.';
        //         $message_type = 'danger';
        //     }
        // } catch (PDOException $e) {
        //     $message = 'Database error: ' . $e->getMessage();
        //     $message_type = 'danger';
        // }
    //}
// }


?>

<?php
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote for <?php echo htmlspecialchars($nominee['name']); ?> - World Publications Awards</title>
    
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
        .nominee-header {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #1a2a6c);
            color: white;
            padding: 60px 0;
        }
        .vote-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .vote-btn {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f);
            border: none;
            padding: 12px 25px;
            font-weight: bold;
        }
        .vote-btn:hover {
            background: linear-gradient(135deg, #0d1a4d, #8a1919);
            transform: translateY(-2px);
        }
        .stats-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
    }

    .nominee-header {
        background: linear-gradient(135deg, #1a2a6c, #b21f1f, #1a2a6c);
        color: white;
        padding: 60px 0;
    }

    .vote-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        overflow: hidden; /* prevents overflow breaking layout */
    }

    .vote-btn {
        background: linear-gradient(135deg, #1a2a6c, #b21f1f);
        border: none;
        padding: 12px 25px;
        font-weight: bold;
    }

    .vote-btn:hover {
        background: linear-gradient(135deg, #0d1a4d, #8a1919);
        transform: translateY(-2px);
    }

    .stats-box {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }

    /* 🔥 CRITICAL FIX FOR YOUR ISSUE */
    .nominee-description {
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .col-md-8 {
        overflow-wrap: anywhere;
    }
</style>

</head>
<body>
    

    <!-- Nominee Header -->
    <section class="nominee-header text-center">
        <div class="container">
            <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($nominee['name']); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($nominee['category_name']); ?></p>
            <div class="mt-3">
                <span class="badge "><?php echo render_country_flag($nominee['country_id']) . ' ' . htmlspecialchars($nominee['country_name']); ?></span>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <!-- Nominee Info -->
            <div class="col-lg-8">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="vote-card p-4 mb-4">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <?php if (!empty($nominee['logo'])): ?>
                                <img src="assets/images/<?php echo htmlspecialchars($nominee['logo']); ?>" 
                                     class="img-fluid rounded" alt="<?php echo htmlspecialchars($nominee['name']); ?>" style="max-height: 200px;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 200px;">
                                    <i class="fas fa-newspaper fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h3>
                                About <?php echo htmlspecialchars($nominee['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </h3>

                            <p class="nominee-description">
                                <?php echo nl2br(htmlspecialchars($nominee['description'], ENT_QUOTES, 'UTF-8')); ?>
                            </p>

                            
                            
                            
                            <div class="mt-3">
                                <?php 
                                    // Calculate vote percentage
                                    $totalVotesStmt = $pdo->query("SELECT COALESCE(SUM(total_votes), 1) as total FROM nominees WHERE is_active = 1");
                                    $totalVotesResult = $totalVotesStmt->fetch(PDO::FETCH_ASSOC);
                                    $totalVotes = $totalVotesResult['total'];


                                    $socialstmt = $pdo->prepare("SELECT * FROM `nominees_social_media_links` WHERE nominee_id = ?");
                                    $socialstmt->execute([$nominee['id']]);
                                    $socialLinks = $socialstmt->fetchAll(PDO::FETCH_ASSOC);

                                    $votePercentage = $totalVotes > 0 ? round(($nominee['total_votes'] / $totalVotes) * 100, 1) : 0;
                                
                                    $platformIcons = [
                                        'facebook'  => 'fa-brands fa-facebook',
                                        'twitter'   => 'fa-brands fa-x-twitter',
                                        'instagram' => 'fa-brands fa-instagram',
                                        'linkedin'  => 'fa-brands fa-linkedin',
                                        'youtube'   => 'fa-brands fa-youtube',
                                        'tiktok'    => 'fa-brands fa-tiktok',
                                        'website'   => 'fa-solid fa-globe'
                                    ];

                                ?>

                                <style>
                                    .nominee-social {
                                        display: flex;
                                        gap: 10px;
                                        margin-top: 10px;
                                    }

                                    .social-link {
                                        font-size: 18px;
                                        color: #555;
                                        transition: 0.3s ease;
                                    }

                                    .social-link:hover {
                                        color: #0d6efd; /* Bootstrap primary */
                                        transform: translateY(-2px);
                                    }

                                </style>
                                <?php if (!empty($socialLinks)): ?>
                                    <div class="nominee-social">
                                        <?php foreach ($socialLinks as $social): 
                                            $platform = strtolower($social['platform_name']);
                                            $icon = $platformIcons[$platform] ?? 'fa-solid fa-link';
                                        ?>
                                            <a href="<?= htmlspecialchars($social['link']) ?>" 
                                            target="_blank" 
                                            rel="noopener noreferrer"
                                            class="social-link">
                                                <i class="<?= $icon ?>"></i>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>


                                
                                <p><i class="fas fa-chart-pie text-info"></i> <strong>Vote Percentage:</strong> <?php echo $votePercentage; ?>%</p>
                            </div>
                            
                            
                        </div>
                    </div>
                </div>
                
                <!-- Voting Form -->
                <div class="vote-card p-4">
                    <h4><i class="fas fa-vote-yea me-2 text-primary"></i>Support This Nominee</h4>
                    <p>Your vote helps promote quality journalism and supports this nominee's work.</p>
                    
                    <!-- <form method="POST" action=""> -->
                        <div class="mb-3">
                            <!-- <label for="amount" class="form-label">Contribution Amount ($)</label>
                            <input type="number"  class="form-control" id="amount" name="amount" step="0.01" min="1" value="3.00" required disabled> -->
                            <div class="form-text">Contribution Amount ($) <strong>3.00</strong></div>
                            <!-- <div class="form-text">Enter the amount you wish to contribute</div> -->
                            
                        </div>
                        
                        <!-- <div class="mb-3">
                            <label for="email" class="form-label">Email (Optional)</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="your.email@example.com">
                            <div class="form-text">Provide your email to receive updates about this nominee</div>
                        </div> -->
                        
                        <!-- <button type="submit" class="btn btn-primary vote-btn">
                            <i class="fas fa-vote-yea me-2"></i>Submit Vote
                        </button> -->
                       
                        <div id="paypal-button-container"></div>
                        <p id="result-message"></p>

                    
                        <!-- Initialize the JS-SDK -->
                        <script src="https://www.paypal.com/sdk/js?client-id=ATaPxMLK8HSXmee6813Sy5fs4I2PmiAruEb_LOb3Kwk7LbNBNNN5S0nvN_4d-CA5w2SCo1hcBAR99isa&buyer-country=US"></script>
                        <script>
                            paypal.Buttons({
                            // style: {
                            //     layout: 'vertical',
                            //     color:  'gold',
                            //     shape:  'rect',
                            //     label:  'paypal'
                            // },
                            createOrder: function (data, actions){
                                    return actions.order.create({
                                        intent: 'CAPTURE',
                                        payer: {
                                            nominee_id : <?= $nominee_id?>
                                        },
                                        purchase_units: [{
                                            amount: {
                                                value: '3.00', 
                                                currency_code: 'USD'
                                            }
                                        }]
                                    });
                                }, 

                            onApprove: function (data, actions) {

                                return actions.order.capture().then(function (details) {

                                    const orderID = details.id;
                                    const nomineeId = <?= (int)$nominee_id ?>;
                                    const nomineeName = <?= json_encode($nominee['name']) ?>;

                                    // 🔁 Send orderID to backend for verification + vote recording
                                    fetch('paypal/process-vote.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            orderID: orderID,
                                            nominee_id: nomineeId
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(result => {

                                        if (result.success) {

                                            document.getElementById('result-message').style.color = 'green';
                                            document.getElementById('result-message').innerHTML =
                                                'Thank you for your vote! Your contribution supports <strong>' 
                                                + nomineeName + 
                                                '</strong>.<br>Transaction ID: ' + orderID;

                                            document.getElementById('paypal-button-container').style.display = 'none';

                                        } else {

                                            document.getElementById('result-message').style.color = 'red';
                                            document.getElementById('result-message').innerHTML =
                                                result.message || 'Vote could not be recorded.';

                                        }
                                    })
                                    .catch(error => {

                                        document.getElementById('result-message').style.color = 'red';
                                        document.getElementById('result-message').innerHTML =
                                            'Server error while recording vote.';

                                        console.error('Vote error:', error);
                                    });

                                });
                            },
                                onCancel: function (data){
                                    //show a cancel message, or go back to cart
                                    document.getElementById('result-message').style.color = 'red';
                                    document.getElementById('result-message').innerHTML = 'Payment cancelled. Your vote was not recorded.';
                                    console.log(data);
                                },
                                onError: function (err){
                                    //show an error message
                                    document.getElementById('result-message').style.color = 'red';
                                    document.getElementById('result-message').innerHTML = 'An error occurred during the payment process. Please try again.';
                                    console.error(err);
                                }
                            }).render('#paypal-button-container');
                            
                        </script>
                        
                        
                        

                        <!-- <div id="paypal-button-container" class="paypal-button-container"></div> -->
                    <!-- </form> -->
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Stats -->
                <div class="vote-card p-4 mb-4">
                    <h5><i class="fas fa-chart-bar me-2 text-info"></i>Current Status</h5>
                    <?php 
                        // Calculate vote percentage
                        $totalVotesStmt = $pdo->query("SELECT COALESCE(SUM(total_votes), 1) as total FROM nominees WHERE is_active = 1");
                        $totalVotesResult = $totalVotesStmt->fetch(PDO::FETCH_ASSOC);
                        $totalVotes = $totalVotesResult['total'];
                        $votePercentage = $totalVotes > 0 ? round(($nominee['total_votes'] / $totalVotes) * 100, 1) : 0;
                    ?>
                    <div class="stats-box">
                        <i class="fas fa-chart-pie fa-2x text-info mb-2"></i>
                        <h3><?php echo $votePercentage; ?>%</h3>
                        <p class="mb-0">of Total Votes</p>
                    </div>
                </div>
                
                <!-- Related Nominees -->
                <div class="vote-card p-4">
                    <h5><i class="fas fa-users me-2 text-secondary"></i>Other Nominees in <?php echo htmlspecialchars($nominee['category_name']); ?></h5>
                    <?php
                    $relatedStmt = $pdo->prepare(
                        "SELECT n.id, n.name, n.logo, n.total_votes, co.name, co.id as country_name, co.iso_code 
                        FROM nominees n 
                        LEFT JOIN countries co ON n.country_id = co.id 
                        WHERE n.category_id = ? AND n.id != ? AND n.is_active = 1 
                        ORDER BY n.total_votes DESC 
                        LIMIT 5
                    ");
                    $relatedStmt->execute([$nominee['category_id'], $nominee['id']]);
                    $relatedNominees = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                    // Get total votes for percentage calculation
                    $totalVotesStmt = $pdo->query("SELECT COALESCE(SUM(total_votes), 1) as total FROM nominees WHERE is_active = 1");
                    $totalVotesResult = $totalVotesStmt->fetch(PDO::FETCH_ASSOC);
                    $totalVotesAll = $totalVotesResult['total'];
                    ?>
                    
                    <?php if (!empty($relatedNominees)): ?>
                        <div class="list-group">
                            <?php
                                require 'encryption/encoder.php';
                            ?>
                            <?php foreach ($relatedNominees as $related): ?>
                                <a href="vote.php?id=<?php echo salted_encode($related['id']); ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php if (!empty($related['logo'])): ?>
                                                <img src="assets/images/<?php echo htmlspecialchars($related['logo']); ?>" 
                                                    class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                            <?php else: ?>
                                                <i class="fas fa-newspaper me-2 text-muted"></i>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($related['name']); ?><br>
                                        </div>
                                        <?php 
                                            $relatedVotePercentage = $totalVotesAll > 0 ? round(($related['total_votes'] / $totalVotesAll) * 100, 1) : 0;
                                        ?>
                                        <span class="badge bg-primary"><?php echo $relatedVotePercentage; ?>%</span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No other nominees in this category.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>