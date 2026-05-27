<?php
session_start();

// Load your clean processing filter logic seamlessly 
include("registrants_filter.php"); 
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrants List - Event Gateway</title>
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
                    <h2 class="text-sm font-bold text-white tracking-wide uppercase">Event Gateway</h2>
                    <p class="text-xs text-slate-500">Admin Control Panel</p>
                </div>
            </div>

            <nav class="p-4 space-y-1">
                <a href="/event_system/event-system/admin/dashboard.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
                    Dashboard
                </a>
                <a href="/event_system/event-system/admin/events/admin_events.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl hover:bg-slate-800 hover:text-white text-slate-400 transition">
                    Manage Events
                </a>
                <a href="/event_system/event-system/admin/registrants/registrants.php" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl bg-slate-800 text-white transition">
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
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Registrant Viewer</h1>
            
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs text-slate-400 font-medium tracking-wide uppercase">System Access</p>
                    <p class="text-sm font-semibold text-slate-800">Administrator</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                    ADM
                </div>
            </div>
        </header>

        <main class="p-8 max-w-7xl w-full mx-auto space-y-8">

            <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-slate-900 text-lg">Student Event Registrations</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Monitoring live seat placements across the campus network.</p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <form method="GET" class="flex items-center gap-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Filter by Event:</label>
                            <select 
                                name="event_id" 
                                onchange="this.form.submit()" 
                                class="bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-sm font-medium focus:outline-none focus:border-indigo-500 cursor-pointer shadow-sm"
                            >
                                <option value="0">All Events</option>
                                <?php 
                                if (isset($events_result)) {
                                    while($event = $events_result->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($selected_event_id == $event['event_id']) ? 'selected' : '';
                                        echo "<option value='{$event['event_id']}' {$selected}>" . htmlspecialchars($event['title']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-semibold bg-slate-50/20">
                                <th class="p-4 pl-6">Student Name</th>
                                <th class="p-4">Email Address</th>
                                <th class="p-4">Assigned Event Workshop Slot</th>
                                <th class="p-4">Registration Date</th>
                                <th class="p-4 pr-6 text-center">Attendance Verification Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            <?php 
                            if (!empty($registrants)) {
                                foreach ($registrants as $row) { 
                            ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 pl-6 font-semibold text-slate-900"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="p-4 text-slate-500"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="p-4 font-medium text-indigo-600"><?php echo htmlspecialchars($row['title']); ?></td>
                                <td class="p-4 text-slate-400">
                                    <?php echo isset($row['registration_date']) ? date("M d, Y", strtotime($row['registration_date'])) : 'N/A'; ?>
                                </td>
                                
                                <td class="p-4 pr-6 text-center">
                                    <?php if (isset($row['attendance_status']) && $row['attendance_status'] === 'Present'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 text-emerald-700 font-bold text-xs rounded-lg border border-emerald-200/60 shadow-sm">
                                            ✅ Verified Present
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-amber-50 text-amber-700 font-bold text-xs rounded-lg border border-amber-200/60 shadow-sm">
                                            ⏳ Registered / Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                } 
                            } else { 
                            ?>
                            <tr>
                                <td colspan="5" class="p-8 text-center font-medium text-slate-400 bg-slate-50/10">No records found matching current criteria.</td>
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