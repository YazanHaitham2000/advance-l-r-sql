<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== 'Admin') {
    header("Location: login.php");
    exit();
}
if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "dbyazan", "0000", "all php");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];
$success = '';

if (isset($_POST["add_user"])) {
    $first_name = $_POST["first_name"];
    $middle_name = $_POST["middle_name"];
    $last_name = $_POST["last_name"];
    $family_name = $_POST["family_name"];
    $email = $_POST["email"];
    $phone_number = $_POST["phone_number"];
    $password = $_POST["password"];
    $role_id = $_POST["role_id"];
    $is_admin = isset($_POST["is_admin"]) ? 1 : 0;

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors['email'] = 'This email is already in use';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, family_name, email, phone_number, password, role_id, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssii", $first_name, $middle_name, $last_name, $family_name, $email, $phone_number, $hashed_password, $role_id, $is_admin);
        if ($stmt->execute()) {
            $success = 'User added successfully';
        } else {
            $errors['general'] = 'Error occurred while adding the user';
        }
        $stmt->close();
    }
}

if (isset($_POST["edit_user"])) {
    $id = $_POST["id"];
    $first_name = $_POST["first_name"];
    $middle_name = $_POST["middle_name"];
    $last_name = $_POST["last_name"];
    $family_name = $_POST["family_name"];
    $email = $_POST["email"];
    $phone_number = $_POST["phone_number"];
    $role_id = $_POST["role_id"];
    $is_admin = isset($_POST["is_admin"]) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, family_name = ?, email = ?, phone_number = ?, role_id = ?, is_admin = ? WHERE id = ?");
    $stmt->bind_param("ssssssiii", $first_name, $middle_name, $last_name, $family_name, $email, $phone_number, $role_id, $is_admin, $id);
    if ($stmt->execute()) {
        $success = 'User updated successfully';
    } else {
        $errors['general'] = 'Error occurred while updating the user';
    }
    $stmt->close();
}

if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'User deleted successfully';
    } else {
        $errors['general'] = 'Error occurred while deleting the user';
    }
    $stmt->close();
}

$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <title>Admin Dashboard</title>
</head>
<body>
<form method="post" action="">
    <button type="submit" name="logout" class="btn btn-danger mt-3">Log Out</button>
</form>
<div class="container mt-5">
    <h1 class="text-center">Admin Dashboard</h1>

    <!-- Add User Form -->
    <div class="card mt-3">
      <div class="card-body">
        <h5>Add New User</h5>
        <?php if (!empty($errors['email'])): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($errors['email']); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form action="" method="post">
          <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
          </div>
          <div class="form-group">
            <label for="middle_name">Middle Name</label>
            <input type="text" class="form-control" id="middle_name" name="middle_name" required>
          </div>
          <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
          </div>
          <div class="form-group">
            <label for="family_name">Family Name</label>
            <input type="text" class="form-control" id="family_name" name="family_name" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="form-group">
            <label for="role_id">Role</label>
            <select class="form-control" id="role_id" name="role_id" required>
              <option value="1">Admin</option>
              <option value="2">User</option>
            </select>
          </div>
          <div class="form-group">
            <label for="is_admin">Is Admin</label>
            <input type="checkbox" id="is_admin" name="is_admin">
          </div>
          <button type="submit" class="btn btn-primary" name="add_user">Add User</button>
        </form>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-body">
        <h5>All Users</h5>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>First Name</th>
              <th>Middle Name</th>
              <th>Last Name</th>
              <th>Family Name</th>
              <th>Email</th>
              <th>Phone Number</th>
              <th>Role</th>
              <th>Is Admin</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                <td><?php echo htmlspecialchars($row['middle_name']); ?></td>
                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['family_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                <td><?php echo htmlspecialchars($row['role_id']);?></td>
                 <td><?php echo $row['is_admin'] ? 'Yes' : 'No'; ?></td>
                 <td>
                   <form action="" method="post" style="display:inline-block;">
                     <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                     <button type="submit" class="btn btn-primary" name="edit_user_form">Edit</button>
                   </form>
                   <a href="?delete=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                 </td>
               </tr>
             <?php endwhile; ?>
           </tbody>
         </table>
       </div>
     </div>
 
     <!-- Edit User Form -->
     <?php if (isset($_POST["edit_user_form"])): ?>
       <?php
       $id = $_POST["id"];
       $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
       $stmt->bind_param("i", $id);
       $stmt->execute();
       $user = $stmt->get_result()->fetch_assoc();
       ?>
       <div class="card mt-3">
         <div class="card-body">
           <h5>Edit User</h5>
           <form action="" method="post">
             <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
             <div class="form-group">
               <label for="first_name">First Name</label>
               <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
             </div>
             <div class="form-group">
               <label for="middle_name">Middle Name</label>
               <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name']); ?>" required>
             </div>
             <div class="form-group">
               <label for="last_name">Last Name</label>
               <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
             </div>
             <div class="form-group">
               <label for="family_name">Family Name</label>
               <input type="text" class="form-control" id="family_name" name="family_name" value="<?php echo htmlspecialchars($user['family_name']); ?>" required>
             </div>
             <div class="form-group">
               <label for="email">Email</label>
               <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
             </div>
             <div class="form-group">
               <label for="phone_number">Phone Number</label>
               <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
             </div>
             <div class="form-group">
               <label for="role_id">Role</label>
               <select class="form-control" id="role_id" name="role_id" required>
                 <option value="1" <?php echo $user['role_id'] == 1 ? 'selected' : ''; ?>>Admin</option>
                 <option value="2" <?php echo $user['role_id'] == 2 ? 'selected' : ''; ?>>User</option>
               </select>
             </div>
             <div class="form-group">
               <label for="is_admin">Is Admin</label>
               <input type="checkbox" id="is_admin" name="is_admin" <?php echo $user['is_admin'] ? 'checked' : ''; ?>>
             </div>
             <button type="submit" class="btn btn-primary" name="edit_user">Update User</button>
           </form>
         </div>
       </div>
     <?php endif; ?>
 
   </div>
 </body>
 </html>
 
 <?php $conn->close(); ?>
