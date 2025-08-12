<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$tweet_id = isset($_GET['tweet_id']) ? $_GET['tweet_id'] : null;

if ($tweet_id) {
    // Fetch tweet details from the database
    $stmt = $pdo->prepare("SELECT tweets.id, tweets.tweet, users.username FROM tweets JOIN users ON tweets.user_id = users.id WHERE tweets.id = ?");
    $stmt->execute([$tweet_id]);
    $tweet = $stmt->fetch();

    if (!$tweet) {
        echo "Tweet not found!";
        exit();
    }
} else {
    echo "Invalid tweet ID!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tweet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f8fa;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #1DA1F2;
            padding: 15px;
            color: white;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px;
        }
        .tweet {
            background-color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .tweet .username {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>View Tweet</h1>
    </header>

    <div class="container">
        <div class="tweet">
            <p class="username"><?php echo htmlspecialchars($tweet['username']); ?></p>
            <p><?php echo htmlspecialchars($tweet['tweet']); ?></p>
        </div>
    </div>
</body>
</html>
