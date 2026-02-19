<?php
/**
 * System Messages Handler for World Publications Awards
 */

// Check if functions already exist to avoid conflicts
if (!function_exists('showSuccessMessage')) {
    // Function to display success message as popup
    function showSuccessMessage($message) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> ' . htmlspecialchars($message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

if (!function_exists('showErrorMessage')) {
    // Function to display error message as popup
    function showErrorMessage($message) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> ' . htmlspecialchars($message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

if (!function_exists('showWarningMessage')) {
    // Function to display warning message as popup
    function showWarningMessage($message) {
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Warning!</strong> ' . htmlspecialchars($message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

if (!function_exists('showInfoMessage')) {
    // Function to display info message as popup
    function showInfoMessage($message) {
        echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Info!</strong> ' . htmlspecialchars($message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

if (!function_exists('showMessage')) {
    // Function to display a message based on type
    function showMessage($message, $type = 'info') {
        switch($type) {
            case 'success':
                showSuccessMessage($message);
                break;
            case 'error':
                showErrorMessage($message);
                break;
            case 'warning':
                showWarningMessage($message);
                break;
            case 'info':
            default:
                showInfoMessage($message);
                break;
        }
    }
}

if (!function_exists('setFlashMessage')) {
    // Function to set flash message in session
    function setFlashMessage($message, $type = 'info') {
        $_SESSION['flash_message'] = [
            'message' => $message,
            'type' => $type
        ];
    }
}

if (!function_exists('displayFlashMessage')) {
    // Function to display and clear flash message
    function displayFlashMessage() {
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            showMessage($flash['message'], $flash['type']);
            unset($_SESSION['flash_message']);
        }
    }
}

if (!function_exists('showPopupNotification')) {
    // Function to show popup notification (JavaScript-based)
    function showPopupNotification($message, $type = 'info') {
        $icon = '';
        switch($type) {
            case 'success':
                $icon = 'fas fa-check-circle';
                break;
            case 'error':
                $icon = 'fas fa-exclamation-circle';
                break;
            case 'warning':
                $icon = 'fas fa-exclamation-triangle';
                break;
            case 'info':
            default:
                $icon = 'fas fa-info-circle';
                break;
        }
        
        echo '<div id="popupNotification" class="popup-notification" style="display:none;">
                <div class="popup-content">
                    <i class="' . $icon . ' popup-icon popup-' . $type . '"></i>
                    <div class="popup-message">' . htmlspecialchars($message) . '</div>
                    <button class="popup-close">&times;</button>
                </div>
              </div>';
        
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var popup = document.getElementById("popupNotification");
                    if (popup) {
                        popup.style.display = "flex";
                        setTimeout(function() {
                            popup.style.display = "none";
                        }, 5000); // Auto-hide after 5 seconds
                    }
                });
              </script>';
    }
}

if (!function_exists('setPopupMessage')) {
    // Enhanced function to set and show popup notification
    function setPopupMessage($message, $type = 'info') {
        $_SESSION['popup_message'] = [
            'message' => $message,
            'type' => $type
        ];
    }
}

if (!function_exists('displayPopupMessage')) {
    // Function to display popup message
    function displayPopupMessage() {
        if (isset($_SESSION['popup_message'])) {
            $popup = $_SESSION['popup_message'];
            showPopupNotification($popup['message'], $popup['type']);
            unset($_SESSION['popup_message']);
        }
    }
}

if (!function_exists('validateRequiredFields')) {
    // Function to validate required fields
    function validateRequiredFields($fields) {
        $errors = [];
        
        foreach ($fields as $field => $value) {
            if (empty(trim($value))) {
                $errors[] = "$field is required";
            }
        }
        
        return $errors;
    }
}

if (!function_exists('validateEmail')) {
    // Function to validate email
    function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format";
        }
        return null;
    }
}

if (!function_exists('validateUrl')) {
    // Function to validate URL
    function validateUrl($url) {
        if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
            return "Invalid URL format";
        }
        return null;
    }
}

if (!function_exists('validateImageFile')) {
    // Function to validate image file
    function validateImageFile($file) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // No file uploaded, which might be OK
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return "Error uploading file";
        }
        
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg+xml'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        
        $fileType = $file['type'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileType, $allowedTypes)) {
            return "Invalid file type. Only JPG, PNG, GIF, and SVG files are allowed.";
        }
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            return "Invalid file extension. Only JPG, PNG, GIF, and SVG files are allowed.";
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            return "File size exceeds 5MB limit";
        }
        
        return null;
    }
}

if (!function_exists('displayValidationErrors')) {
    // Function to generate validation errors display
    function displayValidationErrors($errors) {
        if (!empty($errors)) {
            echo '<div class="alert alert-danger" role="alert">';
            echo '<strong>Please fix the following errors:</strong><ul class="mb-0">';
            foreach ($errors as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul></div>';
        }
    }
}

if (!function_exists('addPopupStyles')) {
    // Function to add popup CSS and JS (to be called in header)
    function addPopupStyles() {
        echo '<style>
            .popup-notification {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                display: flex;
                align-items: center;
                min-width: 300px;
                max-width: 500px;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideInRight 0.3s ease-out;
            }
            
            .popup-content {
                display: flex;
                align-items: center;
                width: 100%;
            }
            
            .popup-icon {
                font-size: 24px;
                margin-right: 10px;
            }
            
            .popup-success { color: #155724; }
            .popup-error { color: #721c24; }
            .popup-warning { color: #856404; }
            .popup-info { color: #0c5460; }
            
            .popup-message {
                flex-grow: 1;
                margin-right: 10px;
            }
            
            .popup-close {
                background: none;
                border: none;
                font-size: 20px;
                cursor: pointer;
                padding: 0;
                margin-left: 10px;
            }
            
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>
        
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Add event listener to close buttons
                document.addEventListener("click", function(e) {
                    if (e.target.classList.contains("popup-close")) {
                        var popup = e.target.closest(".popup-notification");
                        if (popup) {
                            popup.style.display = "none";
                        }
                    }
                });
            });
        </script>';
    }
}
?>