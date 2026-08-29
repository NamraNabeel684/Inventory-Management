
<?php
session_start();
require_once "config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {

        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["user_name"] = $user["full_name"];

    header("Location: dashboard.php");
    exit();

}else {
            $error = "Invalid email or password.";
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

    <title>Login | Twisted Threads</title>

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
                Manage your handmade creations,
                track your inventory, and keep every
                thread organized.
            </p>

        </div>

        <div class="form-side">

            <div class="form-box">

                <p class="small-heading">
                    WELCOME BACK
                </p>

                <h2>Sign in to your workspace</h2>

                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST"
                      action="login.php">

                    <div class="input-group">
                        <label>Email Address</label>

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email"
                            required
                        >
                    </div>

                    <div class="input-group">
                        <label>Password</label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >
                    </div>

                    <button type="submit"
                            class="main-button">
                        Sign In
                    </button>

                </form>

                <p class="switch-text">
                    New to Twisted Threads?
                    <a href="register.php">
                        Create an account
                    </a>
                </p>

            </div>

        </div>

    </div>

</body>
</html>