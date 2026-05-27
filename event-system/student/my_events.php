<?php
session_start();
include("../includes/db.php");

// Protect route: Ensure student is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // FIXED: Changed r.student_id to r.user_id to match your unified schema
    // FIXED: Added e.description to the SELECT statement so it displays in your table layout
    $query = "
        SELECT e.title, e.description, e.event_date, e.event_time, e.venue 
        FROM registrations r
        INNER JOIN events e ON r.event_id = e.event_id
        WHERE r.user_id = :user_id
        ORDER BY e.event_date ASC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    
} catch (PDOException $e) {
    die("Error fetching your events: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - Student Portal</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">

    <nav class="bg-gradient-to-r from-blue-900 to-slate-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-600 p-2 rounded-lg text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="font-bold text-lg tracking-tight">Student Schedule</span>
                </div>
                <div>
                    <a href="dashboard.php" class="bg-white/10 hover:bg-white/20 text-sm font-medium px-4 py-2 rounded-lg transition duration-200 inline-flex items-center gap-1.5">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200/60 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-slate-900 p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold tracking-tight">My Registered Events</h1>
                    <p class="text-blue-100/70 text-xs mt-1 font-light">Keep track of your confirmed workshops, academic lectures, and system event slots.</p>
                </div>
                <span class="self-start sm:self-center bg-blue-500/20 text-blue-200 text-xs font-semibold px-3 py-1.5 rounded-full border border-blue-400/20">
                    Confirmed Registrations
                </span>
            </div>

            <div class="overflow-x-auto">
                <?php if ($stmt->rowCount() > 0): ?>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 text-xs font-semibold uppercase tracking-wider">
                                <th class="py-4 px-6">Event Title</th>
                                <th class="py-4 px-6 hidden md:table-cell">Description</th>
                                <th class="py-4 px-6 text-center">Date</th>
                                <th class="py-4 px-6 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                            <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)){ ?>
                                <tr class="hover:bg-slate-50/80 transition duration-150">
                                    <td class="py-4 px-6 font-semibold text-slate-800">
                                        <?php echo htmlspecialchars($row['title']); ?>
                                        <p class="font-normal text-xs text-slate-500 mt-1 block md:hidden">
                                            <?php echo htmlspecialchars($row['description'] ?? 'No description provided.'); ?>
                                        </p>
                                    </td>
                                    
                                    <td class="py-4 px-6 max-w-sm hidden md:table-cell font-light leading-relaxed">
                                        <?php echo htmlspecialchars($row['description'] ?? 'No description provided.'); ?>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-center whitespace-nowrap font-medium text-slate-700">
                                        <span class="inline-flex items-center text-xs bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md border border-slate-200/40">
                                            <?php echo htmlspecialchars($row['event_date']); ?>
                                        </span>
                                    </td>

                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 text-xs font-medium bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full border border-emerald-200/50">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                            Active Pass
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-16 px-4 bg-white">
                        <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-700">No events found</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">You have not registered for any upcoming events yet. Check out the dashboard to browse active schedules.</p>
                        <a href="dashboard.php" class="inline-block mt-5 bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition duration-200">
                            Explore Events
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </main>

</body>
</html>