<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
    <title>Админ-панель</title>
    <style>
        section {
            background-color:#d91717; 
            color: #fff; 
            cursor:pointer; 
            margin: 20px 0px; 
            text-align:center; 
            height: 150px;
            font-weight: bolder;
            font-family: 'Calibri';
            font-size: 2.2em;
            border-radius:5px;
            padding:45px;
        }
        .container {
            margin-top:100px;
        }
        section:hover {
            background-color: #000;
            cursor: pointer;
        }

        section:active {
            background-color: #000;
            cursor: pointer;
        }

        a:hover {
            color: #fff;
            text-decoration: none;
        }

        a:active {
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='row'>
            <section class='offset-sm-1 col-4'><a href='buses.php' class='text-white'>Автобусы</a></section>
            <section class='offset-sm-1 col-4'><a href='gazelles.php' class='text-white'>Маршрутки</a></section>
        </div>
        <div class='row'>
            <section class='col-3'><a href='emps.php' class='text-white'>Работники</a></section>
            <section class='offset-sm-1 col-3'><a href='routes.php' class='text-white'>Маршруты</a></section>
            <section class='offset-sm-1 col-3'><a href='taxes.php' class='text-white'>Штрафы</a></section>
        </div>
        <div class='row'>
            <section class='offset-sm-1 col-4'><a href='trolleies.php' class='text-white'>Троллейбусы</a></section>
            <section class='offset-sm-1 col-4'><a href='trams.php' class='text-white'>Трамваи</a></section>
        </div>
    </div>
</body>
</html>