<?php
session_start();
include("../includes/db.php");

// Protect the route: Ensure student is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // This is the logged-in student's ID

if(isset($_GET['id'])){

    $event_id = $_GET['id'];

    try {
        // 1. CHECK IF ALREADY REGISTERED using safe PDO prepared statement
        $check_stmt = $pdo->prepare("SELECT * FROM registrations WHERE student_id = :student_id AND event_id = :event_id");
        $check_stmt->execute([
            ':student_id' => $user_id,
            ':event_id'   => $event_id
        ]);

        if($check_stmt->rowCount() > 0){
            echo "
            <script>
            alert('Already Registered');
            window.location='../dashboard.php';
            </script>
            ";
            exit();
        }

        // 2. INSERT INTO REGISTRATIONS
        $reg_stmt = $pdo->prepare("INSERT INTO registrations (student_id, event_id) VALUES (:student_id, :event_id)");
        $reg_stmt->execute([
            ':student_id' => $user_id,
            ':event_id'   => $event_id
        ]);

        // 3. INSERT INTO ATTENDANCE (Sets them up for check-in; defaults to 'present')
        $att_stmt = $pdo->prepare("INSERT INTO attendance (student_id, event_id, status) VALUES (:student_id, :event_id, 'present')");
        $att_stmt->execute([
            ':student_id' => $user_id,
            ':event_id'   => $event_id
        ]);

        echo "
        <script>
        alert('Event Registered Successfully');
        window.location='my_events.php';
        </script>
        ";

    } catch (PDOException $e) {
        // Handle database errors smoothly by backing out to the root dashboard safely
        echo "
        <script>
        alert('Database Error: Unable to complete registration.');
        window.location='../dashboard.php';
        </script>
        ";
    }

}else{
    // Backs out to root dashboard cleanly if no numeric Event ID is present in the URL parameter
    echo "
    <script>
    alert('No Event ID Found');
    window.location='../dashboard.php';
    </script>
    ";
}
?>