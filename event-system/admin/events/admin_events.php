<?php
session_start();

// Step out TWO levels (../../) to escape 'admin/events/' and hit project root
include("../../includes/db.php");

// Protect the route: ensure the user is logged in AND is an admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../../auth/login.php");
    exit();
}

// Check for status messages in the URL
$status_msg = "";
if(isset($_GET['status'])) {
    if($_GET['status'] == 'success') $status_msg = "Event successfully published!";
    if($_GET['status'] == 'updated') $status_msg = "Event successfully updated!";
    if($_GET['status'] == 'deleted') $status_msg = "Event successfully deleted!";
}

try {
    // FIXED: Converted the event selection array from MySQLi over to your active $pdo database connection
//  Fixed Line
    $query = $pdo->query("SELECT * FROM events ORDER BY event_id DESC");
    $events = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: Unable to fetch live events data. " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">

    <nav class="bg-gradient-to-r from-blue-900 to-slate-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="font-bold text-base">Event Management Dashboard</span>
            </div>
            <a href="../dashboard.php" class="bg-white/10 hover:bg-white/20 text-xs px-4 py-2.5 rounded-xl transition">← Back to Dashboard</a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-10">
        
        <?php if(!empty($status_msg)): ?>
            <div class="mb-6 max-w-4xl mx-auto bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
                ✨ <?php echo htmlspecialchars($status_msg); ?>
            </div>
        <?php endif; ?>

        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl border overflow-hidden">
            <div class="bg-gradient-to-r from-blue-700 to-slate-900 p-5 text-white flex justify-between items-center">
                <h1 class="text-base font-bold">Active Live Events Database</h1>
                <span class="bg-blue-500/20 text-xs px-3 py-1 rounded-full"><?php echo count($events); ?> Records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b text-slate-700 text-xs font-semibold uppercase">
                            <th class="py-4 px-4 text-center w-14">ID</th>
                            <th class="py-4 px-5">Event Details</th>
                            <th class="py-4 px-5 text-center">Date</th>
                            <th class="py-4 px-5 text-center">Status</th>
                            <th class="py-4 px-5 text-center w-32">Actions</th>
                        </tr>
                    </thead>
                 <tbody class="divide-y text-sm text-slate-600">
                        <?php if(count($events) > 0): ?>
                            <?php foreach($events as $row){ ?>
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-4 px-4 text-center font-mono text-xs text-slate-400">#<?php echo $row['event_id']; ?></td>
                                    <td class="py-4 px-5">
                                        <div class="font-bold text-slate-800"><?php echo htmlspecialchars($row['title']); ?></div>
                                        <div class="text-xs text-slate-500 line-clamp-2"><?php echo htmlspecialchars($row['description']); ?></div>
                                    </td>
                                    <td class="py-4 px-5 text-center whitespace-nowrap">
                                        <span class="bg-slate-100 text-slate-700 text-xs px-2.5 py-1 rounded-lg"><?php echo htmlspecialchars($row['event_date']); ?></span>
                                    </td>
                                    <td class="py-4 px-5 text-center">
                                        <span class="bg-emerald-50 text-emerald-700 text-xs px-2.5 py-1 rounded-full border"><?php echo htmlspecialchars($row['status'] ?? 'Open'); ?></span>
                                    </td>
                                    <td class="py-4 px-5 text-center whitespace-nowrap">
                                        <div class="inline-flex gap-2">
                                            <a class="text-xs bg-blue-50 text-blue-700 px-3 py-2 rounded-xl border" href="./admin_edit_event.php?id=<?php echo $row['event_id']; ?>">Edit</a>
                                            <a class="text-xs bg-red-50 text-red-600 px-3 py-2 rounded-xl border" href="./admin_delete_event.php?id=<?php echo $row['event_id']; ?>" onclick="return confirm('Are you sure you want to delete this event record?')">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-12 text-slate-400 text-xs">No active events found in database profile.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>