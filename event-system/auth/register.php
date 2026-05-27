<?php
include "../includes/db.php";

// State variable to handle showing our custom popup design
$registrationSuccess = false;
$error_message = "";

if (isset($_POST['register'])) {

    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    $allowed_domain = '@student.edu'; // Your campus domain lock
    
    // FIX 1: Corrected bracket scoping logic
    if (substr($email, -strlen($allowed_domain)) !== $allowed_domain) {
        // Soft error message so the layout doesn't crash via die()
        $error_message = "Registration restricted: Please use an official school email address.";
    } else {
        // 1. Securely hash the password before saving it to the database
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // 2. School metadata fields matching incoming POST arrays
        $student_id_number = isset($_POST['student_id_number']) ? $_POST['student_id_number'] : null;
        $course_year_section = isset($_POST['course_year_section']) ? $_POST['course_year_section'] : null;

        try {
            // 3. Using Prepared Statements to prevent SQL Injection attacks
            // FIX 2: Corrected database field mapping name definitions
            $sql = "INSERT INTO users (student_id_number, full_name, email, password, role, course_year_section)
                    VALUES (:student_id_number, :full_name, :email, :password, 'student', :course_year_section)";
            
            $stmt = $pdo->prepare($sql);
            
            // 4. Bind the parameters safely
            $stmt->execute([
                ':student_id_number'   => $student_id_number,
                ':full_name'           => $name, // Correctly linked to form name data
                ':email'               => $email,
                ':password'            => $password,
                ':course_year_section' => $course_year_section
            ]);

            // If execution reaches here, the insert was successful!
            $registrationSuccess = true;

        } catch (PDOException $e) {
            // Handle registration errors smoothly (e.g., Duplicate email or ID check)
            if ($e->getCode() == 23000) {
                $error_message = "This Student ID or Email address is already registered.";
            } else {
                $error_message = "Registration system encounter: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Student Account - Event System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 relative">

    <div class="w-full max-w-5xl min-h-[550px] bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row text-left">
        
        <div class="w-full md:w-1/2 bg-gradient-to-br from-blue-700 via-blue-800 to-slate-900 p-10 flex flex-col justify-between text-white">
            <div>
                <a href="../index.php" class="inline-flex items-center text-xs font-medium bg-white/10 hover:bg-white/20 text-blue-200 hover:text-white px-3 py-1.5 rounded-full transition duration-200 mb-6">
                    ← Back to Home
                </a>
                <h1 class="text-3xl md:text-4xl font-bold leading-tight mt-4 tracking-tight">
                    Join the Campus Community!
                </h1>
                <p class="text-blue-100/80 mt-4 text-sm font-light leading-relaxed max-w-sm">
                    Create your account in seconds to seamlessly register for workshops, reserve event tickets, and track your campus activities history.
                </p>
            </div>
            
            <div class="mt-8 pt-6 border-t border-white/10 hidden md:block">
                <p class="text-xs text-blue-200/70">Fast, Automated Registration</p>
            </div>
        </div>

        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-white">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Create Account</h2>
                <p class="text-sm text-slate-500 mt-1">Please fill in your details to establish a student profile.</p>
            </div>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
                
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Full Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required 
                           placeholder="e.g., Alex Mercer" 
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 transition duration-200 text-sm text-slate-800">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           placeholder="yourname@student.edu" 
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 transition duration-200 text-sm text-slate-800">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           placeholder="••••••••" 
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20 transition duration-200 text-sm text-slate-800">
                </div>

                <div class="flex items-start pt-1">
                    <input type="checkbox" id="terms" required class="mt-0.5 h-4 w-4 text-blue-700 focus:ring-blue-700 border-slate-300 rounded">
                    <label for="terms" class="ml-2 block text-xs text-slate-500 user-select-none">
                        I agree to the university's campus event policies and code of conduct.
                    </label>
                </div>

                <button type="submit" 
                        name="register"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white font-medium py-3 px-4 rounded-xl transition duration-200 shadow-lg shadow-blue-700/10 hover:shadow-blue-700/20 transform hover:-translate-y-0.5 mt-4 cursor-pointer">
                    Register Account
                </button>
            </form>

            <div class="mt-6 text-center border-t border-slate-100 pt-4">
                <p class="text-xs text-slate-500">
                    Already have a student account? 
                    <a href="login.php" class="text-blue-700 hover:underline font-semibold ml-1">
                        Sign In here
                    </a>
                </p>
            </div>
        </div>

    </div>

    <?php if ($registrationSuccess): ?>
    <div id="successModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-2xl border border-slate-100 transform scale-100 transition-transform duration-300">
            
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>

            <h3 class="text-xl font-bold text-slate-800">Successfully!</h3>
            <p class="text-xs text-slate-500 mt-2 px-2">
                Your student profile registration is complete. You can now log in to the portal dashboard.
            </p>

            <a href="login.php" 
               class="block w-full text-center bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold py-3 px-4 rounded-xl transition duration-200 mt-6 shadow-md shadow-blue-700/10">
                Go to Sign In
            </a>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>