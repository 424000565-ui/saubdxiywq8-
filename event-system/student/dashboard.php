<?php
session_start();
include("../includes/db.php"); // Connects to your upgraded database file

// Route Guard: Ensure the user is logged in AND is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// 1. Handle Free-Will Registration Action when user clicks "Register Event"
if (isset($_POST['register_event'])) {
    $event_id = $_POST['event_id'];
    
    try {
        // Check if student is already registered for this event
        $check = $pdo->prepare("SELECT * FROM registrations WHERE event_id = ? AND user_id = ?");
        $check->execute([$event_id, $user_id]);
        
        if ($check->rowCount() == 0) {
            // Insert a fresh registration entry
            $insert = $pdo->prepare("INSERT INTO registrations (event_id, user_id) VALUES (?, ?)");
            $insert->execute([$event_id, $user_id]);
            $success_msg = "Successfully registered for the event!";
        } else {
            $error_msg = "You are already registered for this event.";
        }
    } catch (PDOException $e) {
        $error_msg = "Registration failed: " . $e->getMessage();
    }
}

// 2. Fetch all events for display
try {
    $query = "SELECT * FROM events ORDER BY event_date ASC";
    $stmt = $pdo->query($query);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Track which events this student has already joined to change button statuses
    $reg_query = $pdo->prepare("SELECT event_id FROM registrations WHERE user_id = ?");
    $reg_query->execute([$user_id]);
    $my_registrations = $reg_query->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Database Error on Dashboard: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Event Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">

    <nav class="bg-gradient-to-r from-blue-900 to-slate-900 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-600 p-2 rounded-lg text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <span class="font-bold text-lg tracking-tight hidden sm:block">Campus Event Pass</span>
                </div>

                <div class="flex items-center space-x-2 md:space-x-4 text-sm font-medium">
                    <a href="my_events.php" class="bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg transition duration-200">
                        My Events
                    </a>
                    <a href="history.php" class="bg-white/10 hover:bg-white/20 px-3 py-2 rounded-lg transition duration-200">
                        History
                    </a>
                    <a href="../auth/logout.php" class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded-lg transition duration-200 shadow-md shadow-red-900/20 font-semibold">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="bg-gradient-to-br from-blue-700 via-blue-800 to-slate-900 text-white rounded-2xl p-6 md:p-8 shadow-xl mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                    Good Day, <?php echo htmlspecialchars($_SESSION['name']); ?>!
                </h1>
                <p class="text-blue-100/80 text-sm mt-1 font-light">
                    We're Excited To Have You! Browse and Register for Upcoming Campus Events Below.
                </p>
            </div>
            <div class="bg-white/10 px-4 py-2 rounded-xl border border-white/10 backdrop-blur-sm text-xs text-blue-200">
                Logged in as <span class="text-white font-semibold capitalize"><?php echo htmlspecialchars($_SESSION['role'] ?? 'Student'); ?></span>
            </div>
        </div>

        <?php if(!empty($success_msg)): ?>
            <div class="p-4 mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
                ✅ <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div class="p-4 mb-6 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium">
                ❌ <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Available Events</h2>
                <span class="text-xs font-semibold bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full">
                    Ongoing & Upcoming Events 
                </span>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $row): ?>
                        <?php $is_registered = in_array($row['event_id'], $my_registrations); ?>
                        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between items-start gap-4 md:flex-row md:items-center">
                            <div class="space-y-2 max-w-3xl">
                                <h3 class="text-lg font-bold text-slate-800 tracking-tight">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </h3>
                                <p class="text-sm text-slate-600 font-light leading-relaxed">
                                    <?php echo htmlspecialchars($row['description']); ?>
                                </p>
                                <div class="inline-flex items-center text-xs font-semibold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg border border-slate-200/60">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1.5 text-blue-700">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Date: <span class="ml-1 text-slate-900"><?php echo date("F d, Y", strtotime($row['event_date'])); ?></span>
                                </div>
                            </div>
                            
                            <div class="w-full md:w-auto flex-shrink-0">
                                <?php if($is_registered): ?>
                                    <button disabled class="w-full md:w-auto text-center bg-emerald-100 text-emerald-700 font-semibold text-sm px-5 py-3 rounded-xl border border-emerald-200 cursor-not-allowed">
                                        ✓ Registered
                                    </button>
                                <?php else: ?>
                                    <form action="" method="POST">
                                        <input type="hidden" name="event_id" value="<?php echo $row['event_id']; ?>">
                                        <button type="submit" name="register_event" class="w-full md:w-auto text-center bg-blue-700 hover:bg-blue-800 text-white text-sm font-medium px-5 py-3 rounded-xl transition duration-200 shadow-md shadow-blue-700/10 cursor-pointer">
                                            Register Event
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-12 bg-white rounded-2xl border border-dashed border-slate-300">
                        <p class="text-slate-500 text-sm">No campus events are currently scheduled. Check back soon!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>