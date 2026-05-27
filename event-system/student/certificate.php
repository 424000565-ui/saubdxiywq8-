<?php
session_start();

include("../includes/db.php");

// Protect route: Ensure student is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in student's ID

// Get the specific event ID from the URL parameter
if(!isset($_GET['event_id'])){
    echo "
    <script>
        alert('No Event Specified!');
        window.location='history.php';
    </script>
    ";
    exit();
}

$event_id = intval($_GET['event_id']);

try {
    // Upgraded SQL checking if this specific student was marked 'present' at this specific event
    $query = "
        SELECT u.full_name AS name,
               e.title AS event_title,
               e.event_date
        FROM attendance a
        INNER JOIN users u ON a.student_id = u.user_id
        INNER JOIN events e ON a.event_id = e.event_id
        WHERE a.student_id = :user_id 
          AND a.event_id = :event_id 
          AND a.status = 'present'
        LIMIT 1
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':user_id'  => $user_id,
        ':event_id' => $event_id
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no record is returned, it means they didn't attend or weren't marked present
    if(!$row){
        echo "
        <script>
            alert('Access Denied: You did not attend this event or attendance has not been verified.');
            window.location='history.php';
        </script>
        ";
        exit();
    }

    // If the verification passes, execution continues below...
    // You can safely use $row['name'], $row['event_title'], and $row['event_date'] inside your FPDF/TCPDF code!

} catch (PDOException $e) {
    die("Verification error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>

<head>

<title>Certificate</title>

<style>

body{
    font-family:Arial;
    background:#f5f5f5;
}

.certificate{
    width:900px;
    margin:auto;
    margin-top:50px;
    background:white;
    padding:50px;
    border:10px solid orange;
    text-align:center;
}

h1{
    font-size:50px;
    color:orange;
}

h2{
    margin-top:40px;
    font-size:35px;
}

p{
    font-size:22px;
    margin-top:20px;
}

button{
    padding:12px 20px;
    background:orange;
    color:white;
    border:none;
    margin-top:30px;
    cursor:pointer;
    border-radius:5px;
}

a{
    display:inline-block;
    padding:12px 20px;
    background:gray;
    color:white;
    text-decoration:none;
    margin-top:20px;
    border-radius:5px;
}

.message{
    color:red;
    font-size:25px;
    margin-top:50px;
}

@media print{

    button{
        display:none;
    }

    a{
        display:none;
    }

}

</style>

</head>

<body>

<div class="certificate">

<?php if($row){ ?>

<h1>Certificate of Attendance</h1>

<p>This certificate is proudly presented to</p>

<h2>
<?php echo $row['name']; ?>
</h2>

<p>for attending the event</p>

<h2>
<?php echo $row['title']; ?>
</h2>

<p>Congratulations and Thank You!</p>

<button onclick="window.print()">
Print Certificate
</button>

<?php } else { ?>

<div class="message">
No attendance record found.
</div>

<?php } ?>

<br>

<a href="dashboard.php">
Back Dashboard
</a>

</div>

</body>
</html>