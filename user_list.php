<?php
include("DbCon.php");
session_start(); // Here Session Starts

$log = false;
if (isset($_SESSION['username'])) {
    $log = true;
}

// Initialize variables for the update process
$updateMode = false;
$employeeToUpdate = [];

// Check if a deletion request was made
if (isset($_GET['delete_email'])) {
    $emailToDelete = $_GET['delete_email'];

    // Prepare the SQL delete statement
    $deleteSql = "DELETE FROM employee WHERE email = ?";
    $stmt = mysqli_prepare($conn, $deleteSql);
    mysqli_stmt_bind_param($stmt, "s", $emailToDelete);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>window.location.href='user_list.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting employee');</script>";
    }
}

// Check if an update request was made
if (isset($_GET['update_email'])) {
    $emailToUpdate = $_GET['update_email'];
    $updateMode = true;

    // Retrieve specific employee details for editing
    $query = "SELECT * FROM employee WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $emailToUpdate);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $employeeToUpdate = mysqli_fetch_assoc($result);
}

// Handle form submission for updating employee details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $mob_no = $_POST['mob_no'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = password_hash($_POST['pass'], PASSWORD_DEFAULT); // Hash password

    // Prepare the SQL update statement with updated_at set to current time
    $updateSql = "UPDATE employee SET id = ?, name = ?, mob_no = ?, address = ?, pass = ?, updated_at = NOW() WHERE email = ?";
    $stmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($stmt, "isssss", $id, $name, $mob_no, $address, $password, $email);

    // Execute the update statement
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>window.location.href='user_list.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating employee');</script>";
    }
}

// Retrieve the list of all employees, ordered by update or creation date
$sql = "SELECT id, name, mob_no, address, email, pass, created_at, updated_at FROM employee ORDER BY updated_at DESC, created_at DESC";
$result = mysqli_query($conn, $sql);
$employees = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <link rel="icon" href="brilliance.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }

        .container {
            padding: 15px;
        }

        .table-container,
        .update-form {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
        }

        .btn-group .btn {
            margin: 0 2px;
        }

        .update-form {
            margin-top: 20px;
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
        }

        .update-form label {
            font-weight: 500;
        }
    </style>
</head>

<body>
    <nav class="navbar bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Employee Management System'; ?></a>
            <form class="d-flex" role="search">

                <?php

                if ($log == true) {

                    echo '<a class="btn btn-outline-light me-2" type="button" href="logout.php">Log Out</a>';
                } else {

                    echo '<a class="btn btn-outline-light me-2" type="button" href="signin_page.php">Log In</a>';
                }

                ?>

                <button class="btn btn-outline-light" type="button" onclick="window.location.href='signup_page.php'">Add User</button>
            </form>
        </div>
    </nav>

    <div class="container my-4">
        <div class="table-container">
            <h2 class="text-center">Registered Employees</h2>
            <table class="table table-hover table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>Password</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?= htmlspecialchars($employee['id']); ?></td>
                            <td><?= htmlspecialchars($employee['name']); ?></td>
                            <td><?= htmlspecialchars($employee['email']); ?></td>
                            <td><?= htmlspecialchars($employee['mob_no']); ?></td>
                            <td><?= htmlspecialchars($employee['address']); ?></td>
                            <td><?= htmlspecialchars($employee['pass']); ?></td> <!-- Display hashed password -->
                            <td><?= htmlspecialchars($employee['created_at']); ?></td>
                            <td><?= htmlspecialchars($employee['updated_at']); ?></td>
                            <td>
                                <div class='btn-group' role='group'>
                                    <a href='?update_email=<?= urlencode($employee['email']); ?>' class='btn btn-warning'>Update</a>
                                    <a href='?delete_email=<?= urlencode($employee['email']); ?>' onclick="return confirm('Are you sure you want to delete this employee?');" class='btn btn-danger'>Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($updateMode): ?>
            <div class="update-form">
                <h3>Update Employee</h3>
                <form method="POST" action="">
                    <div class="mb-3">
                        <input type="hidden" class="form-control" id="id" name="id" value="<?= htmlspecialchars($employeeToUpdate['id']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($employeeToUpdate['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="mob_no" class="form-label">Mobile</label>
                        <input type="text" class="form-control" id="mob_no" name="mob_no" value="<?= htmlspecialchars($employeeToUpdate['mob_no']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($employeeToUpdate['address']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="pass" class="form-label">Password</label>
                        <input type="password" class="form-control" id="pass" name="pass">
                    </div>
                    <input type="hidden" name="email" value="<?= htmlspecialchars($employeeToUpdate['email']); ?>">
                    <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                    <a href="user_list.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>