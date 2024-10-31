<?php

session_start();

if (isset($_SESSION["user_id"])) {
    
    $mysqli = require __DIR__ . "/database.php";
    
    $sql = "SELECT * FROM user
            WHERE id = {$_SESSION["user_id"]}";
            
    $result = $mysqli->query($sql);
    
    $user = $result->fetch_assoc();
}

?>

<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $mysqli = require __DIR__ . "/database.php";
    
    $sql = sprintf("SELECT * FROM user
                    WHERE email = '%s'",
                   $mysqli->real_escape_string($_POST["email"]));
    
    $result = $mysqli->query($sql);
    
    $user = $result->fetch_assoc();
    
    if ($user) {
        
        if (password_verify($_POST["password"], $user["password_hash"])) {
            
            session_start();
            
            session_regenerate_id();
            
            $_SESSION["user_id"] = $user["id"];
            
            header("Location: index.php");
            exit;
        }
    }
    
    $is_invalid = true;
}

?>

<?php

if (empty($_POST["name"])) {
    die("Name is required");
}

if ( ! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if ( ! preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/database.php";

$sql = "INSERT INTO user (name, email, password_hash)
        VALUES (?, ?, ?)";
        
$stmt = $mysqli->stmt_init();

if ( ! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("sss",
                  $_POST["name"],
                  $_POST["email"],
                  $password_hash);
                  
if ($stmt->execute()) {

    header("Location: signup-success.html");
    exit;
    
} else {
    
    if ($mysqli->errno === 1062) {
        die("email already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holopets | Home</title>
    <link rel="icon" type="image/x-icon" href="logo.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap-grid.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container-fluid">
        
        <header class="header row">
            <nav class="navbar col-12">
                <a href="index.php">Home</a>
                <a href="About.php">About Us</a>
                <a href="Store.php">Store</a>
                <a href="Reviews.php">Reviews</a>
                <a href="FAQ.php">FAQ</a>
                <a href="Contact.php">Contact</a>
            </nav>
        </header>

        
        <div class="background row"></div>

        
        <div class="container">
            <div class="row content">
                <div class= "logo-section">
                    <h2 class="logo">
                        <img class="badge" src="Logo.png" alt="Logo" width="100px" height="100px">
                        HoloPets
                    </h2>
                </div>
                <div class ="text-sci">
                    <h2>Welcome!<br><span>To Our New Website.</span></h2>
                    <p>Here at HoloPets we strive to bring you the most unique experience by providing you with infinite possibilities...</p>
                    <div class="social-icons">
                        <a href="#"><i class='bx bxl-linkedin'></i></a>
                        <a href="#"><i class='bx bxl-github'></i></a>
                        <a href="#"><i class='bx bxl-instagram'></i></a>
                        <a href="#"><i class='bx bxl-twitter'></i></a>
                        <a href="#"><i class='bx bxl-facebook'></i></a>
                    </div>
                </div>
            </div>

            <!-- Login/Register Box Section -->
            <div class="row logreg-box">
                <div class="col-12 col-md-6 form-box login">
                    <form action="#" method="post">
                        <h2>Login</h2>
                            
                        <div class="input-box">
                            <span class="icon"><i class='bx bxs-envelope'></i></span>
                            <input type="email" name="email" id="email" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                            <label for="email">Email</label>
                        </div>
                        <div class="input-box">
                            <span class="icon"><i class='bx bxs-lock-alt'></i></span>
                            <input type="password" name="password" id="password">
                            <label for="password">Password</label>
                        </div>
                        <button type="submit" class="btn">Login</button>
                        <div class="login-register">
                            <p>Don't have an account? <a href="#" class="register-link">Sign Up</a></p>
                        </div>
                    </form>
                </div>

                <div class="col-12 col-md-6 form-box register">
                    <form action="#" method="post">
                        <h2>Sign Up</h2>
                        
                        <div class="input-box">
                            <span class="icon"><i class='bx bxs-user'></i></span>
                            <input type="text" name="uid" placeholder="username" required>
                        </div>
                        <div class="input-box">
                        <span class="icon"><i
                        class='bx bxs-envelope'></i></span>
                        <input type="text" name="mail" placeholder="E-mail" required>
                    </div>

                    <div class="input-box">
                        <span class="icon"><i
                        class='bx bxs-lock-alt'></i></span>
                        <input type="password" name="pwd" placeholder="Password" required>
                    </div>

                    <div class="input-box">
                        <span class="icon"><i class='bx bxs-lock-alt'></i></span>
                        <input type="password" name="pwd-repeat" placeholder="Repeat Password" required>
                    </div>
                        <button type="submit" class="btn" name="signup-submit">Sign Up</button>
                        <div class="login-register">
                            <p>Already have an account? <a href="#" class="login-link">Sign In</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</body>
</html>