<?php
// public_html/Project/register.php
require(__DIR__ . "/../../partials/nav.php");
reset_session();
?>
<div class="container-fluid">
    <h1>Register</h1>
    <form onsubmit="return validate(this)" method="POST">
        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" type="email" id="email" name="email" required />
        </div>
        <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input class="form-control" type="text" name="username" required maxlength="30" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="pw">Password</label>
            <input class="form-control" type="password" id="pw" name="password" required minlength="8" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="confirm">Confirm</label>
            <input class="form-control" type="password" name="confirm" required minlength="8" />
        </div>
        <input type="submit" class="mt-3 btn btn-primary" value="Register" />
    </form>
</div>
<script>
    function validate(form) {
        // Implement JavaScript validation
        // Ensure it returns false for an error and true for success
        return true;
    }
</script>
<?php
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"]) && isset($_POST["username"])) {
    $email = $_POST["email"];  // Simplified for example
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];
    $username = $_POST["username"];

    $hasError = false;
    if ($password !== $confirm) {
        flash("Passwords must match", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            flash("Successfully registered!", "success");
        } catch (Exception $e) {
            flash("Registration failed: " . $e->getMessage(), "danger");
        }
    }
}
require(__DIR__ . "/../../partials/footer.php");
?>
