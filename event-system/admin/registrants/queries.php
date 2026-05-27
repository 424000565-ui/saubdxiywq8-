<?php
// ── queries.php ──
// All database queries for the Registrant Viewer (Role 4) - PDO REFACTORED

// Fetch all events for the dropdown filter
function getEvents($pdo) {
    try {
        // FIXED: Using 'event_id' instead of 'id' to match your database column architecture
        $sql = "SELECT event_id, title FROM events ORDER BY event_date ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database Error inside getEvents(): " . $e->getMessage());
    }
}

// Fetch all registrants (with optional event filter)
function getRegistrants($pdo, $event_id = 0) {
   try {
    // 3. If 'All Events' or nothing is selected, display everything
    if ($event_id === 0) {
        $query = "
            SELECT 
                r.user_id, 
                u.name AS name, 
                u.email AS email,
                e.title AS title
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
                e.title AS title
            FROM registrations r
            INNER JOIN users u ON r.user_id = u.user_id
            INNER JOIN events e ON r.event_id = e.event_id
            WHERE r.event_id = :event_id
            ORDER BY u.name ASC
        ";
        $registrants_stmt = $pdo->prepare($query);
        $registrants_stmt->execute([':event_id' => $event_id]);
    }
    
    // Assign values dynamically back into the global variable used by your HTML table loop
    $registrants = $registrants_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // 💡 Alternative Fallback: If your table actually uses student_id instead of user_id, 
    // change the code above from 'u.user_id' to 'u.student_id'
    die("Error filtering records: " . $e->getMessage());
}
}