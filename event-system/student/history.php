<?php
session_start();
include("../includes/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // FIXED: Upgraded query syntax to PDO prepared statements
    // FIXED: Changed events.id to events.event_id to match your database schema
    $query = "
        SELECT events.title, events.description, events.event_date
        FROM registrations
        INNER JOIN events ON registrations.event_id = events.event_id
        WHERE registrations.user_id = :user_id
        ORDER BY events.event_date DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    
} catch (PDOException $e) {
    die("Error fetching your event history: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Event History - Student Portal</title>

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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <span class="font-bold text-lg tracking-tight">Academic History</span>
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
                    <h1 class="text-xl md:text-2xl font-bold tracking-tight">My Event History</h1>
                    <p class="text-blue-100/70 text-xs mt-1 font-light">Review the timeline of all your attended and completed campus event sessions.</p>
                </div>
                <span class="self-start sm:self-center bg-blue-500/20 text-blue-200 text-xs font-semibold px-3 py-1.5 rounded-full border border-blue-400/20">
                    Archived Logs
                </span>
            </div>

            <div class="overflow-x-auto">
                <?php if($stmt->rowCount() > 0): ?>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 text-xs font-semibold uppercase tracking-wider">
                                <th class="py-4 px-6">Event Title</th>
                                <th class="py-4 px-6 hidden md:table-cell">Description</th>
                                <th class="py-4 px-6 text-center">Date Attended</th>
                                <th class="py-4 px-6 text-center">Verification</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                            <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)){ ?>
                                <tr class="hover:bg-slate-50/60 transition duration-150">
                                    
                                    <td class="py-4 px-6 font-semibold text-slate-800">
                                        <?php echo htmlspecialchars($row['title']); ?>
                                        <p class="font-normal text-xs text-slate-500 mt-1 block md:hidden">
                                            <?php echo htmlspecialchars($row['description']); ?>
                                        </p>
                                    </td>
                                    
                                    <td class="py-4 px-6 max-w-xs hidden md:table-cell font-light leading-relaxed">
                                        <?php echo htmlspecialchars($row['description']); ?>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-center whitespace-nowrap font-medium text-slate-600">
                                        <span class="inline-flex items-center text-xs bg-slate-100 text-slate-700 px-2.5 py-1.5 rounded-lg border border-slate-200/50">
                                            <?php echo htmlspecialchars($row['event_date']); ?>
                                        </span>
                                    </td>

                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold bg-blue-50 text-blue-700 px-3 py-1 rounded-full border border-blue-200/30">
                                            Completed
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-16 px-4 bg-white">
                        <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m16.5 0a2.25 2.25 0 00-2.25-2.25h-1.5a1 1 0 00-.75-.35l-.863-1.151A1 1 0 0014.137 3.5H9.863a1 1 0 00-.742.349L8.258 5M20.25 7.5G18.72 7.5 15.75 6.75 12 6.75S5.28 7.5 3.75 7.5m9.375 3.375a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0zM12 18.75v-6.75" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-700">No Event History Found</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">You haven't participated in any campus workshops or completed events yet.</p>
                        <a href="dashboard.php" class="inline-block mt-5 bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition duration-200 shadow-sm">
                            Browse Upcoming Events
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="bg-slate-50/50 px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-400">Records are automatically synchronized with university activity logs.</p>
                <a href="dashboard.php" class="text-xs font-semibold text-blue-700 hover:text-blue-800 hover:underline">
                    Return to Home Panel
                </a>
            </div>

        </div>

    </main>

</body>
</html>