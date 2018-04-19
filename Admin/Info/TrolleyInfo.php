<?php
session_start();
if ($_SESSION ?? '') {
    if ($_SESSION['name'] === 'admin') {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($_GET['trolley'] ?? '') {
            $id = $_GET['trolley'];
            require_once '../../Classes/Trolley.php';
            $trolley = Trolley::Get($id);
            if ($trolley) {
                echo("<!DOCTYPE html>
                <html>
                <head>
                    <meta charset='utf-8' />
                    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                    <title>Троллейбус {$trolley[0]->Number}</title>
                    <meta name='viewport' content='width=device-width, initial-scale=1'>
                    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css'>
                </head>
                <body>
                <div class='container'>");
                for ($i=0; $i < count($trolley); $i++) { 
                    echo("<div class='creator-container row'>
                    <form method='POST' class='col'>
                        <div class='form-group col-4'>
                            <label for='col-form-label'>Номер</label>
                            <input type='text' class='form-control' id='number' value='{$trolley[0]->Number}'>
                        </div>
                        <div class='form-group col-4'>
                            <label for='col-form-label'>Фотография</label>
                            <input type='file' class='form-control' id='photo'>
                        </div>
                        <div class='form-group col-4'>
                            <label for='col-form-label'>№ маршрута</label>
                            <input type='text' class='form-control' id='route' value='{$trolley[0]->Route}'>
                        </div>
                        <div class='form-group col-4'>
                            <label for='col-form-label'>Состояние</label>
                            <select type='text' class='form-control' id='statement' value='{$trolley[0]->Statement}'>
                                <option value='В ремонте'>В ремонте</option>
                                <option value='Рабочее'>Рабочее</option>
                            </select>
                        </div>
                        <button class='btn btn-primary row' id='btn-send' type='button'>Отправить</button>
                    </form>
                </div>");
                }
                echo('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                        <script src="../../Scripts/Admin/Info/trolley_info_scripts.js"></script>
                        </div>
                    </body>
                </html>');
            } else {
                header('location: ../trolleies.php');    
            }

        } else {
            header('location: ../trolleies.php');
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($_POST['trolley'] ?? '') {
            $new_trolley = json_decode($_POST['trolley']);
            require_once '../../Classes/Trolley.php';
            if ($_FILES['photo'] ?? '') {
                $photo = $_FILES['photo'];
                if (Trolley::Validate($new_trolley, $photo)) {
                    $trolley = new Trolley($new_trolley, $photo);
                    $trolley->Update($trolley);
                }
            } else {
                $photo = '';
                if (Trolley::Validate($new_trolley, $photo)) {
                    $trolley = new Trolley($new_trolley);
                    $trolley = $trolley->Update($trolley);
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