<?php
session_start();

// Check login and user role
if (!isset($_SESSION["loggedin"]) || $_SESSION["role_id"] != 1) {
    header("Location: login.php");
    exit();
}

// Logout
if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "dbyazan", "0000", "all php");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$success = '';

// Add new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_user"])) {
    $first_name = trim($_POST["first_name"]);
    $middle_name = trim($_POST["middle_name"]);
    $last_name = trim($_POST["last_name"]);
    $family_name = trim($_POST["family_name"]);
    $email = trim($_POST["email"]);
    $phone_number = trim($_POST["phone_number"]);
    $password = trim($_POST["password"]);
    $role_id = intval($_POST["role_id"]);
    $is_admin = isset($_POST["is_admin"]) ? 1 : 0;
    $profile_image = '';

    // Check if all fields are filled
    if (empty($first_name) || empty($middle_name) || empty($last_name) || empty($family_name) || empty($email) || empty($phone_number) || empty($password) || empty($role_id)) {
        $errors['general'] = 'All fields are required';
    } else {
        // Check if email is already in use
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors['email'] = 'This email is already in use';
        } else {
            // Handle image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
                $image_name = basename($_FILES['profile_image']['name']);
                $target_dir = "uploads/";
                $target_file = $target_dir . $image_name;
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    $profile_image = $image_name;
                } else {
                    $errors['profile_image'] = 'Error uploading the image';
                }
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, family_name, email, phone_number, password, role_id, is_admin, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssiss", $first_name, $middle_name, $last_name, $family_name, $email, $phone_number, $hashed_password, $role_id, $is_admin, $profile_image);
            if ($stmt->execute()) {
                $success = 'User added successfully';
            } else {
                $errors['general'] = 'Error occurred while adding the user';
            }
            $stmt->close();
        }
    }
}

// Update user details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_user"])) {
    $id = intval($_POST["id"]);
    $first_name = trim($_POST["first_name"]);
    $middle_name = trim($_POST["middle_name"]);
    $last_name = trim($_POST["last_name"]);
    $family_name = trim($_POST["family_name"]);
    $email = trim($_POST["email"]);
    $phone_number = trim($_POST["phone_number"]);
    $role_id = intval($_POST["role_id"]);
    $is_admin = isset($_POST["is_admin"]) ? 1 : 0;
    $profile_image = isset($_POST["existing_image"]) ? $_POST["existing_image"] : '';

    // Check if all fields are filled
    if (empty($first_name) || empty($middle_name) || empty($last_name) || empty($family_name) || empty($email) || empty($phone_number) || empty($role_id)) {
        $errors['general'] = 'All fields are required';
    } else {
        // Handle image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
            $image_name = basename($_FILES['profile_image']['name']);
            $target_dir = "uploads/";
            $target_file = $target_dir . $image_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = $image_name;
            } else {
                $errors['profile_image'] = 'Error uploading the image';
            }
        }

        // Update user details
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, family_name = ?, email = ?, phone_number = ?, role_id = ?, is_admin = ?, profile_image = ? WHERE id = ?");
        $stmt->bind_param("ssssssissi", $first_name, $middle_name, $last_name, $family_name, $email, $phone_number, $role_id, $is_admin, $profile_image, $id);
        if ($stmt->execute()) {
            $success = 'User updated successfully';
        } else {
            $errors['general'] = 'Error occurred while updating the user';
        }
        $stmt->close();
    }
}

// Delete user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_user"])) {
    $id = intval($_POST["id"]);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'User deleted successfully';
    } else {
        $errors['general'] = 'Error occurred while deleting the user';
    }
    $stmt->close();
}

