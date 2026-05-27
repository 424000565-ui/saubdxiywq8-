<?php
session_start();

include("../../includes/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../../auth/login.php");
    exit();
}

// Verify that an ID parameter exists before attempting to process database drops
if (isset($_GET['id'])) {
    
    // Grab the ID from the URL parameters and enforce strict integer sanitization
    $id = intval($_GET['id']);

    try {
        // Converted to a secure, prepared PDO deletion statement using $pdo
        $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = :id");
        $stmt->execute([':id' => $id]);
        
    } catch (PDOException $e) {
        // Handle error and output message if data integrity rules block deletion
        die("Error processing event deletion database execution: " . $e->getMessage());
    }
}

// FIXED: Cleaned up relative pathing traversal rules to prevent 404 URL loops
header("Location: ./admin_events.php?status=deleted");
exit();
?>