
<!-- TOP NAVIGATION -->
 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WPA Admin – Traffic & Analytics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --wpa-primary: #0b1c2d;
            --wpa-secondary: #1f4fd8;
            --wpa-accent: #d4af37;
            --wpa-bg: #f5f7fa;
        }

        body {
            background-color: var(--wpa-bg);
        }

        .navbar-wpa {
            background-color: var(--wpa-primary);
        }

        .navbar-wpa .nav-link,
        .navbar-wpa .navbar-brand {
            color: #ffffffcc;
        }

        .navbar-wpa .nav-link.active,
        .navbar-wpa .nav-link:hover {
            color: #fff;
            border-bottom: 2px solid var(--wpa-accent);
        }

        .kpi-card {
            border-top: 4px solid var(--wpa-accent);
        }

        .section-title {
            border-left: 5px solid var(--wpa-secondary);
            padding-left: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>


<nav class="navbar navbar-expand-lg navbar-wpa">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="wpaNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="traffic.php">Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="integrity.php">Integrity</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Reports</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
