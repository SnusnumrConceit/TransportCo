<?php
session_start();
if ($_SESSION ?? '') {
    if ($_SESSION['name'] === 'admin') {
 if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($_GET['desc'] ?? '') {
            require_once '../Classes/Tax.php';
            $desc = $_GET['desc'];
            $taxes = Tax::Find($desc);

######_____________ПОИСКОВАЯ_____VIEW___________########
print <<<POST
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Штрафы</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <div class="functional-container row">
                <button class="btn btn-success col-1" id="btn-open-container">Добавить</button>
                <a class="btn btn-default offset-sm-1" href="admin.php">На главную</a>
                <form method="GET" class="form-inline col" id="find-input">
                    <div class="form group offset-sm-4">
                        <input type="text" class="form-control" placeholder="Введите описание" value="{$desc}">
                        <button class="btn btn-primary">Найти</button>
                    </div>
                </form>
            </div>
            <div class="creator-container row">
                <form method="POST" class="col">
                <div class="form-group col-4">
                        <label class="col-form-label">Штраф</label>
                        <input type="text" class="form-control" id="tax">
                </div>    
                <div class="form-group col-4">
                        <label class="col-form-label" for='size'>Размер</label>
                        <input type="text" class="form-control" id="size">
                    </div>
                    <button class="btn btn-primary row" id="btn-send" type="button">Отправить</button>
                </form>
            </div>
            <div class="row">
POST;
            if ($taxes) {
                echo("<table class='table table-hover'>
                        <thead class='thead-dark'>
                            <th class='d-none'></th>
                            <th>Описание</th>
                            <th>Размер</th>
                            <th>Операции</th>
                        </thead>
                        <tbody>");
                $taxesLength = count($taxes);
                for ($i=0; $i < $taxesLength; $i++) {
                    echo("<tr>
                            <td class='d-none'>{$taxes[$i]->id}</td>
                            <td>{$taxes[$i]->Description}</td>
                            <td>{$taxes[$i]->Size}</td>
                            <td>
                                <button class='btn btn-warning'>Изменить</button>
                                <button class='btn btn-danger'>Удалить</button>
                            </td>
                        </tr>");
                }
                        echo("</tbody>
                    </table>");
            } else {
                echo("<div>По запросу <i>{$desc}</i> ничего не найдено");
            }
            echo("</div>
        </div>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
        <script src='../Scripts/Admin/taxes_scripts.js'></script>
    </body>
</html>");

        }
        else {


########______ОСНОВНАЯ___VIEW______######
print <<<POST
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Штрафы</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
        <style>
            thead {
                background-color: #d91717;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="functional-container row">
                <button class="btn btn-success col-1" id="btn-open-container">Добавить</button>
                <a class="btn btn-default offset-sm-1" href="admin.php">На главную</a>
                <form method="GET" class="form-inline col">
                    <div class="form group offset-sm-4">
                        <input type="text" class="form-control" placeholder="Введите описание" id="find-input">
                        <button class="btn btn-primary" id="btn-find" type="button">Найти</button>
                    </div>
                </form>
            </div>
            <div class="creator-container row">
                <form method="POST" class="col">
                <div class="form-group col-4">
                        <label class="col-form-label">Штраф</label>
                        <input type="text" class="form-control" id="tax">
                </div>    
                <div class="form-group col-4">
                        <label class="col-form-label" for='size'>Размер</label>
                        <input type="text" class="form-control" id="size">
                    </div>
                    <button class="btn btn-primary row" id="btn-send" type="button">Отправить</button>
                </form>
            </div>
            <div class="row">
POST;
            require_once '../Classes/Tax.php';
            $taxes = Tax::Show();

            if ($taxes) {
                echo("<table class='table table-hover'>
                        <thead class='thead-dark' class='thead class='thead-dark'-dark'>
                            <th class='d-none'></th>
                            <th>Описание</th>
                            <th>Размер</th>
                            <th>Операции</th>
                        </thead class='thead-dark'>
                        <tbody>");
                $taxesLength = count($taxes);
                for ($i=0; $i < $taxesLength; $i++) {
                    echo("<tr>
                            <td class='d-none'>{$taxes[$i]->id}</td>
                            <td>{$taxes[$i]->Description}</td>
                            <td>{$taxes[$i]->Size}</td>
                            <td>
                                <button class='btn btn-warning'>Изменить</button>
                                <button class='btn btn-danger'>Удалить</button>
                            </td>
                        </tr>");
                }
                echo("</tbody>
                    </table>");
            } else {
                echo("<div>Вы не создали ни одного штрафа!");
            }
            echo("</div>
        </div>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
        <script src='../Scripts/Admin/taxes_scripts.js'></script>
    </body>
</html>");
        }
 }  elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($_POST['tax'] ?? '') {
                $inputData = json_decode($_POST['tax']);
                require_once '../Classes/Tax.php';
                if (Tax::Validate($inputData)) {
                    $tax = new Tax($inputData);
                    $tax->Create($tax);
                }
        } elseif ($_POST['id'] ?? '') {
            require_once '../Classes/Tax.php';
            $id = $_POST['id'];
            Tax::Delete($id);
        }
    } else {
        header('location: index.php');
    }
} else {
    header('location: ../index.php');
}
} else {
header('location: ../index.php');
}
?>