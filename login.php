<?php
// Database configuration
$servername = "localhost";
$username = "dbyazan";
$password = "0000";
$dbname = "all php";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $password = "";
$email_err = $password_err = "";

// Start session
session_start();

// Check if the user is already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if ($_SESSION["role_id"] == 1) {
        header("location:admin.php");
    } else {
        header("location:welcome.php");
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check credentials
    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id, email, password, role_id FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $email, $hashed_password, $role_id);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Start a new session
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;
                            $_SESSION["role_id"] = $role_id;

                            if ($role_id == 1) {
                                header("location: admin.php");
                            } else {
                                header("location: welcome.php");
                            }
                            exit;
                        } else {
                            $password_err = "Invalid password.";
                        }
                    }
                } else {
                    $email_err = "No account found with that email.";
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .btn-custom {
            width: 100%;
            margin-top: 20px;
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
    </style>
    <script>
        function validateLoginForm() {
            var email = document.forms["loginForm"]["email"].value;
            var password = document.forms["loginForm"]["password"].value;
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            var isValid = true;
            document.getElementById("email_err").innerHTML = "";
            document.getElementById("password_err").innerHTML = "";

            if (!email.match(emailPattern)) {
                document.getElementById("email_err").innerHTML = "Invalid email format.";
                isValid = false;
            }

            if (password.trim() === "") {
                document.getElementById("password_err").innerHTML = "Please enter your password.";
                isValid = false;
            }

            return isValid;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form name="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateLoginForm()">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                <span id="email_err" class="error"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span id="password_err" class="error"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary btn-custom" value="Login">
            </div>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>    
</body>
</html>
