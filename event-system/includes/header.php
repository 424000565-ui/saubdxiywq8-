<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Student Event Registration System
    </title>

    <!-- Bootstrap CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Custom CSS -->

    <style>

        body {
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .card {
            border-radius: 10px;
        }

        .btn {
            border-radius: 5px;
        }

        footer {
            margin-top: 50px;
            padding: 15px 0;
            background-color: #212529;
            color: white;
            text-align: center;
        }

    </style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">

    <div class="container">

        <a class="navbar-brand" href="#">
            Event Registration System
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse"
             id="navbarNav">

            <ul class="navbar-nav ms-auto">

                <?php if(isset($_SESSION['user_id'])): ?>

                    <!-- ADMIN NAVIGATION -->

                    <?php if($_SESSION['role'] == 'admin'): ?>

                        <li class="nav-item">

                            <a class="nav-link"
                               href="/event-system/admin/events/admin_events.php">

                                Manage Events

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                               href="/event-system/admin/events/admin_add-event.php">

                                Add Event

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                               href="/event-system/admin/registrants/registrants.php">

                                Registrants

                            </a>

                        </li>

                    <?php else: ?>

                        <!-- STUDENT NAVIGATION -->

                        <li class="nav-item">

                            <a class="nav-link"
                               href="/event-system/student/dashboard.php">

                                Dashboard

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link"
                               href="/event-system/student/my_events.php">

                                My Events

                            </a>

                        </li>

                    <?php endif; ?>

                    <!-- USER INFO -->

                    <li class="nav-item">

                        <span class="nav-link text-info">

                            Welcome,
                            <?php echo $_SESSION['name']; ?>

                        </span>

                    </li>

                    <!-- LOGOUT -->

                    <li class="nav-item">

                        <a class="nav-link text-warning"
                           href="/event-system/auth/logout.php">

                            Logout

                        </a>

                    </li>

                <?php else: ?>

                    <!-- GUEST NAVIGATION -->

                    <li class="nav-item">

                        <a class="nav-link"
                           href="/event-system/auth/login.php">

                            Login

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link"
                           href="/event-system/auth/register.php">

                            Register

                        </a>

                    </li>

                <?php endif; ?>

            </ul>

        </div>

    </div>

</nav>

<!-- MAIN CONTENT -->

<div class="container mt-4"></div>