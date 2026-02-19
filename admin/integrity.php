<?php
include 'header.php';
include 'traffic/header.php';
include 'traffic/fn.inc.php';

// ---- FETCH DATA ----
$ipCountryAnomalies = getIpCountryAnomalies();
$highFrequencyIps   = getHighFrequencyIps(10);
$countrySpikes      = getCountryVoteSpikes();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voting Integrity – WPA Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container my-4">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">
            <i class="fas fa-shield-alt text-danger me-2"></i>Voting Integrity
        </h3>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>

    <!-- INTEGRITY PRINCIPLES -->
    <div class="alert alert-warning">
        <strong>Integrity Notice:</strong>
        All indicators shown below are <em>diagnostic alerts</em>.
        They highlight voting patterns that may require administrative review.
        No automatic penalties are applied.
    </div>

    <!-- IP → COUNTRY ANOMALIES -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="section-title mb-3">
                <i class="fas fa-network-wired me-2"></i>IP & Country Anomalies
            </h5>

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Countries Used</th>
                            <th>Risk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($ipCountryAnomalies): ?>
                            <?php foreach ($ipCountryAnomalies as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['voter_ip']) ?></td>
                                    <td><?= $row['country_count'] ?></td>
                                    <td><span class="badge bg-danger">High</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No anomalies detected</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- HIGH FREQUENCY IPs -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="section-title mb-3">
                <i class="fas fa-bolt me-2"></i>High-Frequency Voting IPs
            </h5>

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Votes Cast</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($highFrequencyIps): ?>
                            <?php foreach ($highFrequencyIps as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['voter_ip']) ?></td>
                                    <td><?= $row['vote_count'] ?></td>
                                    <td><span class="badge bg-warning text-dark">Review</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No high-frequency activity</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- COUNTRY VOTE SPIKES -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="section-title mb-3">
                <i class="fas fa-chart-line me-2"></i>Country Vote Spikes
            </h5>

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th>Date</th>
                            <th>Votes</th>
                            <th>Flag</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($countrySpikes): ?>
                            <?php foreach ($countrySpikes as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['voter_country']) ?></td>
                                    <td><?= htmlspecialchars($row['vote_date']) ?></td>
                                    <td><?= $row['votes'] ?></td>
                                    <td><span class="badge bg-danger">Spike</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No spikes detected</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
