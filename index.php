<?php
$conn = @mysqli_connect("localhost", "root", "", "it_db");
$services = false;

if($conn){
    $services = @mysqli_query($conn, "SELECT * FROM services");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>IT Services</title>
    <style>
        *{
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body{
            font-family: Arial, sans-serif;
            background: #f4f6fb;
            color: #1a2540;
        }

        .nav{
            background: #0f1f3d;
            padding: 18px 50px;
            color: white;
            box-shadow: 0 2px 12px rgba(0,0,0,0.25);
        }

        .nav-logo{
            font-size: 24px;
            font-weight: bold;
        }

        .nav-logo span{
            color: #7aaeff;
        }

        .hero{
            background: linear-gradient(135deg, #0f1f3d, #1e3f78);
            color: white;
            padding: 65px 50px;
        }

        .hero h1{
            font-size: 44px;
            margin-bottom: 12px;
            font-weight: normal;
        }

        .hero p{
            color: #b9c9e6;
            font-size: 17px;
        }

        .page{
            width: 1100px;
            max-width: 95%;
            margin: 45px auto;
        }

        .section-title{
            font-size: 28px;
            margin-bottom: 25px;
            font-weight: normal;
        }

        .card-grid{
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .card{
            background: white;
            border: 1px solid #dde3ef;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(15,31,61,0.08);
        }

        .card-header{
            background: #0f1f3d;
            color: white;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .card-icon{
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.12);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-icon img{
            width: 30px;
            height: 30px;
            object-fit: contain;
        }

        .card-name{
            font-size: 21px;
        }

        .card-body{
            padding: 25px;
        }

        .card-desc{
            color: #6b7a99;
            line-height: 1.6;
            min-height: 75px;
            margin-bottom: 20px;
        }

        .card-footer{
            border-top: 1px solid #dde3ef;
            padding-top: 18px;
        }

        .price-label{
            color: #9aa7bc;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .price-value{
            color: #2f6bff;
            font-size: 24px;
            font-weight: bold;
        }

        @media(max-width: 800px){
            .card-grid{
                grid-template-columns: 1fr;
            }

            .hero h1{
                font-size: 34px;
            }
        }
    </style>
</head>
<body>

<div class="nav">
    <div class="nav-logo">Tech<span>Solutions</span></div>
</div>

<div class="hero">
    <h1>Professional IT Solutions</h1>
    <p>Cloud infrastructure, computer repair and network support.</p>
</div>

<div class="page">
    <h2 class="section-title">Available Services</h2>

    <div class="card-grid">
        <?php
        if($services){
            while($row = mysqli_fetch_array($services)){
        ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <img src="assets/<?php echo $row['icon']; ?>" alt="">
                    </div>
                    <div class="card-name"><?php echo $row['name']; ?></div>
                </div>
                <div class="card-body">
                    <p class="card-desc"><?php echo $row['details']; ?></p>
                    <div class="card-footer">
                        <div class="price-label">Starting from</div>
                        <div class="price-value"><?php echo $row['price']; ?></div>
                    </div>
                </div>
            </div>
        <?php
            }
        }
        ?>
    </div>
</div>

</body>
</html>
