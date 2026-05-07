<?php
$conn = @mysqli_connect("localhost", "root", "", "it_db");
$id = 0;

if(isset($_GET['id'])){
    $id = $_GET['id'];
}

$result = false;

if($conn){
    $result = @mysqli_query($conn, "SELECT * FROM services WHERE id = $id");
}

$row = false;

if($result){
    $row = mysqli_fetch_array($result);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Service</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background: #f4f6fb;
            margin: 0;
            color: #1a2540;
        }

        .nav{
            background: #0f1f3d;
            padding: 18px 50px;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .nav span{
            color: #7aaeff;
        }

        .box{
            width: 650px;
            max-width: 90%;
            margin: 50px auto;
            background: white;
            border: 1px solid #dde3ef;
            border-radius: 14px;
            padding: 35px;
            box-shadow: 0 4px 18px rgba(15,31,61,0.08);
        }

        img{
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        h1{
            font-weight: normal;
            margin-bottom: 15px;
        }

        p{
            line-height: 1.6;
            color: #6b7a99;
        }

        .price{
            color: #2f6bff;
            font-size: 25px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="nav">Tech<span>Solutions</span></div>

<?php if($row){ ?>
<div class="box">
    <img src="assets/<?php echo $row['icon']; ?>" alt="">
    <h1><?php echo $row['name']; ?></h1>
    <p><?php echo $row['details']; ?></p>
    <div class="price"><?php echo $row['price']; ?></div>
</div>
<?php } ?>

</body>
</html>
