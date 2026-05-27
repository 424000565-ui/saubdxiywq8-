<?php
// NOTICE FIX: Removed duplicate session_start() call to eliminate log warnings

include("../../includes/db.php");

// Protect route
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../../auth/login.php");
    exit();
}

// 1. Get all events to fill up the filter dropdown list
try {
    $events_result = $pdo->query("SELECT event_id, title FROM events ORDER BY event_date DESC");
} catch (PDOException $e) {
    die("Error fetching events: " . $e->getMessage());
}

// 2. Capture selected filter option
$selected_event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

try {
    // 3. If 'All Events' or nothing is selected, display everything
    if ($selected_event_id === 0) {
        $query = "
            SELECT 
                r.user_id, 
                u.name AS name, 
                u.email AS email,
                e.title AS title,
                r.registration_date -- Ensure this matches your DB column name if you show dates
            FROM registrations r
            INNER JOIN users u ON r.user_id = u.user_id
            INNER JOIN events e ON r.event_id = e.event_id
            ORDER BY r.registration_id DESC
        ";
        $registrants_stmt = $pdo->query($query);
    } else {
        // Otherwise, filter down specifically by selected event ID using a prepared statement
        $query = "
            SELECT 
                r.user_id, 
                u.name AS name, 
                u.email AS email,
                e.title AS title,
                r.registration_date
            FROM registrations r
            INNER JOIN users u ON r.user_id = u.user_id
            INNER JOIN events e ON r.event_id = e.event_id
            WHERE r.event_id = :event_id
            ORDER BY u.name ASC
        ";
        $registrants_stmt = $pdo->prepare($query);
        $registrants_stmt->execute([':event_id' => $selected_event_id]);
    }
    
    // Fetch values cleanly to map inside the main view table loop array
    $registrants = $registrants_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error filtering records: " . $e->getMessage());
}
?>