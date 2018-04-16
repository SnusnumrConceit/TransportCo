<?php
session_start();
if ($_SESSION ?? '') {
    if ($_SESSION['name'] === 'admin') {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($_GET['tram'] ?? '') {
            $id = $_GET['tram'];
            require_once '../../Classes/Tram.php';
            $tram = new Tram();
            $tram = $tram->Get($id);
            if ($tram) {
                echo('<!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8" />
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <title>Page Title</title>
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
                </head>
                <body>
                <div class="container">');
                for ($i=0; $i < count($tram); $i++) { 
                    echo("<div class='creator-container row'>
                    <form method='POST' class='col'>
                        <div class='form-group col-4'>
                            <label for='col-form-label'>Номер</label>
                            <input type='text' class='form-control' id='number' value='{$tram[0]->Number}'>
                        </div>
                        <div class='form-group col-4'>
                            <label for='col-form-label'>Фотография</label>
                            <input type='file' class='form-control' id='photo'>
                        </div>
                        <div class='form-group col-4'>
                            <label for='col-form-label'>№ маршрута</label>
                            <input type='text' class='form-control' id='route' value='{$tram[0]->Route}'>
                        </div>
                        <div class='form-group col-4'>
                            <label for='col-form-label'>Состояние</label>
                            <select type='text' class='form-control' id='statement' value='{$tram[0]->Statement}'>
                                <option value='В ремонте'>В ремонте</option>
                                <option value='Рабочее'>Рабочее</option>
                            </select>
                        </div>
                        <button class='btn btn-primary row' id='btn-send' type='button'>Отправить</button>
                    </form>
                </div>");
                }
                echo('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                        <script src="../../Scripts/Admin/Info/tram_info_scripts.js"></script>
                        </div>
                    </body>
                </html>');
            } else {
                header('location: ../trams.php');    
            }

        } else {
            header('location: ../trams.php');
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($_POST['tram'] ?? '') {
            $new_tram = json_decode($_POST['tram']);
            require_once '../../Classes/Tram.php';
            $tram = new Tram('');
            if ($_FILES['photo'] ?? '') {
                $photo = $_FILES['photo'];
                if ($tram->Validate($new_tram, $photo)) {
                    $tram = $tram->Set($new_tram, $photo);
                    $tram->Update($tram);
                }
            } else {
                $photo = '';
                if ($tram->Validate($new_tram, $photo)) {
                    $tram = $tram->Set($new_tram);
                    $tram = $tram->Update($tram);
                }
                
            }
            
        }
    } else {
        http_response_code(502);
    }
} else {
    header('location: ../index.php');
}
} else {
header('location: ../index.php');
}
?>