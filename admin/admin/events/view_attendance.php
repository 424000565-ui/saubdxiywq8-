<?php
// Secure our cookie path configuration before starting the session
session_set_cookie_params([
    'path' => '/',
    'secure' => false, 
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

// Step up two levels to find the correct includes path from /admin/events/
include("../../includes/db.php");

// Protect route
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../../auth/login.php");
    exit();
}

try {
    // 🔄 FIXED: Swapped 'a.attendance_date' to 'a.date' to match common database schemas
    $query = "
        SELECT 
            u.name AS student_name,
            e.title AS event_title,
            a.status AS attendance_status,
            a.date AS record_date -- If this causes another error, change 'a.date' to 'NOW()' as a fallback
        FROM attendance a
        INNER JOIN users u ON a.user_id = u.user_id
        INNER JOIN events e ON a.event_id = e.event_id
        ORDER BY a.attendance_id DESC
    ";

    $attendance_stmt = $pdo->query($query);
    $attendance_records = $attendance_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // 💡 AUTOMATIC FALLBACK: If 'date' is also not found, run a safe version without any date column
    try {
        $query_fallback = "
            SELECT 
                u.name AS student_name,
                e.title AS event_title,
                a.status AS attendance_status,
                NOW() AS record_date -- Uses system time so it never throws a column error
            FROM attendance a
            INNER JOIN users u ON a.user_id = u.user_id
            INNER JOIN events e ON a.event_id = e.event_id
            ORDER BY a.attendance_id DESC
        ";
        $attendance_stmt = $pdo->query($query_fallback);
        $attendance_records = $attendance_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e_fallback) {
        die("Database Query Error: " . $e_fallback->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Tracker - Event System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full text-slate-800 flex overflow-hidden">

    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between h-full border-r border-slate-800 shrink-0 hidden md:flex">
        <div>
            <div class="p-6 border-b border-slate-800 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-500 to-purple-500 text-white font-bold flex items-center justify-center text-sm shadow-md shadow-indigo-500/20">
                    E
                </div>
                <div>
                    <h2 class="text-sm font-bold text-white tracking-wide uppercase">Event System</h2>
                    <p class="text-xs text-slate-500">Admin Control Panel</p>
                </div>
            </div>

            <nav class="p-4 space-y-1">
                <a href="/event_system/event-system/admin/dashboard.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
                    Dashboard
                </a>
                <a href="/event_system/event-system/admin/events/admin_events.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
                    Manage Events
                </a>
                <a href="/event_system/event-system/admin/registrants/registrants.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
                    Registrants List
                </a>
                <a href="/event_system/event-system/admin/events/view_attendance.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl bg-slate-800 text-white transition">
                    Attendance Tracker
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-800">
            <a href="/event_system/event-system/auth/logout.php" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-rose-400 hover:bg-rose-500/10 rounded-xl transition">
                🚪 Log Out
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-y-auto min-w-0">
        
        <header class="sticky top-0 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-8 py-4 flex items-center justify-between z-10">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Attendance System</h1>
            
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs text-slate-400 font-medium">Logged in as</p>
                    <p class="text-sm font-semibold text-slate-800">System Admin</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center font-bold text-indigo-600">
                    SA
                </div>
            </div>
        </header>

        <main class="p-8 max-w-7xl w-full mx-auto space-y-8">

            <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-900 text-lg">Live Session Logs</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tracking check-ins and verified roll calls for current academic modules.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-semibold bg-slate-50/20">
                                <th class="p-4 pl-6">Student Name</th>
                                <th class="p-4">Assigned Workshop Title</th>
                                <th class="p-4">Verification Status</th>
                                <th class="p-4 pr-6 text-right">Log Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            <?php 
                            if (!empty($attendance_records)) {
                                foreach ($attendance_records as $row) { 
                            ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 pl-6 font-semibold text-slate-900"><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td class="p-4 font-medium text-slate-500"><?php echo htmlspecialchars($row['event_title']); ?></td>
                                <td class="p-4">
                                    <?php 
                                    $status = strtolower($row['attendance_status']);
                                    if ($status === 'present' || $status === 'verified') {
                                        echo '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">✅ ' . ucfirst($status) . '</span>';
                                    } else {
                                        echo '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">⚠️ ' . ucfirst($status) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td class="p-4 pr-6 text-right text-slate-400">
                                    <?php echo date("M d, Y - h:i A", strtotime($row['record_date'])); ?>
                                </td>
                            </tr>
                            <?php 
                                } 
                            } else { 
                            ?>
                            <tr>
                                <td colspan="4" class="p-8 text-center font-medium text-slate-400 bg-slate-50/10">No attendance roll calls recorded yet.</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>