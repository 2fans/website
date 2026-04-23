<!DOCTYPE html>
<html>
<head>
    <title>IT Solutions</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 40px; }
        h1 { text-align: center; color: #232f3e; margin-bottom: 40px; }
        
        /* The Grid Container */
        .services-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; 
            max-width: 1200px; 
            margin: auto; 
        }

        /* Individual Service Cards */
        .card { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            transition: transform 0.3s ease;
        }
        .card:hover { transform: translateY(-10px); }
        
        .card img { width: 80px; height: auto; margin-bottom: 15px; }
        .card h2 { font-size: 1.4em; color: #0056b3; margin: 10px 0; }
        .card p { color: #666; font-size: 0.9em; height: 50px; overflow: hidden; }
        
        .btn { 
            display: inline-block; 
            margin-top: 15px; 
            padding: 10px 20px; 
            background: #ff9900; /* AWS Orange */
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
        }
    </style>
</head>
<body>

    <h1>Our Professional IT Services</h1>

    <div class="services-grid">
<?php
$con = new mysqli('localhost', 'root', '', 'it_db');

$query = $con->query("SELECT * FROM services");

while($row = $query->fetch_assoc()) {
    echo "<div class='card'>";
    echo "<img src='assets/" . $row['icon'] . "' alt='icon'>";
    echo "<h2>" . $row['name'] . "</h2>";
    echo "<p>" . $row['details'] . "</p>";
    echo "<a href='details.php?id=" . $row['id'] . "' class='btn'>View Details</a>";
    echo "</div>";
}
?>
    </div>

</body>
</html>