// Fetch users data from database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .btn-custom {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Admin Dashboard</h2>
    <!-- Logout button -->
    <form method="post">
        <button type="submit" name="logout" class="btn btn-danger">Logout</button>
    </form>

    <!-- Add new user form -->
    <h3>Add User</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="add_user" value="1">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Middle Name</label>
            <input type="text" name="middle_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Family Name</label>
            <input type="text" name="family_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
            <?php if (isset($errors['email'])): ?>
                <div class="alert alert-danger mt-2"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone_number" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Role ID</label>
            <input type="number" name="role_id" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Profile Image</label>
            <input type="file" name="profile_image" class="form-control">
            <?php if (isset($errors['profile_image'])): ?>
                <div class="alert alert-danger mt-2"><?php echo $errors['profile_image']; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" name="is_admin" class="form-check-input">
            <label class="form-check-label">Is Admin</label>
        </div>
        <button type="submit" class="btn btn-primary">Add User</button>
        <?php if (!empty($success) && isset($_POST["add_user"])): ?>
            <div class="alert alert-success mt-2"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (!empty($errors['general']) && isset($_POST["add_user"])): ?>
            <div class="alert alert-danger mt-2"><?php echo $errors['general']; ?></div>
        <?php endif; ?>
    </form>

    <!-- Users list -->
    <h3 class="mt-5">Users List</h3>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Family Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Role ID</th>
            <th>Is Admin</th>
            <th>Profile Image</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['first_name']; ?></td>
                <td><?php echo $row['middle_name']; ?></td>
                <td><?php echo $row['last_name']; ?></td>
                <td><?php echo $row['family_name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone_number']; ?></td>
                <td><?php echo $row['role_id']; ?></td>
                <td><?php echo $row['is_admin'] ? 'Yes' : 'No'; ?></td>
                <td>
                    <?php if ($row['profile_image']): ?>
                        <img src="uploads/<?php echo $row['profile_image']; ?>" alt="Profile Image" width="50" height="50">
                    <?php endif; ?>
                </td>
                <td>
                    <!-- Edit user form -->
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="first_name" value="<?php echo $row['first_name']; ?>">
                        <input type="hidden" name="middle_name" value="<?php echo $row['middle_name']; ?>">
                        <input type="hidden" name="last_name" value="<?php echo $row['last_name']; ?>">
                        <input type="hidden" name="family_name" value="<?php echo $row['family_name']; ?>">
                        <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
                        <input type="hidden" name="phone_number" value="<?php echo $row['phone_number']; ?>">
                        <input type="hidden" name="role_id" value="<?php echo $row['role_id']; ?>">
                        <input type="hidden" name="is_admin" value="<?php echo $row['is_admin']; ?>">
                        <input type="hidden" name="existing_image" value="<?php echo $row['profile_image']; ?>">
                        <button type="submit" name="edit_user" class="btn btn-warning btn-custom">Edit</button>
                    </form>
                    <!-- Delete user form -->
                    <form method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_user" class="btn btn-danger btn-custom">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Edit user form -->
    <?php if (isset($_POST["edit_user"])): ?>
        <?php
        $id = intval($_POST["id"]);
        $first_name = $_POST["first_name"];
        $middle_name = $_POST["middle_name"];
        $last_name = $_POST["last_name"];
        $family_name = $_POST["family_name"];
        $email = $_POST["email"];
        $phone_number = $_POST["phone_number"];
        $role_id = $_POST["role_id"];
        $is_admin = $_POST["is_admin"];
        $existing_image = $_POST["existing_image"];
        ?>
        <h3 class="mt-5">Edit User</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="update_user" value="1">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>" required>
            </div>
            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name" class="form-control" value="<?php echo $middle_name; ?>" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>" required>
            </div>
            <div class="form-group">
                <label>Family Name</label>
                <input type="text" name="family_name" class="form-control" value="<?php echo $family_name; ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" class="form-control" value="<?php echo $phone_number; ?>" required>
            </div>
            <div class="form-group">
                <label>Role ID</label>
                <input type="number" name="role_id" class="form-control" value="<?php echo $role_id; ?>" required>
            </div>
            <div class="form-group">
                <label>Profile Image</label>
                <input type="file" name="profile_image" class="form-control">
                <?php if ($existing_image): ?>
                    <p>Current Image: <img src="uploads/<?php echo $existing_image; ?>" alt="Profile Image" width="50" height="50"></p>
                <?php endif; ?>
                <input type="hidden" name="existing_image" value="<?php echo $existing_image; ?>">
                <?php if (isset($errors['profile_image'])): ?>
                    <div class="alert alert-danger mt-2"><?php echo $errors['profile_image']; ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" name="is_admin" class="form-check-input" <?php echo $is_admin ? 'checked' : ''; ?>>
                <label class="form-check-label">Is Admin</label>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <?php if (!empty($success) && isset($_POST["update_user"])): ?>
                <div class="alert alert-success mt-2"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (!empty($errors['general']) && isset($_POST["update_user"])): ?>
                <div class="alert alert-danger mt-2"><?php echo $errors['general']; ?></div>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</div>
</body>
</html>

<?php $conn->close(); ?>
