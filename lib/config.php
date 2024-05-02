<?php
// Attempt to read from .env file
$ini = @parse_ini_file(".env");
if ($ini && isset($ini["DB_URL"])) {
    // Load local .env file if available
    $url = $ini["DB_URL"];
} else {
    // Load from environment variables (e.g., Heroku)
    $url = getenv("DB_URL");
    if (!$url) {
        error_log("Database URL not found in environment variables.");
        throw new Exception("Database configuration not found.");
    }
}

// Parse the database URL
$db_url = parse_url($url);

// Handle failure where parse_url does not parse properly
if (!$db_url || count($db_url) < 4) {
    error_log("Failed to parse DB_URL: $url");
    $pattern = "/mysql:\/\/(\w+):([\w\%]+)@([^:\/]+):(\d+)\/(\w+)/i";
    if (!preg_match($pattern, $url, $matches)) {
        error_log("Error parsing DB URL with regex.");
        throw new Exception("Invalid DB URL format.");
    }
    $db_url["host"] = $matches[3];
    $db_url["port"] = $matches[4]; // Including port in parsing
    $db_url["user"] = $matches[1];
    $db_url["pass"] = urldecode($matches[2]); // Decode the password in case of URL encoding
    $db_url["path"] = "/" . $matches[5];
}

// Extract components and set database configuration
if (!$db_url || count($db_url) < 4) {
    error_log("Failed to load database configuration.");
    throw new Exception("Failed to configure database, check the logs for further details");
} else {
    $dbhost = $db_url["host"];
    $dbuser = $db_url["user"];
    $dbpass = $db_url["pass"];
    $dbdatabase = substr($db_url["path"], 1); // Remove leading '/' from path
}

// Echo these variables only for debugging; remove or comment out in production
echo "Host: $dbhost, User: $dbuser, DB: $dbdatabase"; // This line should be removed or commented out in production
?>
