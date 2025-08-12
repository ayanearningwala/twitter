<?php
session_start();
include('db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch tweets of the logged-in user
$stmt = $pdo->prepare("SELECT * FROM tweets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tweets = $stmt->fetchAll();

// Handle profile update (if needed)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);

    // Update user profile
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->execute([$username, $email, $user_id]);

    // Reload the page after update
    header('Location: profile.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - My Twitter</title>
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
        .back-btn {
            position: absolute;
            left: 20px;
            top: 20px;
            background-color: transparent;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
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
    </style>
</head>
<body>
    <header>
        <button class="back-btn" onclick="window.location.href='index.php';">Back</button>
        <h1>My Twitter</h1>
    </header>

    <div class="container">
        <h2>Your Profile</h2>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <button type="submit" name="update_profile" class="btn">Update Profile</button>
        </form>

        <h3>Your Tweets</h3>
        <div class="tweets">
            <?php foreach ($tweets as $tweet): ?>
                <div class="tweet">
                    <p class="username"><?php echo htmlspecialchars($user['username']); ?></p>
                    <p><?php echo htmlspecialchars($tweet['tweet']); ?></p>
                    <p><small><?php echo $tweet['created_at']; ?></small></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
