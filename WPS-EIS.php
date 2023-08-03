<html>
<head>
    <title>WPS Export/Import Users Script</title>
        <style>
        body {
            background-color: #333;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        form {
            max-width: 450px;
            margin: 0 auto;
            padding: 20px;
            background-color: #222;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            
            padding-top:10px;
            padding-bottom:10px;
            padding-left:5px;
            padding-right:0px;
            
            margin-top: 5px;
            margin-bottom: 5px;
            margin-right: 10px;
            margin-left: 0px;
            border: none;
            background-color: #444;
            color: #fff;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            margin:5px 0;
            padding: 10px 25px;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<?php
function exportUsers($dbHost, $dbUser, $dbPassword, $dbName, $prefix) {
    // Connect to the database
    $mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
    
    $prefix = $prefix . 'users';
    
    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Query to export users
    $query = "SELECT * FROM $prefix";

    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $users = $result->fetch_all(MYSQLI_ASSOC);

        // Convert users array to JSON or any other format you prefer
        $jsonUsers = json_encode($users);

        // Save the data to a file
        file_put_contents('exported_users.json', $jsonUsers);

        echo "Users exported successfully!";
    } else {
        echo "No users found.";
    }

    $mysqli->close();
}

function importUsers($dbHost, $dbUser, $dbPassword, $dbName, $prefix) {
    // Connect to the target database
    $mysqli_target = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
    
    $prefix = $prefix . 'user';
    // Check connection
    if ($mysqli_target->connect_error) {
        die("Connection failed: " . $mysqli_target->connect_error);
    }

    // Read the exported users data from the file
    $jsonUsers = file_get_contents('exported_users.json');
    $users = json_decode($jsonUsers, true);

    if (is_array($users)) {
        foreach ($users as $user) {
            // Check if the user exists in the target database
            $query_check_user = "SELECT ID FROM wpsp_users WHERE user_email = '" . $user['user_email'] . "'";
            $result_check_user = $mysqli_target->query($query_check_user);

            if ($result_check_user->num_rows === 0) {
                // User does not exist, insert the user into the target database
                $query_insert_user = "INSERT INTO $prefix (user_login, user_pass, user_nicename, user_email, user_registered) 
                                      VALUES ('" . $user['user_login'] . "', '" . $user['user_pass'] . "', '" . $user['user_nicename'] . "', 
                                              '" . $user['user_email'] . "', '" . $user['user_registered'] . "')";

                $mysqli_target->query($query_insert_user);

                echo "User " . $user['user_login'] . " imported successfully.<br>";
            } else {
                echo "User " . $user['user_login'] . " already exists, skipping.<br>";
            }
        }
    } else {
        echo "No users to import.";
    }

    $mysqli_target->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dbHost = $_POST['dbHost'];
    $dbUser = $_POST['dbUser'];
    $dbPassword = $_POST['dbPassword'];
    $dbName = $_POST['dbName'];
    $prefix = $_POST['prefix'];

    if (isset($_POST['export'])) {
        exportUsers($dbHost, $dbUser, $dbPassword, $dbName, $prefix);
    } elseif (isset($_POST['import'])) {
        importUsers($dbHost, $dbUser, $dbPassword, $dbName, $prefix);
    }
}
?>

<form method="post">
    Database Host: <input type="text" name="dbHost" placeholder="localhost"><br>
    Database User: <input type="text" name="dbUser" placeholder="wps_devUser"><br>
    Database Password: <input type="password" name="dbPassword" placeholder="**********"><br>
    Database Name: <input type="text" name="dbName" placeholder="wps_dev"><br>
    Table Prefix: <input type="text" name="prefix" placeholder="wps_"><br>
    <input type="submit" name="export" value="Export Users">
    <input type="submit" name="import" value="Import Users">
</form>

</body>
</html>
