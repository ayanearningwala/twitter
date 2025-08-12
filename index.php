<?php
session_start();
include('db.php');
 
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
 
$user_id = $_SESSION['user_id'];
 
// Handle tweet submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tweet'])) {
    $tweet = htmlspecialchars($_POST['tweet']);
    if (!empty($tweet)) {
        // Insert the tweet into the database
        $stmt = $pdo->prepare("INSERT INTO tweets (user_id, tweet) VALUES (?, ?)");
        $stmt->execute([$user_id, $tweet]);
    }
}
 
// Fetch all tweets in chronological order (oldest first) for the current user
$tweets = $pdo->query("SELECT tweets.id, tweets.tweet, tweets.created_at, users.username 
                       FROM tweets 
                       JOIN users ON tweets.user_id = users.id 
                       ORDER BY tweets.created_at DESC")->fetchAll();
 
// Handle like functionality
if (isset($_GET['like_tweet'])) {
    $tweet_id = $_GET['like_tweet'];
 
    // Check if the user has already liked the tweet
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE tweet_id = ? AND user_id = ?");
    $stmt->execute([$tweet_id, $user_id]);
    if ($stmt->rowCount() == 0) {
        // Insert like into the database
        $stmt = $pdo->prepare("INSERT INTO likes (tweet_id, user_id) VALUES (?, ?)");
        $stmt->execute([$tweet_id, $user_id]);
    }
    header('Location: index.php');
    exit();
}
 
// Handle share functionality
if (isset($_GET['share_tweet'])) {
    $tweet_id = $_GET['share_tweet'];
    $tweet_url = "http://yourwebsite.com/index.php?view_tweet=" . $tweet_id; // Customize with actual URL
    $_SESSION['shared_url'] = $tweet_url; // Store the shared URL in session
    header('Location: index.php');
    exit();
}
 
// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy(); // Destroy the session to log the user out
    header('Location: login.php'); // Redirect to login page after logout
    exit();
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Twitter</title>
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
            position: relative;
        }
        header h1 {
            margin: 0;
        }
        nav {
            position: absolute;
            top: 15px;
            left: 20px;
        }
        nav a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }
        .logout-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            background-color: transparent;
            color: white;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px;
        }
        .container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn {
            background-color: #1DA1F2;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 5px;
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
        .tweet-actions {
            margin-top: 10px;
        }
        .tweet-actions button {
            background-color: transparent;
            border: none;
            color: #1DA1F2;
            margin-right: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1><a href="index.php" style="color: white; text-decoration: none;">My Twitter</a></h1>
        <nav>
            <a href="profile.php">Profile</a>
        </nav>
        <a href="index.php?logout=true" class="logout-btn">Logout</a>
    </header>
 
    <div class="container">
        <!-- Tweet Form -->
        <form method="POST">
            <textarea name="tweet" placeholder="What's happening?" rows="3" required></textarea>
            <button type="submit" class="btn">Tweet</button>
        </form>
 
        <!-- Display Tweets -->
        <div class="tweets">
            <?php foreach ($tweets as $tweet): ?>
                <?php
                // Check the number of likes for each tweet
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE tweet_id = ?");
                $stmt->execute([$tweet['id']]);
                $like_count = $stmt->fetchColumn();
                ?>
 
                <div class="tweet">
                    <p class="username"><?php echo htmlspecialchars($tweet['username']); ?></p>
                    <p><?php echo htmlspecialchars($tweet['tweet']); ?></p>
                    <p><small><?php echo $tweet['created_at']; ?></small></p>
 
                    <div class="tweet-actions">
                        <!-- Like Button -->
                        <a href="index.php?like_tweet=<?php echo $tweet['id']; ?>" class="btn">Like (<?php echo $like_count; ?>)</a>
 
                        <!-- Share Button -->
                        <a href="index.php?share_tweet=<?php echo $tweet['id']; ?>" class="btn">Share</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
 
    <script>
        // Copy link functionality
        function copyLink(url) {
            const textarea = document.createElement("textarea");
            textarea.value = url;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand("copy");
            document.body.removeChild(textarea);
            alert("Link copied to clipboard!");
        }
    </script>
 
</body>
</html>
