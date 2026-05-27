<?php
session_start();

include("../includes/db.php");

// Protect the route: ensure the user is logged in AND is an admin/scanner
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

/* 1. MARK ATTENDANCE */
if(isset($_GET['user_id']) && isset($_GET['event_id'])){

    $student_id = $_GET['user_id']; // This is the student's ID
    $event_id = $_GET['event_id'];

    try {
        // CHECK DUPLICATE using safe PDO prepared statement
        $check_stmt = $pdo->prepare("SELECT * FROM attendance WHERE student_id = :student_id AND event_id = :event_id");
        $check_stmt->execute([
            ':student_id' => $student_id,
            ':event_id'   => $event_id
        ]);

        if($check_stmt->rowCount() == 0){

            // INSERT INTO ATTENDANCE (time_in automatically tracks the current stamp)
            $insert_stmt = $pdo->prepare("INSERT INTO attendance (student_id, event_id, status) VALUES (:student_id, :event_id, 'present')");
            $insert_stmt->execute([
                ':student_id' => $student_id,
                ':event_id'   => $event_id
            ]);

            echo "
            <script>
                alert('Attendance Marked!');
                window.location='attendance.php';
            </script>
            ";
            exit();

        } else {
            echo "
            <script>
                alert('Already Marked Present!');
                window.location='attendance.php';
            </script>
            ";
            exit();
        }

    } catch (PDOException $e) {
        echo "
        <script>
            alert('Database Error: Unable to mark attendance.');
            window.location='attendance.php';
        </script>
        ";
        exit();
    }
}

/* 2. GET REGISTRATIONS FOR THE VIEW LIST */
try {
    // Upgraded SQL using our explicit primary keys (user_id, event_id) and full_name column
    $query = "
        SELECT r.student_id AS user_id,
               u.full_name AS name,
               e.title,
               e.event_id AS event_id
        FROM registrations r
        INNER JOIN users u ON r.student_id = u.user_id
        INNER JOIN events e ON r.event_id = e.event_id
        ORDER BY r.registration_id DESC
    ";

    // Execute query using PDO
    $result = $pdo->query($query);
    
} catch (PDOException $e) {
    die("Database query error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance System</title>
<style>
body{
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 0;
}

.container{
    width: 900px;
    margin: auto;
    margin-top: 50px;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px gray;
}

h1{
    text-align: center;
    color: orange;
}

table{
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td{
    border: 1px solid gray;
}

th, td{
    padding: 12px;
    text-align: center;
}

a{
    background: green;
    color: white;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 5px;
}

a:hover{
    background: darkgreen;
}

.back{
    display: inline-block;
    margin-top: 20px;
    background: orange;
}

.back:hover{
    background: #cc6600;
}
</style>
</head>
<body>

<div class="container">

    <h1>Attendance System</h1>

    <table>
        <tr>
            <th>Student</th>
            <th>Event</th>
            <th>Action</th>
        </tr>

        <?php while($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td>
                <a href="?user_id=<?php echo $row['user_id']; ?>&event_id=<?php echo $row['event_id']; ?>">
                    Mark Present
                </a>
            </td>
        </tr>
        <?php } ?>

    </table>

    <br>
    <a class="back" href="dashboard.php">Back Dashboard</a>

</div>

</body>
</html>