<?php
session_start();
include 'database.php';

// Periksa jika pengguna sudah login, maka arahkan ke dashboard
if(isset($_SESSION['memberid'])){
    header("location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari form login
    $memberid = $_POST['memberid'];
    $pin = $_POST['pin'];
    $password = $_POST['password'];

    // Query untuk memeriksa apakah memberid dan pin sesuai
    $sql = "SELECT * FROM member WHERE memberid='$memberid' AND pin='$pin'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Jika memberid dan pin sesuai, periksa password
        $row = $result->fetch_assoc();
        if ($password == $row['password']) {
            // Jika password benar, buat sesi dan redirect ke halaman dashboard
            $_SESSION['memberid'] = $memberid;
            header("location: dashboard.php");
            exit;
        } else {
            $error = "Password salah";
        }
    } else {
        $error = "Member ID atau PIN salah";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            <?php if(isset($error)) { ?>
                <p class="error"><?php echo $error; ?></p>
            <?php } ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="input-group">
                    <input type="text" id="memberid" name="memberid" required>
                    <label for="memberid">Member ID</label>
                </div>
                <div class="input-group">
                    <input type="password" id="pin" name="pin" required>
                    <label for="pin">PIN</label>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" required>
                    <label for="password">Password</label>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
