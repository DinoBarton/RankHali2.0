<?php
$host = 'localhost';
$db   = 'RankHali';
$user = 'root';
$pass = 'Dino09Barton';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted username and password
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Validate the submitted information
    if (validateFormData($username, $password, $email)) {
        // Hash the password
        $password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the INSERT statement
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");

        // Bind the parameters and execute the statement
        $stmt->execute([$username, $password, $email]);

        // Redirect to the login page
        header('Location: main.php');
        exit;
    } else {
        // One or more fields are empty
        echo "Please fill out all the fields.";
    }
}

function validateFormData($username, $password, $email) {
    global $pdo;

    // Check if any of the fields are empty
    if (empty($username) || empty($password) || empty($email)) {
        return false;
    }
    
    // Check if the username is already taken
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // The username is taken, now check the password
        if (password_verify($password, $user['password'])) {
            // The password is correct
            return true;
        } else {
            // The password is incorrect
            return false;
        }
    } else {
        // The username is not taken
        return true;
    }
    
    // Check if the email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Add more validation rules as needed
    
    return true;
}

function usernameExists($username) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch() !== false;
}

$dsn = 'mysql:host=localhost;dbname=RankHali';
$dbUsername = 'root';
$dbPassword = 'Dino09Barton';
$pdo = new PDO($dsn, $dbUsername, $dbPassword);

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve the submitted username and password
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];

        // Validate the submitted information
        if (validateFormData($username, $password, $email)) {
            // Hash the password
            $password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the INSERT statement
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");

            // Bind the parameters and execute the statement
            $stmt->execute([$username, $password, $email]);

            // Redirect to the login page
            header('Location: login.php');
            exit;
        } else {
            // One or more fields are empty
            echo "Please fill out all the fields.";
        }
    }
} catch (PDOException $e) {
    // Handle the exception here
    echo "An error occurred: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup Page</title>
</head>
<body>
    <h2>Signup</h2>
    <form method="POST" action="signup.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>
        
        <input type="submit" value="Signup">
    </form>
</body>
</html>