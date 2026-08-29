<?php
session_start();
require_once "config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (
        empty($full_name) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 8) {

        $error = "Password must be at least 8 characters long.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->execute([$email]);

        if ($check->fetch()) {

            $error = "An account with this email already exists.";

        } else {

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                "INSERT INTO users
                (full_name, email, password)
                VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $full_name,
                $email,
                $hashed_password
            ]);

            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register | Twisted Threads</title>

    <link rel="stylesheet"
          href="assets/css/style.css">
</head>

<body class="auth-body">

    <div class="auth-page">

        <div class="brand-side">

            <img src="assets/images/logo.jpg"
                 alt="Twisted Threads Logo"
                 class="brand-logo">

            <h1>Twisted Threads</h1>

            <p>
                Your handmade creations deserve
                an organized workspace.
            </p>

        </div>

        <div class="form-side">

            <div class="form-box">

                <p class="small-heading">
                    CREATE ACCOUNT
                </p>

                <h2>Join your workspace</h2>

                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST"
                      action="register.php">

                    <div class="input-group">
                        <label>Full Name</label>

                        <input
                            type="text"
                            name="full_name"
                            required
                        >
                    </div>

                    <div class="input-group">
                        <label>Email Address</label>

                        <input
                            type="email"
                            name="email"
                            required
                        >
                    </div>

                    <div class="input-group">
                        <label>Password</label>

                        <input
                            type="password"
                            name="password"
                            required
                        >
                    </div>

                    <div class="input-group">
                        <label>Confirm Password</label>

                        <input
                            type="password"
                            name="confirm_password"
                            required
                        >
                    </div>

                    <button type="submit"
                            class="main-button">
                        Create Account
                    </button>

                </form>

                <p class="switch-text">
                    Already have an account?
                    <a href="login.php">
                        Sign in
                    </a>
                </p>

            </div>

        </div>

    </div>

</body>
</html>