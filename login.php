<?php
require_once(__DIR__ . "/../../partials/nav.php");
?>

<form onsubmit="return validate(this)" method="POST">
    <div>
         <label for="email">Email</label>
        <input id="email" type="email" name="email" required />
    </div>
  
    <input type="submit" value="login" />
</form>
<script>
    function validate(form) {
//TODO 1: implement JavaScript validation
//ensure it returns false for an error and true for success
        return true;
}
</script>
<?php
//TODO 2: add PHP Code
if(isset($_POST["email"]) && isset($_POST["password"])){
    $email = se($_POST, "email", "", false);//$_POST["email"];
    $password = se($_POST, "password", "", false); //$_POST["password"];
     
//TODO 3: 
$hasError = false;
if (empty($email)) {
    echo "Email must not be empty <br>";
    $hasError = true;
}
//sanitize
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
echo "please enter a valid email <br>";
$hasError = true;
}
if (empty($password)) {
    echo "password must not be empty <br>";
    $hasError = true;
}

if (strlen($password) < 8) {
    echo "Password too short <br>";
    $hasError = true;
}

if (!$hasError) {
    //echo "Welcome, $email";
//TODO4

$db = getDB();
$stmt = $db->prepare("SELECT email, password from Users where email = email");
try {
    $r = $stmt->execute([" email" => $email]);
    if ($r) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $hash = $user[ "password"];
            unset ($user["password" ]);
            if (password_verify($password, $hash)) {
                echo "Welcome $email";
                $_SESSION["user"] = $user;
                die(header("Location: home.php"));
            }
                else {
                    echo "Invalid password";
                } 
            } else
                {
                    echo "Email not found";
                }
            }
        }
        catch (Exception $e) {
            echo "<pre>" . var_export($e, true) . "</pre>";
        }
}
}
?> 