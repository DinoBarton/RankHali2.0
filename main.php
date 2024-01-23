<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected teacher from the form
    $selectedTeacher = $_POST['teacher'];

    // Set up the PostgreSQL database connection
    $dsn = 'pgsql:host=localhost;dbname=RankHali;port=5432';
    $dbUsername = 'root';
    $dbPassword = 'Dino09Barton';
    $pdo = new PDO($dsn, $dbUsername, $dbPassword);

    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the user has already voted
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM votes WHERE user_ip = :user_ip");
        $stmt->bindParam(':user_ip', $_SERVER['REMOTE_ADDR']);
        $stmt->execute();
        $voteCount = $stmt->fetchColumn();

        if ($voteCount > 0) {
            // User has already voted, handle accordingly (e.g., show an error message)
            echo "You have already voted.";
        } else {
            // Prepare the SQL statement
            $stmt = $pdo->prepare("INSERT INTO votes (teacher, user_ip) VALUES (:teacher, :user_ip)");

            // Bind the parameters
            $stmt->bindParam(':teacher', $selectedTeacher);
            $stmt->bindParam(':user_ip', $_SERVER['REMOTE_ADDR']);

            // Execute the statement
            $stmt->execute();

            // Redirect to a thank you page
            header('Location: thank_you.php');
            exit;
        }
    } catch (PDOException $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }
}

// Get the top ten teachers with the most votes
try {
    $stmt = $pdo->prepare("SELECT teacher, COUNT(*) as vote_count FROM votes GROUP BY teacher ORDER BY vote_count DESC LIMIT 10");
    $stmt->execute();
    $topTeachers = $stmt->fetchAll();
} catch (PDOException $e) {
    // Handle any errors
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vote for Your Favorite Teacher</title>
</head>
<body>
    <h1>Vote for Your Favorite Teacher</h1>
    <div style="float: left; width: 200px;">
        <h3>Voting Criteria:</h3>
        <p>The site's purpose is to allow students to vote for the teacher they feel represents the school values</p>
        <ul>
            <li>Intellectually curious</li>
            <li>Respectful</li>
            <li>Warm-hearted</li>
            <li>Team players</li>
            <li>Creative</li>
            <li>Resilient</li>
        </ul>
    </div>  
    <h2>Top 10 Teachers:</h2>
    <ul>
        <?php foreach ($topTeachers as $teacher) { ?>
            <li><?php echo $teacher['teacher']; ?> - <?php echo $teacher['vote_count']; ?> votes</li>
        <?php } ?>
    </ul>
    <style>
        .golden-rectangle {
            width: 161.8px; /* Width of the golden rectangle */
            height: 100px; /* Height of the golden rectangle */
            margin: 10px;
            background-color: #f0f0f0;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="teacher">Select your favorite teacher:</label>
        <div>
            <?php
            // Read the file line by line
            $file = fopen("TeacherList.txt", "r");
            while (($line = fgets($file)) !== false) {
                // Remove newline characters
                $line = trim($line);
                echo '<button class="golden-rectangle" type="submit" name="teacher" value="' . $line . '">' . $line . '</button>';
            }
            fclose($file);
            ?>
        </div>
    </form>
</body>
</html>