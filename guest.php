<?php
$serverName = "localhost";
$userName = "root";
$pass = "";
$dbName = "abcd";

$conn = new mysqli($serverName, $userName, $pass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['reg'])) {
    $id = sanitizeInput($_POST['id']);
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $pass = password_hash(sanitizeInput($_POST['pass']), PASSWORD_BCRYPT);
    $amount = sanitizeInput($_POST['amount']);

    if (!empty($id) && !empty($name) && !empty($email) && !empty($pass) && !empty($amount)) {
        $payment_status = true; 
        if ($payment_status) {
            $sql = "INSERT INTO ab (Id, Name, Email, Pass, Amount) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssd", $id, $name, $email, $pass, $amount);
            if ($stmt->execute()) {
                echo "Registration and Payment Done";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Payment Failed";
        }
    } else {
        echo "Fill up the form first";
    }
}

// Check-in operation
if (isset($_POST['checkin'])) {
    $id = sanitizeInput($_POST['id']);
    $checkin_time = date('Y-m-d H:i:s');
    if (!empty($id)) {
        $sql = "INSERT INTO checkinout (emp_id, checkin_time) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $id, $checkin_time);
        if ($stmt->execute()) {
            echo "Check-in Successful";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "ID is required for check-in";
    }
}

// Check-out operation
if (isset($_POST['checkout'])) {
    $id = sanitizeInput($_POST['id']);
    $checkout_time = date('Y-m-d H:i:s');
    if (!empty($id)) {
        $sql = "UPDATE checkinout SET checkout_time=? WHERE emp_id=? AND checkout_time IS NULL";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $checkout_time, $id);
        if ($stmt->execute()) {
            echo "Check-out Successful";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "ID is required for check-out";
    }
}

// Update operation
if (isset($_POST['update'])) {
    $id = sanitizeInput($_POST['id']);
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $pass = password_hash(sanitizeInput($_POST['pass']), PASSWORD_BCRYPT);

    if (!empty($id) && !empty($name) && !empty($email) && !empty($pass)) {
        $sql = "UPDATE ab SET Name=?, Email=?, Pass=? WHERE Id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $pass, $id);
        if ($stmt->execute()) {
            echo "Record updated successfully";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "All fields must be filled out for update";
    }
}

// Delete operation
if (isset($_GET['delete'])) {
    $id = sanitizeInput($_GET['delete']);
    if (!empty($id)) {
        $sql = "DELETE FROM ab WHERE Id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
            echo "Record deleted successfully";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "ID is required for deletion";
    }
}

// Fetch and display data
$sql = "SELECT a.Id, a.Name, a.Email, ci.checkin_time, co.checkout_time
        FROM ab a
        LEFT JOIN (
            SELECT emp_id, MAX(checkin_time) AS checkin_time 
            FROM checkinout 
            GROUP BY emp_id
        ) ci ON a.Id = ci.emp_id
        LEFT JOIN (
            SELECT emp_id, MAX(checkout_time) AS checkout_time 
            FROM checkinout 
            GROUP BY emp_id
        ) co ON a.Id = co.emp_id";
$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Operations with Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        h1 {
            color: #333;
        }

        h2 {
            color: #666;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 300px;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form input[type="number"],
        form button {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #45a049;
        }

        table {
            width: 80%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .action-buttons form {
            display: inline-block;
            margin-right: 10px;
        }

        .action-buttons a, 
        .action-buttons button {
            margin: 0;
        }

        .action-buttons a {
            color: white;
            background-color: #f44336;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .action-buttons a:hover {
            background-color: #e53935;
        }
    </style>
    <script>
        function validateForm() {
            var id = document.forms["addGuestForm"]["id"].value;
            var name = document.forms["addGuestForm"]["name"].value;
            var email = document.forms["addGuestForm"]["email"].value;
            var pass = document.forms["addGuestForm"]["pass"].value;
            var amount = document.forms["addGuestForm"]["amount"].value;
            if (id == "" || name == "" || email == "" || pass == "" || amount == "") {
                alert("All fields must be filled out");
                return false;
            }
        }
    </script>
</head>
<body>
    <h1>Guest Management</h1>

    <h2>Add Guest with Payment</h2>
    <form name="addGuestForm" method="post" onsubmit="return validateForm()">
        ID: <input type="text" name="id"><br>
        Name: <input type="text" name="name"><br>
        Email: <input type="email" name="email"><br>
        Pass: <input type="password" name="pass"><br>
        Amount: <input type="number" name="amount"><br>
        <button name="reg">Add</button>
    </form>

    <h2>Check-in/Check-out</h2>
    <form method="post">
        ID: <input type="text" name="id"><br> <br>
        <button name="checkin">Check-in</button>
        <button name="checkout">Check-out</button>
    </form>

    <h2>Room Selection and Payment Upgrade</h2>
    <form method="post">
        ID: <input type="text" name="id"><br>
        Room Type: 
        <select name="room_type">
            <option value="standard">Standard</option>
            <option value="deluxe">Deluxe</option>
            <option value="suite">Suite</option>
        </select><br>
        Additional Amount: <input type="number" name="additional_amount"><br>
        <button name="upgrade">Upgrade</button>
    </form>

    <h2>Guest List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Check-in Time</th>
            <th>Check-out Time</th>
            <th>Action</th>
        </tr>
        <?php if ($res && $res->num_rows > 0): ?>
            <?php while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Id']); ?></td>
                    <td><?php echo htmlspecialchars($row['Name']); ?></td>
                    <td><?php echo htmlspecialchars($row['Email']); ?></td>
                    <td><?php echo htmlspecialchars($row['checkin_time'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['checkout_time'] ?? 'N/A'); ?></td>
                    <td class="action-buttons">
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['Id']); ?>">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($row['Name']); ?>">
                            <input type="text" name="email" value="<?php echo htmlspecialchars($row['Email']); ?>">
                            <input type="password" name="pass" value="">
                            <button type="submit" name="update">Update</button>
                        </form>
                        <a href="?delete=<?php echo htmlspecialchars($row['Id']); ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No records found</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
