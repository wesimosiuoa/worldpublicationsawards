<?php
// Test script for message system
session_start();

include 'includes/messages.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Message System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .test-section {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
    <?php addPopupStyles(); ?>
</head>
<body>
    <div class="container">
        <h1>Message System Test</h1>
        
        <div class="test-section">
            <h3>1. Direct Message Functions</h3>
            <?php
            showSuccessMessage("This is a direct success message!");
            showErrorMessage("This is a direct error message!");
            showWarningMessage("This is a direct warning message!");
            showInfoMessage("This is a direct info message!");
            ?>
        </div>
        
        <div class="test-section">
            <h3>2. Flash Messages (Set and Display)</h3>
            <?php
            // Set flash messages
            setFlashMessage("This is a flash success message!", 'success');
            setFlashMessage("This is a flash error message!", 'error');
            setFlashMessage("This is a flash warning message!", 'warning');
            setFlashMessage("This is a flash info message!", 'info');
            
            // Display flash messages
            displayFlashMessage();
            ?>
        </div>
        
        <div class="test-section">
            <h3>3. Popup Messages (Set and Display)</h3>
            <?php
            // Set popup messages
            setPopupMessage("This is a popup success message!", 'success');
            setPopupMessage("This is a popup error message!", 'error');
            setPopupMessage("This is a popup warning message!", 'warning');
            setPopupMessage("This is a popup info message!", 'info');
            
            // Display popup messages
            displayPopupMessage();
            ?>
        </div>
        
        <div class="test-section">
            <h3>4. Generic Message Function</h3>
            <?php
            showMessage("This is a generic success message!", 'success');
            showMessage("This is a generic error message!", 'error');
            showMessage("This is a generic warning message!", 'warning');
            showMessage("This is a generic info message!", 'info');
            ?>
        </div>
        
        <div class="test-section">
            <h3>5. Test Form Submission Messages</h3>
            <form method="post" class="mb-3">
                <div class="mb-3">
                    <label for="testInput" class="form-label">Test Input:</label>
                    <input type="text" class="form-control" id="testInput" name="testInput">
                </div>
                <button type="submit" class="btn btn-primary">Submit Test</button>
            </form>
            
            <?php
            if ($_POST) {
                if (empty($_POST['testInput'])) {
                    setFlashMessage("Please enter some text!", 'error');
                } else {
                    setFlashMessage("Form submitted successfully with: " . htmlspecialchars($_POST['testInput']), 'success');
                }
                displayFlashMessage();
            }
            ?>
        </div>
        
        <div class="test-section">
            <h3>6. Validation Test</h3>
            <form method="post" class="mb-3">
                <div class="mb-3">
                    <label for="requiredField" class="form-label">Required Field:</label>
                    <input type="text" class="form-control" id="requiredField" name="requiredField">
                </div>
                <div class="mb-3">
                    <label for="emailField" class="form-label">Email Field:</label>
                    <input type="email" class="form-control" id="emailField" name="emailField">
                </div>
                <button type="submit" name="validationTest" class="btn btn-primary">Validate Form</button>
            </form>
            
            <?php
            if (isset($_POST['validationTest'])) {
                $fields = [
                    'requiredField' => $_POST['requiredField'] ?? '',
                ];
                
                $errors = validateRequiredFields($fields);
                
                if (!empty($_POST['emailField'])) {
                    $emailError = validateEmail($_POST['emailField']);
                    if ($emailError) {
                        $errors[] = $emailError;
                    }
                }
                
                if (!empty($errors)) {
                    displayValidationErrors($errors);
                } else {
                    showSuccessMessage("Form validated successfully!");
                }
            }
            ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>