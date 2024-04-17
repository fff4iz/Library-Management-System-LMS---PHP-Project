<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Update User</title>
    <link rel="stylesheet" href="style.css">

    <style>
        /* CSS styles for the side navigation menu */
        .sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #334;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }

        .sidenav a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }

        .sidenav a:hover {
            color: #f1f1f1;
        }

        .sidenav .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        #main {
            transition: margin-left .5s;
            padding: 16px;
        }
    </style>

    <script type="text/javascript">
        function preventBack() {
            window.history.forward();
        }
        setTimeout("preventBack()", 0);
        window.onunload = function() {
            null
        };
    </script>
    <script>
        function capitalizeInput(input) {
            input.value = input.value.toUpperCase();
        }
    </script>
</head>
<body>
    <header>
        <div class="menu-btn">
            <span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776;</span>
        </div>
        <h2> &nbsp &nbsp LMS Admin</h2>
        <nav class="navigation">
            <a href="admin-dashboard.php" class="active">Dashboard</a>
            <a href="admin-logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="admin-list-user.php">List User</a>
            <a href="admin-list-book.php">List Book</a>
            <a href="admin-new-book.php">New Book</a>
        </div>

        <script>
            function openNav() {
                document.getElementById("mySidenav").style.width = "250px";
                document.getElementById("main").style.marginLeft = "250px";
            }

            function closeNav() {
                document.getElementById("mySidenav").style.width = "0";
                document.getElementById("main").style.marginLeft= "0";
            }
        </script>
    </main>

    <?php
    require "db.php";

    $userid = isset($_GET['user_id']) ? $_GET['user_id'] : null;

    if ($userid) {
        $query = "SELECT * FROM user WHERE id = $userid";
        $view_user = mysqli_query($mysqli, $query);

        if ($view_user && mysqli_num_rows($view_user) > 0) {
            while($row = mysqli_fetch_assoc($view_user)){
                $id = $row["id"];
                $un = $row["username"];
                $fn = $row["fullname"];
                $em = $row["email"];
                $pw = $row["password"];
            }
        } else {
            showAlert("User not found.");
        }
    } else {
        showAlert("User ID not provided.");
    }

    if (isset($_POST['update'])) {
        $un = $_POST['username']; 
        $fn = $_POST['fullname']; 
        $em = $_POST['email'];
        $pw = $_POST['password'];
        $confirm_pw = $_POST['confirm_password']; // Add this line
    
        // Password validation
        if ($pw !== $confirm_pw) {
            showAlert("Error: Passwords do not match.");
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{8,}$/', $pw)) {
            showAlert("Error: Password must be at least 8 characters long and contain at least one uppercase letter, one number, and one symbol.");
        } else {
            // Check if username is already taken
            $check_username_query = "SELECT * FROM user WHERE username=?";
            $check_username_stmt = $mysqli->prepare($check_username_query);
            $check_username_stmt->bind_param("s", $un);
            $check_username_stmt->execute();
            $check_username_result = $check_username_stmt->get_result();
    
            if ($check_username_result->num_rows > 0) {
                showAlert("Error: Username already taken.");
            } else {
                // Check if email is already registered
                $check_email_query = "SELECT * FROM user WHERE email=?";
                $check_email_stmt = $mysqli->prepare($check_email_query);
                $check_email_stmt->bind_param("s", $em);
                $check_email_stmt->execute();
                $check_email_result = $check_email_stmt->get_result();
    
                if ($check_email_result->num_rows > 0) {
                    showAlert("Error: Email already registered.");
                } else {
                    // Update user details
                    $query = "UPDATE user SET username = ?, fullname = ?, email = ?, password = ? WHERE id = ?";
                    $update_stmt = $mysqli->prepare($query);
                    $update_stmt->bind_param("ssssi", $un, $fn, $em, $pw, $userid);
                    $result = $update_stmt->execute();
    
                    if ($result) {
                        showAlert("User updated successfully!");
                    } else {
                        showAlert("Failed to update user: " . $update_stmt->error);
                    }
                }
            }
        }
    }        

    // Function to create JavaScript alert
    function showAlert($message) {
        echo "<script>alert('$message');</script>";
    }
    ?>

    <div class="form-box-login-register">
        <h3>Update user Form</h3>
        <form action="admin-update-user.php?user_id=<?php echo $id; ?>" method="post">
            <div class="input-box">
                <input type="text" name="fullname" oninput="capitalizeInput(this)" required placeholder="FULL NAME" value="<?php echo $fn ?>">
            </div>
            <div class="input-box">
                <input type="text" name="username" required placeholder="USER NAME" value="<?php echo $un ?>">
            </div>
            <div class="input-box">
                <input type="email" name="email" required placeholder="EMAIL" value="<?php echo $em ?>">
            </div>
            <div class="input-box">
                <input type="password" name="password" required placeholder="PASSWORD" value="<?php echo $pw ?>">
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" required placeholder="CONFIRM PASSWORD" value="<?php echo $pw ?>">
            </div>
            <div class="input-box">
                <input type="submit" name="update" value="Update">
            </div>
        </form>
    </div>    
</body>
</html>
