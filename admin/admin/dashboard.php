<?php
session_start();
include("../includes/db.php");

// Route Guard: Ensure the user is logged in AND is an admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

try {
    // 1. Get Live Stats
    $total_events = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    $total_registrants = $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
    
    // Calculate Attendance Percentage
    $total_attendance = $pdo->query("SELECT COUNT(*) FROM attendance WHERE status = 'present'")->fetchColumn();
    $attendance_rate = $total_registrants > 0 ? round(($total_attendance / $total_registrants) * 100, 1) : 0;

    // 2. Fetch Events for the Table
    // Using event_id and matching your table columns
    $query = "
        SELECT e.event_id, e.title, e.event_date,
               (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id) AS reg_count
        FROM events e 
        ORDER BY e.event_date ASC
    ";
    $result = $pdo->query($query);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Event System</title>
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
                <a href="/event_system/event-system/admin/events/dashboard.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl bg-slate-800 text-white transition">
                    Dashboard
                </a>
                <a href="/event_system/event-system/admin/events/admin_events.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 transition">
                    Manage Events
                </a>
                <a href="/event_system/event-system/admin/registrants/registrants.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 transition">
                    Registrants List
                </a>
                <a href="/event_system/event-system/admin/events/view_attendance.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 transition">
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
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Overview Dashboard</h1>
            
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

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm shadow-slate-100/50 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Active Events</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1"><?php echo $total_events; ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-medium text-xl"></div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm shadow-slate-100/50 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Registrants</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1"><?php echo number_format($total_registrants); ?></h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-medium text-xl"></div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm shadow-slate-100/50 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Average Attendance</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1"><?php echo $attendance_rate; ?>%</h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-medium text-xl"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-slate-900">Upcoming Live Events</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Quick look at current registrations</p>
                    </div>
                    <a href="/event_system/event-system/admin/events/admin_add_event.php" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-xl text-sm hover:opacity-95 shadow-md shadow-indigo-100 transition cursor-pointer">
                        + Create Event
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-semibold bg-slate-50/20">
                                <th class="p-4 pl-6">Event Name</th>
                                <th class="p-4">Date</th>
                                <th class="p-4">Registrations</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 pr-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            <?php while($row = $result->fetch(PDO::FETCH_ASSOC)){ ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 pl-6 font-semibold text-slate-900"><?php echo htmlspecialchars($row['title']); ?></td>
                                <td class="p-4 text-slate-500"><?php echo date("F d, Y", strtotime($row['event_date'])); ?></td>
                                <td class="p-4 font-medium"><?php echo $row['reg_count']; ?> Users</td>
                                <td class="p-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/50">Open</span>
                                </td>
                                <td class="p-4 pr-6 text-right space-x-2">
                                    <a href="/event_system/event-system/admin/events/admin_edit_event.php?id=<?php echo $row['event_id']; ?>" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Edit</a>
                                    <a href="/event_system/event-system/admin/events/admin_delete_event.php?id=<?php echo $row['event_id']; ?>" onclick="return confirm('Are you sure you want to delete this event record?')" class="text-rose-600 hover:text-rose-900 font-semibold text-xs">Delete</a>
                                </td>
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