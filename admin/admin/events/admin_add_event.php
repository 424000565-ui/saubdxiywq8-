<?php
session_start();

// Step out TWO levels (../../) to escape 'admin/events/' and hit project root
include("../../includes/db.php");

// Protect the route: ensure the user is logged in AND is an admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../../auth/login.php");
    exit();
}

$success_msg = false;
$error_msg = false;

if(isset($_POST['add_event'])){

    // 1. Grab inputs from your form fields
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    
    // 2. Safely capture the form inputs for time and venue
    $event_time = !empty($_POST['event_time']) ? $_POST['event_time'] : '00:00:00';
    $venue = !empty($_POST['venue']) ? $_POST['venue'] : 'School Campus';

    try {
        // 3. Prepare the safe PDO insert query
        $insert = "INSERT INTO events (title, description, event_date, event_time, venue, status)
                   VALUES (:title, :description, :event_date, :event_time, :venue, 'Open')";
        
        // CHANGED: Successfully points to your unified $pdo connection object
        $stmt = $pdo->prepare($insert);
        
        // 4. Safely execute with form data
        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':event_date'  => $event_date,
            ':event_time'  => $event_time,
            ':venue'       => $venue
        ]);

        // Route cleanly back to management screen with success parameters
        header("Location: admin_events.php?status=success");
        exit();

    } catch (PDOException $e) {
        $error_msg = "Database Error: Unable to publish your event session. " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Event - System Admin Portal</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen text-slate-800 flex flex-col">

    <nav class="bg-[#1e293b] bg-gradient-to-r from-[#1e3a8a] to-[#0f172a] text-white shadow-lg border-b border-blue-900/40">
        <div class="max-w-4xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-600 p-2 rounded-xl text-white shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-sm md:text-base tracking-tight block">Event Configuration</span>
                        <span class="text-[10px] text-blue-400 font-medium tracking-wider uppercase block">System Admin Panel</span>
                    </div>
                </div>
                <div>
                    <a href="admin_events.php" class="bg-white/5 hover:bg-white/15 text-slate-200 text-xs font-medium px-4 py-2.5 rounded-xl transition duration-200 inline-flex items-center gap-1.5 border border-white/10">
                        ← View All Events
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-2xl w-full mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/80 border border-slate-200/70 overflow-hidden">
            
            <div class="bg-gradient-to-r from-[#1e3a8a] to-[#1e293b] p-6 text-white">
                <h1 class="text-xl font-bold tracking-tight">Add New Campus Event</h1>
                <p class="text-blue-200/70 text-xs mt-1 font-light">
                    Input your structural event criteria profiles. Once published, slots immediately load onto live client data-feeds.
                </p>
            </div>

            <?php if($error_msg): ?>
                <div class="bg-red-50 border-b border-red-200 p-4 text-sm text-red-600 font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 flex-shrink-0 text-red-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="p-6 md:p-8 space-y-5">
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Event Title Name</label>
                    <input type="text" name="title" placeholder="e.g., UI/UX Design Masterclass Workshop" required
                           class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600/15 focus:border-blue-600 transition outline-none text-slate-800 font-medium placeholder:text-slate-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Detailed Event Description</label>
                    <textarea name="description" placeholder="Provide complete seminar details, venue locations, and target developer tracks..." rows="5" required
                              class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600/15 focus:border-blue-600 transition outline-none text-slate-700 font-light resize-none leading-relaxed placeholder:text-slate-400"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Target Event Date</label>
                        <input type="date" name="event_date" required
                               class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600/15 focus:border-blue-600 transition outline-none text-slate-800 font-medium text-slate-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Event Time</label>
                        <input type="time" name="event_time" required
                               class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600/15 focus:border-blue-600 transition outline-none text-slate-800 font-medium text-slate-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Venue / Location</label>
                    <input type="text" name="venue" placeholder="e.g., Main Auditorium, Building B" required
                           class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600/15 focus:border-blue-600 transition outline-none text-slate-800 font-medium placeholder:text-slate-400">
                </div>

                <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 mt-8">
                    <a href="../dashboard.php" class="text-xs text-slate-400 hover:text-slate-600 transition font-medium underline order-2 sm:order-1">
                        Cancel and Go Back
                    </a>
                    
                    <button type="submit" name="add_event"
                            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-3.5 rounded-xl transition duration-200 shadow-md shadow-blue-600/10 inline-flex items-center justify-center gap-2 order-1 sm:order-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    <a href="../dashboard.php" class="text-white font-semibold text-slate-400 hover:text-slate-600 transition font-medium order-2 sm:order-1">
                        Publish Event Session
                    </a>
                    </button>
                </div>

            </form>
        </div>
    </main>

</body>
</html>