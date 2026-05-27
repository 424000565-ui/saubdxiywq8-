<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Event Registration System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-5xl min-h-[550px] bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row text-left">
        
        <div class="w-full md:w-1/2 bg-gradient-to-br from-blue-700 via-blue-800 to-slate-900 p-10 flex flex-col justify-between text-white">
            <div>
                <span class="text-xs font-semibold tracking-wider uppercase bg-white/10 text-blue-200 px-3 py-1 rounded-full compatibility">
                    University Portal
                </span>
                <h1 class="text-3xl md:text-4xl font-bold mt-6 leading-tight tracking-tight">
                    Student Event <br>Registration System
                </h1>
                <p class="text-blue-100/80 mt-4 text-sm font-light leading-relaxed max-w-sm">
                    Discover campus workshops, academic symposiums, and sports events. Secure your seats, manage your schedule, and access your digital passes all from one central dashboard.
                </p>
            </div>
            
            <div class="mt-8 pt-6 border-t border-white/10 hidden md:block">
                <div class="flex gap-6 text-sm text-blue-200/90">
                    <div>
                        <span class="block text-xl font-bold text-white">Fast Pass</span>
                        <span class="text-xs">QR Code Entry</span>
                    </div>
                    <div>
                        <span class="block text-xl font-bold text-white">Real-time</span>
                        <span class="text-xs">Seat Allocations</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-white">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Welcome</h2>
                <p class="text-sm text-slate-500 mt-1">Sign in to your portal or establish a new profile to get started.</p>
            </div>

            <div class="space-y-4">
                
                <a href="auth/login.php" 
                   class="block w-full text-center bg-blue-700 hover:bg-blue-800 text-white font-medium py-3.5 px-4 rounded-xl transition duration-200 shadow-lg shadow-blue-700/10 hover:shadow-blue-700/20 transform hover:-translate-y-0.5">
                    Login
                </a>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-slate-200"></div>
                    <span class="flex-shrink mx-4 text-slate-400 text-xs uppercase tracking-wider font-semibold">Or</span>
                    <div class="flex-grow border-t border-slate-200"></div>
                </div>

                <a href="auth/register.php" 
                   class="block w-full text-center bg-white border-2 border-slate-200 hover:border-blue-700 text-slate-700 hover:text-blue-700 font-medium py-3.5 px-4 rounded-xl transition duration-200">
                    Create Student Account
                </a>
            </div>

            <div class="mt-12 text-center">
                <p class="text-xs text-slate-400">
                    Authorized Administrator? 
                    <a href="admin/login.php" class="text-blue-700 hover:underline font-semibold ml-1">
                        Admin Access
                    </a>
                </p>
            </div>
        </div>

    </div>

</body>
</html>