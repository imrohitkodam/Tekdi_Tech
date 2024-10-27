<?php
include("DbCon.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['fname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['Address'];
    $mob = $_POST['Mobile'];

    // Check if email already exists
    $sql1 = "SELECT * FROM employee WHERE email='$email'";
    $result = mysqli_query($conn, $sql1);
    $rows = mysqli_num_rows($result);

    if ($rows == 0) {
        // Hash the password before storing it in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the employee data with the hashed password
        $sql = "INSERT INTO employee (name, email, pass, mob_no, address, created_at, updated_at) VALUES('$name', '$email', '$hashed_password', '$mob', '$address', NOW(), NOW())";
        if (mysqli_query($conn, $sql)) {
            // Store the user's name in the session
            header("Location: user_list.php"); // Redirect to user list on success
            exit();
        }
    } else {
        echo "<script>alert('Email already exists');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="icon" href="brilliance.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar bg-primary" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Employee Management System'; ?></a>
            <form class="d-flex" role="search">

            </form>
        </div>
    </nav>

    <form action="signup_page.php" method="post">
        <div class="container mt-3">
            <div class="row">
                <div class="col">
                    <input type="text" name="fname" class="form-control" placeholder="Enter Name" required>
                </div>
                <div class="col">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
            </div>
        </div>
        <div class="container mt-3">
            <div class="row">
                <div class="col">
                    <input type="text" class="form-control" placeholder="Address" name="Address" required>
                </div>
                <div class="col">
                    <input type="password" class="form-control" placeholder="Password" name="password" required>
                </div>
            </div>
        </div>
        <div class="container mt-3">
            <div class="row">
                <div class="col">
                    <input style="width:49%;" type="number" class="form-control" placeholder="Mobile" name="Mobile" required>
                </div>
            </div>
        </div>
        <div class="container mt-3">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <button class="btn btn-primary w-100 py-2 fw-semi-bold" type="submit">Sign Up</button>
                </div>
            </div>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>