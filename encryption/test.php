<?php 
    require_once 'encoder.php';
    
    $name = salted_encode("John Doe");
    echo "Encoded Name: " . $name . "\n";
    

    ?>
    
        <a href="train.php?name=<?=$name?>"> Click to go to train page</a>
    <?php
?>