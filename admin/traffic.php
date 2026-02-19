<?php
    include 'header.php';
    include 'traffic/header.php';
    //include '../includes/dbcon.inc.php';
    include 'traffic/fn.inc.php';

    $votesByCountry = getVotesByCountry();
    $topCountries = getTopVotingCountries(5);
   
    $totalVotes    = getTotalVotes();
    //$topCountries  = getTopVotingCountries(5);




?>
<script>
    const votesByCountry = <?= json_encode($votesByCountry, JSON_HEX_TAG) ?>;
    const topCountriesData = <?= json_encode($topCountries, JSON_HEX_TAG) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const labels = votesByCountry.map(item => item.voter_country);
    const data = votesByCountry.map(item => item.total_votes);

    // WPA-friendly color palette
    const barColors = [
        '#0d6efd', // WPA Blue
        '#198754', // Green
        '#ffc107', // Yellow
        '#dc3545', // Red
        '#6f42c1', // Purple
        '#20c997', // Teal
        '#fd7e14', // Orange
        '#6610f2', // Indigo
        '#0dcaf0', // Cyan
        '#adb5bd'  // Gray
    ];

    const ctx = document.getElementById('votesByCountryChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Number of Votes',
                data: data,
                backgroundColor: barColors.slice(0, data.length),
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Votes Distribution by Country',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        bottom: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' votes';
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Country'
                    },
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Votes'
                    },
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

});
</script>


<!-- MAIN CONTENT -->
<div class="container-fluid px-4 mt-4">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Traffic & Voting Analytics</h3>
        
    </div>

    <!-- KPI ROW -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card kpi-card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Countries Participating</h6>
                    <h3><?php echo getParticipatingCountries(); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card kpi-card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Votes</h6>
                    <h3><?php echo getTotalVotes(); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card kpi-card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Revenue</h6>
                    <h3>$ <?php echo getTotalRevenue(); ?></h3>
                </div>
            </div>
        </div>
    </div>

   <!-- GLOBAL OVERVIEW --> 
    <div class="card shadow-sm mb-4"> 
        <div class="card-body"> 
            <h5 class="section-title"> <i class="fas fa-globe me-2"></i>Global Overview </h5>
            <div class="text-center text-muted py-5"> <canvas id="votesByCountryChart" height="120"></canvas> </div>
        </div>
    </div>


    <!-- TOP COUNTRIES -->


        <!-- TOP VOTING COUNTRIES -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="section-title">
                <i class="fas fa-flag me-2"></i>Top Voting Countries
            </h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th>Votes</th>
                            <th>Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($topCountries)): ?>
                            <?php foreach ($topCountries as $row): 
                                $share = $totalVotes > 0 
                                    ? ($row['total_votes'] / $totalVotes) * 100 
                                    : 0;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['voter_country']) ?></td>
                                    <td><?= number_format($row['total_votes']) ?></td>
                                    <td><?= number_format($share, 1) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No voting data available
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>


</div>

]

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>



    <?php
    include 'footer.php';

?>

                            