<?php 
    include 'includes/dbcon.inc.php';
    include_once 'includes/helpers.php';
    include_once 'includes/messages.php';

    // Display popup messages if available
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    displayPopupMessage();
    displayFlashMessage();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>World Publications Awards</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> -->

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/custom.css" rel="stylesheet">
    
    <!-- Popup Styles -->
    <?php addPopupStyles(); ?>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            World Publications Awards
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <div class="d-flex me-auto my-2 my-lg-0" style="position: relative;">
                <div id="search-container">
                    <button id="search-toggle-btn" class="btn btn-outline-secondary" type="button" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                    <form id="live-search-form" style="display: none; position: absolute; right: 0; width: 250px; z-index: 1000;">
                        <input class="form-control" type="search" placeholder="Search nominees..." aria-label="Search" id="live-search-input" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    </form>
                </div>
                <div id="live-search-results" class="dropdown-menu" style="display: none; max-height: 300px; overflow-y: auto; position: absolute; z-index: 1000; right: 0; width: 250px;">
                    <div id="search-results-content"></div>
                </div>
            </div>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="how-it-works.php">How It Works</a></li>
                <li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="nominees.php">Nominees</a></li>
                <li class="nav-item"><a class="nav-link" href="numbers.php">Numbers</a></li>
                <li class="nav-item">
                    <a class="btn btn-warning ms-lg-2 fw-semibold" href="start-voting.php" title="Start Voting">
                        <i class="fas fa-vote-yea"></i><span class="d-none d-lg-inline"> Vote</span>
                    </a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a href="login.php" class="btn btn-outline-light" title="Login">
                        <i class="fas fa-sign-in-alt"></i><span class="d-none d-lg-inline"> Login</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel"> Sign in </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm" action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Login</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <small class="text-muted">For administrators and stakeholders only</small>
                </div>
            </div>
        </div>
    </div>

        <!-- Loading Indicator -->
    <div id="loadingIndicator" class="position-fixed top-0 start-0 w-100" style="z-index: 9999; height: 3px; display: none;">
        <div class="progress" style="height: 3px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 100%; height: 3px;"></div>
        </div>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loadingIndicator').style.display = 'block';
        }
        
        function hideLoading() {
            document.getElementById('loadingIndicator').style.display = 'none';
        }
        
        // Override the global fetch function to show loader for all requests
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            showLoading();
            return originalFetch.apply(this, args)
                .finally(() => {
                    hideLoading();
                });
        };
        
        // Also override XMLHttpRequest to show loader for all AJAX requests
        const originalXHR = window.XMLHttpRequest;
        window.XMLHttpRequest = function() {
            const xhr = new originalXHR();
            
            const originalOpen = xhr.open;
            xhr.open = function(...args) {
                originalOpen.apply(this, args);
            };
            
            const originalSend = xhr.send;
            xhr.send = function(...args) {
                showLoading();
                const loadendHandler = () => {
                    hideLoading();
                    xhr.removeEventListener('loadend', loadendHandler);
                };
                xhr.addEventListener('loadend', loadendHandler);
                return originalSend.apply(this, args);
            };
            
            return xhr;
        };
        
        // Search toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchToggleBtn = document.getElementById('search-toggle-btn');
            const searchForm = document.getElementById('live-search-form');
            const searchInput = document.getElementById('live-search-input');
            const searchResults = document.getElementById('live-search-results');
            const searchResultsContent = document.getElementById('search-results-content');
            let searchTimeout;
            
            // Toggle search input visibility
            searchToggleBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                const isDisplayed = searchForm.style.display !== 'none';
                
                if (isDisplayed) {
                    searchForm.style.display = 'none';
                    searchResults.style.display = 'none';
                } else {
                    searchForm.style.display = 'block';
                    searchInput.focus();
                }
            });
            
            // Handle search input
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                
                const searchTerm = this.value.trim();
                
                if (searchTerm.length === 0) {
                    searchResults.style.display = 'none';
                    return;
                }
                
                if (searchTerm.length < 2) {
                    searchResultsContent.innerHTML = '<div class="dropdown-item-text px-3 py-2">Enter at least 2 characters to search</div>';
                    searchResults.style.display = 'block';
                    return;
                }
                
                // Debounce the search to avoid too many requests
                searchTimeout = setTimeout(function() {
                    fetch(`search_nominees.php?q=${encodeURIComponent(searchTerm)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                let html = '';
                                data.forEach(nominee => {
                                    html += `<a class="dropdown-item" href="nominees.php?search=${encodeURIComponent(searchTerm)}&highlight=${encodeURIComponent(nominee.name)}">${nominee.name} - ${nominee.country}</a>`;
                                });
                                searchResultsContent.innerHTML = html;
                                searchResults.style.display = 'block';
                            } else {
                                searchResultsContent.innerHTML = '<div class="dropdown-item-text px-3 py-2">No nominees found</div>';
                                searchResults.style.display = 'block';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching search results:', error);
                        });
                }, 300); // Wait 300ms after user stops typing
            });
            
            // Hide search form and results when clicking outside
            document.addEventListener('click', function(event) {
                if (!searchForm.contains(event.target) && event.target !== searchToggleBtn) {
                    searchForm.style.display = 'none';
                    searchResults.style.display = 'none';
                }
            });
            
            // Prevent form submission
            const searchFormElement = document.getElementById('live-search-form');
            searchFormElement.addEventListener('submit', function(e) {
                e.preventDefault();
            });
        });

    </script>

<div style="margin-top:80px;"></div>
</body>
</html>