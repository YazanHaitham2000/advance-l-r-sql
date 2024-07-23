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

$first_name = $middle_name = $last_name = $family_name = $email = $password = $confirm_password = $mobile_number = "";
$first_name_err = $middle_name_err = $last_name_err = $family_name_err = $email_err = $password_err = $confirm_password_err = $mobile_number_err = "";

// Default role_id (assuming 2 is for a standard user)
$role_id = 2;

// Check if the user is registering as an admin
if (isset($_POST["register_as_admin"]) && $_POST["register_as_admin"] == "on") {
    $role_id = 1;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    if (empty(trim($_POST["first_name"]))) {
        $first_name_err = "Please enter your first name.";
    } else {
        $first_name = trim($_POST["first_name"]);
    }

    // Validate middle name
    if (empty(trim($_POST["middle_name"]))) {
        $middle_name_err = "Please enter your middle name.";
    } else {
        $middle_name = trim($_POST["middle_name"]);
    }

    // Validate last name
    if (empty(trim($_POST["last_name"]))) {
        $last_name_err = "Please enter your last name.";
    } else {
        $last_name = trim($_POST["last_name"]);
    }

    // Validate family name
    if (empty(trim($_POST["family_name"]))) {
        $family_name_err = "Please enter your family name.";
    } else {
        $family_name = trim($_POST["family_name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate mobile number
    if (empty(trim($_POST["mobile_number"]))) {
        $mobile_number_err = "Please enter your mobile number.";
    } elseif (!preg_match("/^[0-9]{10}$/", trim($_POST["mobile_number"]))) {
        $mobile_number_err = "Invalid mobile number format. It should be 10 digits.";
    } else {
        $mobile_number = trim($_POST["mobile_number"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', trim($_POST["password"]))) {
        $password_err = "Password must be at least 8 characters and include at least one upper case letter, one lower case letter, one number, and one special character.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password !== $confirm_password) {
            $confirm_password_err = "Passwords do not match.";
        }
    }

    // Check for errors before inserting into database
    if (empty($first_name_err) && empty($middle_name_err) && empty($last_name_err) && empty($family_name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($mobile_number_err)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare an insert statement
        $sql = "INSERT INTO users (first_name, middle_name, last_name, family_name, email, password, phone_number, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement
            $stmt->bind_param("sssssssi", $first_name, $middle_name, $last_name, $family_name, $email, $hashed_password, $mobile_number, $role_id);

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to login page
                header("location: login.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
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
    <title>Register</title>
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
        function validateRegisterForm() {
            var firstName = document.forms["registerForm"]["first_name"].value;
            var middleName = document.forms["registerForm"]["middle_name"].value;
            var lastName = document.forms["registerForm"]["last_name"].value;
            var familyName = document.forms["registerForm"]["family_name"].value;
            var email = document.forms["registerForm"]["email"].value;
            var password = document.forms["registerForm"]["password"].value;
            var confirmPassword = document.forms["registerForm"]["confirm_password"].value;
            var mobileNumber = document.forms["registerForm"]["mobile_number"].value;
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            var mobilePattern = /^[0-9]{10}$/;
            var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            var isValid = true;

            document.getElementById("first_name_err").innerHTML = "";
            document.getElementById("middle_name_err").innerHTML = "";
            document.getElementById("last_name_err").innerHTML = "";
            document.getElementById("family_name_err").innerHTML = "";
            document.getElementById("email_err").innerHTML = "";
            document.getElementById("password_err").innerHTML = "";
            document.getElementById("confirm_password_err").innerHTML = "";
            document.getElementById("mobile_number_err").innerHTML = "";

            if (firstName.trim() === "") {
                document.getElementById("first_name_err").innerHTML = "Please enter your first name.";
                isValid = false;
            }

            if (middleName.trim() === "") {
                document.getElementById("middle_name_err").innerHTML = "Please enter your middle name.";
                isValid = false;
            }

            if (lastName.trim() === "") {
                document.getElementById("last_name_err").innerHTML = "Please enter your last name.";
                isValid = false;
            }

            if (familyName.trim() === "") {
                document.getElementById("family_name_err").innerHTML = "Please enter your family name.";
                isValid = false;
            }

            if (!email.match(emailPattern)) {
                document.getElementById("email_err").innerHTML = "Invalid email format";
                isValid = false;
            }

            if (!password.match(passwordPattern)) {
                document.getElementById("password_err").innerHTML = "Password must be at least 8 characters long and include at least one upper case letter, one lower case letter, one number, and one special character.";
                isValid = false;
            }

            if (password !== confirmPassword) {
                document.getElementById("confirm_password_err").innerHTML = "Passwords do not match";
                isValid = false;
            }

            if (!mobileNumber.match(mobilePattern)) {
                document.getElementById("mobile_number_err").innerHTML = "Invalid mobile number format. It should be 10 digits.";
                isValid = false;
            }

            return isValid;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form name="registerForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateRegisterForm()">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>">
                <span id="first_name_err" class="error"><?php echo $first_name_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name" class="form-control" value="<?php echo $middle_name; ?>">
                <span id="middle_name_err" class="error"><?php echo $middle_name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>">
                <span id="last_name_err" class="error"><?php echo $last_name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Family Name</label>
                <input type="text" name="family_name" class="form-control" value="<?php echo $family_name; ?>">
                <span id="family_name_err" class="error"><?php echo $family_name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span id="email_err" class="error"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span id="password_err" class="error"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control">
                <span id="confirm_password_err" class="error"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Mobile Number</label>
                <input type="text" name="mobile_number" class="form-control" value="<?php echo $mobile_number; ?>">
                <span id="mobile_number_err" class="error"><?php echo $mobile_number_err; ?></span>
            </div>
            <div class="form-group">
                <label>Register as Admin</label>
                <input type="checkbox" name="register_as_admin">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary btn-custom" value="Register">
            </div>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>    
</body>
</html>
