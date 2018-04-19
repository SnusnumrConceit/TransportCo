<?php
session_start();
if ($_SESSION ?? '') {
    if ($_SESSION['name'] === 'admin') {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($_GET['tax'] ?? '') {
            $id = $_GET['tax'];
            require_once '../../Classes/Tax.php';
            $tax = Tax::Get($id);
            if ($tax) {
                echo("<!DOCTYPE html>
                <html>
                <head>
                    <meta charset='utf-8' />
                    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                    <title>Штраф {$tax[0]->Description}</title>
                    <meta name='viewport' content='width=device-width, initial-scale=1'>
                    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css'>
                </head>
                <body>
                <div class='container'>
                <h2>{$tax[0]->Description}</h2>");
                for ($i=0; $i < count($tax); $i++) { 
                    echo("<div class='creator-container row'>
                    <form method='POST' class='col'>
                        <div class='form-group col-4'>
                            <label class='col-form-label' for='desc'>Описание</label>
                            <input type='text' class='form-control' id='desc' value='{$tax[0]->Description}'>
                        </div>
                        <div class='form-group col-4'>
                            <label class='col-form-label' for='size'>Размер</label>
                            <input type='text' class='form-control' id='size' value='{$tax[0]->Size}'>
                        </div>
                        <button class='btn btn-primary row' id='btn-send' type='button'>Отправить</button>
                    </form>
                </div>");
                }
                echo('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
                        <script src="../../Scripts/Admin/Info/tax_info_scripts.js"></script>
                        </div>
                    </body>
                </html>');
            } else {
                header('location: ../taxes.php');    
            }

        } else {
            header('location: ../taxes.php');
        }
        
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($_POST['tax'] ?? '') {
            $new_tax = json_decode($_POST['tax']);
            require_once '../../Classes/Tax.php';
            if (Tax::Validate($new_tax)) {
                $tax = new Tax($new_tax);
                $tax->Update($tax);
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