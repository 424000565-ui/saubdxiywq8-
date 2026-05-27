<?php
session_start();

include("../../includes/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../../auth/login.php");
    exit();
}

if(!isset($_GET['id'])){
    die("No Event ID");
}

$id = $_GET['id'];

try {
    // FIXED: Converted event data selection to PDO prepared statement using $pdo
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

if(!$row) {
    die("Event not found");
}

if(isset($_POST['update'])){

    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];

    try {
        // FIXED: Converted UPDATE operation to PDO prepared statement execution
        $updateQuery = "UPDATE events
                        SET title = :title,
                            description = :description,
                            event_date = :event_date
                        WHERE id = :id";
        
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':event_date'  => $event_date,
            ':id'          => $id
        ]);

        echo "
        <script>
            alert('Event Updated!');
            window.location='admin_events.php';
        </script>
        ";
        exit();

    } catch (PDOException $e) {
        die("Database Error: Could not update event. " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Event</title>

<style>

body{
    font-family:Arial;
    background:#f5f5f5;
}

.container{
    width:600px;
    margin:auto;
    margin-top:50px;
    background:white;
    padding:30px;
    border-radius:10px;
}

h1{
    text-align:center;
    color:orange;
}

input, textarea{
    width:100%;
    padding:10px;
    margin-top:10px;
}

button{
    padding:10px 20px;
    background:orange;
    color:white;
    border:none;
    margin-top:15px;
    cursor:pointer;
}

</style>

</head>

<body>

<div class="container">

<h1>Edit Event</h1>

<form method="POST">

<input
type="text"
name="title"
value="<?php echo htmlspecialchars($row['title']); ?>"
required
>

<textarea
name="description"
required
><?php echo htmlspecialchars($row['description']); ?></textarea>

<input
type="date"
name="event_date"
value="<?php echo htmlspecialchars($row['event_date']); ?>"
required
>

<button type="submit" name="update">
Update Event
</button>

</form>

</div>

</body>
</html>