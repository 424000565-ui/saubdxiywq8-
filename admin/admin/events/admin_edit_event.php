<?php
session_start();
include("../../includes/db.php");

// Route protection: ensure user is validated and logged in
if(!isset($_SESSION['user_id'])){
    header("Location: ../../auth/login.php");
    exit();
}

if(!isset($_GET['id'])){
    die("No Event ID Found");
}

$id = $_GET['id'];

try {
    // FIXED: Converted selection logic from MySQLi to clean, safe PDO syntax
    $query = "SELECT * FROM events WHERE event_id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

if(!$event){
    die("Event not found");
}

$error_msg = false;

if(isset($_POST['update'])){

    // Grab input variables cleanly from post profiles
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];

    try {
        // FIXED: Converted update engine sequence over to a secure PDO prepared statement query structure
        $update = "UPDATE events 
                   SET title = :title,
                       description = :description,
                       event_date = :date
                   WHERE event_id = :id";

        $stmt = $pdo->prepare($update);
        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':date'        => $date,
            ':id'          => $id
        ]);

        // Clean redirection back to event index grid upon modification success
        header("Location: admin_events.php?status=updated");
        exit();

    } catch (PDOException $e) {
        $error_msg = "Database Error: Could not update event data profile. " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event Settings - System Admin Panel</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen text-slate-800 flex flex-col">

    <nav class="bg-[#1e293b] bg-gradient-to-r from-[#1e3a8a] to-[#0f172a] text-white shadow-lg border-b border-blue-900/40">
        <div class="max-w-4xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-600 p-2 rounded-xl text-white shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-sm md:text-base tracking-tight block">Modifier Workspace</span>
                        <span class="text-[10px] text-blue-400 font-medium tracking-wider uppercase block">System Admin Panel</span>
                    </div>
                </div>
                <div>
                    <a href="admin_events.php" class="bg-white/5 hover:bg-white/15 text-slate-200 text-xs font-medium px-4 py-2.5 rounded-xl transition duration-200 inline-flex items-center gap-1.5 border border-white/10">
                        ← Cancel Changes
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-2xl w-full mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/80 border border-slate-200/70 overflow-hidden">
            
            <div class="bg-gradient-to-r from-[#1e3a8a] to-[#1e293b] p-6 text-white flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold tracking-tight">Edit Event Properties</h1>
                    <p class="text-blue-200/70 text-xs mt-1 font-light">
                        Modifying active database specifications for record index reference profile entries.
                    </p>
                </div>
                <span class="bg-blue-500/20 text-blue-300 font-mono text-xs font-semibold px-3 py-1.5 rounded-xl border border-blue-400/20 shadow-inner">
                    Record #<?php echo htmlspecialchars($event['event_id']); ?>
                </span>
            </div>

            <?php if($error_msg): ?>
                <div class="bg-red-50 border-b border-red-200 p-4 text-sm text-red-600 font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 flex-shrink-0 text-red-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="admin_edit_event.php?id=<?php echo htmlspecialchars($id); ?>" class="p-6 md:p-8 space-y-5">
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Event Title Name</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required
                           class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600/15 focus:border-blue-600 transition outline-none text-slate-800 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Detailed Event Summary Description</label>
                    <textarea name="description" rows="5" required
                              class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600/15 focus:border-blue-600 transition outline-none text-slate-700 font-light resize-none leading-relaxed"><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>

                <div class="max-w-xs">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Target Event Date</label>
                    <input type="date" name="date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required
                           class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-600/15 focus:border-blue-600 transition outline-none text-slate-800 font-semibold text-slate-600">
                </div>

                <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 mt-8">
                    <a href="admin_events.php" class="text-xs text-slate-400 hover:text-slate-600 transition font-medium underline order-2 sm:order-1">
                        Discard Changes and Return
                    </a>
                    
                    <button type="submit" name="update"
                            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-3.5 rounded-xl transition duration-200 shadow-md shadow-blue-600/10 inline-flex items-center justify-center gap-2 order-1 sm:order-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Save Record Modifications
                    </button>
                </div>

            </form>

        </div>
    </main>

</body>
</html>