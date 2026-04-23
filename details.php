<!DOCTYPE html>
<html>
<head>
    <title>Service Details</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 0; }
        .container { width: 50%; margin: 50px auto; background: #fff; padding: 40px; border-radius: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; }
        img { width: 100px; margin-bottom: 20px; }
        h1 { color: #333; }
        .price { font-size: 1.5em; color: #2e7d32; font-weight: bold; margin: 20px 0; }
        .back-btn { display: inline-block; margin-top: 30px; padding: 10px 20px; background: #0056b3; color: white; text-decoration: none; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'it_db';

$con = new mysqli($host, $user, $pass, $db);

$id = $_GET['id'];

$result = $con->query("SELECT * FROM services WHERE id = $id");
$service = $result->fetch_assoc();

if($service) {
    echo "<img src='assets/" . $service['icon'] . "'>";
    echo "<h1>" . $service['name'] . "</h1>";
    echo "<p>" . $service['details'] . "</p>";
    echo "<div class='price'>Rate: " . $service['price'] . "</div>";
} else {
    echo "<h1>Service not found</h1>";
}

$con->close();
?>
        <a href="index.php" class="back-btn">Back to Catalog</a>
    </div>
</body>
</html>