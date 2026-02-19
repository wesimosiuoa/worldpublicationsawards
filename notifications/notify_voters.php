<?php

    //this is where all voters will be notified on any activity taking place on the platform. This includes new nominees, new blog posts, and any other important updates related to the awards.    
    //it i will be done using a cron job that will run every hour to check for any new updates and send out notifications to all voters.
    //notifications are sent via email and also stored in the database for users to view on their profile page.
    //this is only for subscribers who have opted in to receive notifications. Users can manage their notification preferences in their profile settings.
    //and it is only sent to their email address that they used to register on the platform.

    function notifyVoters($subject, $message) {
        global $pdo;

        // Fetch all voters who have opted in for notifications
        $stmt = $pdo->prepare("SELECT email FROM newsletter_subscribers ");
        $stmt->execute();
        $voters = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($voters as $voter) {
            // Send email notification
            //mail($voter['email'], $subject, $message);
            sendEmail ($voter['email'], $subject, $message);
        }
    }