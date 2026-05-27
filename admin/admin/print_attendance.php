<?php
include("../includes/db.php");

$query = "
SELECT users.name,
       events.title
FROM attendance
JOIN users ON attendance.user_id = users.id
JOIN events ON attendance.event_id = events.id
ORDER BY attendance.id DESC
";

$result = mysqli_query($conn,$query);
?>

<!DOCTYPE html>
<html>

<head>

<title>Print Attendance</title>

<style>

body{
    font-family:Arial;
    padding:20px;
    background:#f5f5f5;
}

.container{
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0px 0px 10px gray;
}

h1{
    text-align:center;
    color:orange;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table,th,td{
    border:1px solid black;
}

th,td{
    padding:12px;
    text-align:center;
}

button{
    padding:10px 20px;
    background:orange;
    color:white;
    border:none;
    cursor:pointer;
    margin-bottom:20px;
    border-radius:5px;
}

button:hover{
    background:#cc6600;
}

@media print{

    button{
        display:none;
    }

}

</style>

</head>

<body>

<div class="container">

<button onclick="window.print()">
    Print Attendance
</button>

<h1>Attendance Records</h1>

<table>

<tr>
<th>Student</th>
<th>Event</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['title']; ?></td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>