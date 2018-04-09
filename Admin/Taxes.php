<?php
 if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($_GET['desc'] ?? '') {
            require_once '../Classes/Tax.php';
            $desc = $_GET['desc'];
            $tax = new Tax();
            $taxes = $tax->Find($desc);

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
                <button class="btn btn-secondary offset-sm-1" id="btn-open-container">На главную</button>
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
                        <label class="col-form-label" for='size'>Размер</label>
                        <input type="text" class="form-control" id="size">
                    </div>
                    <div class="form-group col-4">
                        <label class="col-form-label">Штраф</label>
                        <select type="text" class="form-control" id="tax">
                            <option value='Превышение 20 км/ч'>Превышение 20 км/ч</option>
                            <option value='Превышение 40 км/ч'>Превышение 40 км/ч</option>
                            <option value='Превышение 60 км/ч'>Превышение 60 км/ч</option>
                            <option value='Проезд на красный свет'>Проезд на красный свет</option>
                            <option value='Несоблюдение дистанции'>Несоблюдение дистанции</option>
                        </select>
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
    </head>
    <body>
        <div class="container">
            <div class="functional-container row">
                <button class="btn btn-success col-1" id="btn-open-container">Добавить</button>
                <button class="btn btn-secondary offset-sm-1" id="btn-open-container">На главную</button>
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
                        <label class="col-form-label" for='size'>Размер</label>
                        <input type="text" class="form-control" id="size">
                    </div>
                    <div class="form-group col-4">
                        <label class="col-form-label">Штраф</label>
                        <select type="text" class="form-control" id="tax">
                            <option value='Превышение 20 км/ч'>Превышение 20 км/ч</option>
                            <option value='Превышение 40 км/ч'>Превышение 40 км/ч</option>
                            <option value='Превышение 60 км/ч'>Превышение 60 км/ч</option>
                            <option value='Проезд на красный свет'>Проезд на красный свет</option>
                            <option value='Несоблюдение дистанции'>Несоблюдение дистанции</option>
                        </select>
                    </div>
                    <button class="btn btn-primary row" id="btn-send" type="button">Отправить</button>
                </form>
            </div>
            <div class="row">
POST;
            require_once '../Classes/Tax.php';
            $tax = new Tax();
            $taxes = $tax->Show();

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
                $tax = new Tax();
                if ($tax->Validate($inputData)) {
                    $tax = $tax->Set($inputData);
                    $tax->Create($tax);
                }
        } elseif ($_POST['id'] ?? '') {
            require_once '../Classes/Tax.php';
            $tax = new Tax();
            $id = $_POST['id'];
            $tax->Delete($id);
        }
    } else {
        header('location: index.php');
    }
    