<?php
  // For Database Connection
  require_once 'authentication/connection.php';
  require_once 'authentication/setup.php';

  // To be worked on later
  //create_db($conn, 'sh_project_group_yellow');
  //create_table();
  
  // For Login and Registration
  // Start the session
  session_start();

  // Check if the user is logged in
  if(isset($_SESSION["user_login"])) {
    header("location:welcome.php");
  }

  // When the LOGIN button is clicked
  if(isset($_REQUEST["login_btn"])) {
    $username = htmlspecialchars($_REQUEST["username"]);
    $password = htmlspecialchars($_REQUEST["password"]);

    if(empty($username)) {
      $errorMsg[] = "Please enter your username";
    } else if(empty($password)) {
      $errorMsg[] = "Please enter your password";
    } else {
      try {
        $select_stmt = $conn->prepare("SELECT * FROM users WHERE username=:uname");
        $select_stmt->execute(array(':uname'=>$username));
        $user_row = $select_stmt->fetch(PDO::FETCH_ASSOC);
        // Check that there was a result for the
        // username provided
        if($select_stmt->rowCount() > 0) {
          if($username==$user_row["username"]) {
            if(password_verify($password, $user_row["password"])) {
              // Set the session
              $_SESSION["user_login"] = $user_row["user_id"];
              $_SESSION['loginMsg'] = "Successful Login";
              header("refresh:2; user-dashboard/dashboard.php");
            } else {
              $errorMsg[] = "Wrong Username/Password";
            }
          } else {
            $errorMsg[] = "Wrong Username/Password";
          }
        } else {
          $errorMsg[] = "Wrong Username/Password";
        }
      } catch(PDOEXCEPTION $e) {
        $e->getMessage();
      }
    }
  }

  if(isset($_REQUEST['reg_btn'])) {
    // Collect Username, Password and Email
    // from login form
    $reg_username = htmlspecialchars($_REQUEST['reg_username']);
    $reg_email = htmlspecialchars($_REQUEST['reg_email']);;
    $reg_password = htmlspecialchars($_REQUEST['reg_password']);;

    if(empty($reg_username)) {
        $reg_errorMsg[] = "Please enter username";
    } else if(empty($reg_email)) {
        $reg_errorMsg[] = "Please enter email";
    } else if(!filter_var($reg_email, FILTER_VALIDATE_EMAIL)) {
        $reg_errorMsg[] = "Please enter a valid email address";
    } else if(empty($reg_password)) {
        $reg_errorMsg[] = "Please enter password";
    } else if(strlen($reg_password) < 6) {
        $reg_errorMsg[] = "Password must be atleast 6 characters long";
    } else {
        try {
          $select_stmt = $conn->prepare("SELECT username, email FROM users WHERE username=:uname OR email=:uemail");
          $select_stmt->execute(array(':uname'=>$reg_username, ":uemail"=>$reg_email));
          $reg_row = $select_stmt->fetch(PDO::FETCH_ASSOC);

          if($reg_row['reg_username']==$reg_username) {
            $reg_errorMsg[] = "Sorry, Username Already Exist";
          } else if ($reg_row['reg_email']==$reg_email) {
              $reg_errorMsg[] = "Sorry, Email Already Exist";
          } else if(!isset($reg_errorMsg)) {
              $hashed_password = password_hash($reg_password, PASSWORD_DEFAULT);

              $insert_data = "INSERT INTO `users` (username, email, password) VALUES (:uname, :uemail, :upassword)";
              $insert_reg_data = $conn->prepare($insert_data);

              if($insert_reg_data->execute(array(':uname'=>$reg_username, ':uemail'=>$reg_email, ':upassword'=>$hashed_password))) {
                $reg_errorMsg[] = "Registration Successful... Please Login";
              }
          }
        } catch(PDOException $e) {
            $e->getMessage();
        }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css"/>
    
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/custom.css" />

    <!-- project title -->
    <title>Registration Form</title>

    <!-- project name and description -->
    <meta name="Registration" description="user login and sign up page">

  </head>
  <!-- end of head -->

  <body>
    <div class="container">
      <div class="forms-container">
        <div class="signin-signup">
          <form action=<?php echo $_SERVER["PHP_SELF"]; ?> method="post" class="sign-in-form">
            <h2 class="title">Sign in</h2>
            <?php
              //Handle the error messages while trying to login
              if(isset($errorMsg)) {
                foreach($errorMsg as $error) {
                  echo "<p style='color:red;'>$error</p>";
                }
              }
            ?>
            <div class="input-field">
              <i class="fas fa-user"></i>
              <input type="text" name="username" placeholder="Username" />
            </div>
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" placeholder="Password" />
            </div>
            <input type="submit" name="login_btn" value="Login" class="btn solid" />
            <p class="social-text">Or </p>
            <div class="social-media">
              <a href="#" class="social-icon">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a href="#" class="social-icon">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="#" class="social-icon">
                <i class="fab fa-google"></i>
              </a>
              <a href="#" class="social-icon">
                <i class="fab fa-linkedin-in"></i>
              </a>
            </div>
          </form>
          <form action=<?php echo $_SERVER["PHP_SELF"]; ?> method="post" class="sign-up-form">
            <h2 class="title">Sign up</h2>
            <?php
              //Handle the error messages while trying to login
              if(isset($reg_errorMsg)) {
                foreach($reg_errorMsg as $error) {
                  echo "<p style='color:red;'>$error</p>";
                }
              }
            ?>
            <div class="input-field">
              <i class="fas fa-user"></i>
              <input type="text" name="reg_username" placeholder="Username" />
            </div>
            <div class="input-field">
              <i class="fas fa-envelope"></i>
              <input type="email" name="reg_email" placeholder="E-mail" />
            </div>
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input type="password" name="reg_password" placeholder="Password" />
            </div>
            <input type="submit" name="reg_btn" class="btn" value="Sign up" />
            <p class="social-text">Or </p>
            <div class="social-media">
              <a href="#" class="social-icon">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a href="#" class="social-icon">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="#" class="social-icon">
                <i class="fab fa-google"></i>
              </a>
              <a href="#" class="social-icon">
                <i class="fab fa-linkedin-in"></i>
              </a>
            </div>
          </form>
        </div>
      </div>

      <div class="panels-container">
        <div class="panel left-panel">
          <div class="content">
            <h3>New here ?</h3>
            <p>
              Click on the SideHustle Team Yellow
              Sign Up Button Below
            </p>
            <button class="btn transparent" id="sign-up-btn">
              Sign up
            </button>
          </div>
          <img src="assets/img/log.svg" class="image" alt="" />
        </div>
        <div class="panel right-panel">
          <div class="content">
            <h3>One of us ?</h3>
            <p>
              Click on the SideHustle Team Yellow
              Sign In Button Below
            </p>
            <button class="btn transparent" id="sign-in-btn">
              Sign in
            </button>
          </div>
          <img src="assets/img/register.svg" class="image" alt="" />
        </div>
      </div>
    </div>

    <script src="assets/js/app.js"></script>
  </body>
</html>
