<?php
session_start();
if ($_SESSION ?? '') {
    if ($_SESSION['name'] === 'admin') {
 if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($_GET['number'] ?? '') {
            require_once '../Classes/Bus.php';
            $number = $_GET['number'];
            $buses = Bus::Find($number);
######_____________ПОИСКОВАЯ_____VIEW___________########
print <<<POST
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Автобусы</title>
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
                        <input type="text" class="form-control" placeholder="Введите номер" value="{$number}">
                        <button class="btn btn-primary">Найти</button>
                    </div>
                </form>
            </div>
            <div class="creator-container row">
               <form method="POST" class="col">
                    <div class="form-group col-4">
                        <label for="col-form-label">Номер</label>
                        <input type="text" class="form-control" id="number">
                    </div>
                    <div class="form-group col-4">
                        <label for="col-form-label">Фотография</label>
                        <input type="file" class="form-control" id="photo">
                    </div>
                    <div class="form-group col-4">
                        <label for="col-form-label">№ маршрута</label>
                        <input type="text" class="form-control" id="route">
                    </div>
                    <div class="form-group col-4">
                        <label for="col-form-label">Состояние</label>
                        <select type="text" class="form-control" id="statement">
                            <option value='В ремонте'>В ремонте</option>
                            <option value='Рабочее'>Рабочее</option>
                        </select>
                    </div>
                    <button class="btn btn-primary row" id="btn-send" type="button">Отправить</button>
                </form>
            </div>
            <div class="row">
POST;
            if ($buses) {
                echo("<table class='table table-hover'>
                        <thead class='thead-dark' class='thead class='thead-dark'-dark'>
                            <th class='d-none'></th>
                            <th>Номер</th>
                            <th>Фото</th>
                            <th>№ маршрута</th>
                            <th>Состояние</th>
                            <th>Операции</th>
                        </thead class='thead-dark'>
                        <tbody>");
                        $busesLength = count($buses);
                        require_once '../WideImage/lib/wideimage.php';
                        for ($i=0; $i < $busesLength; $i++) { 
                            $img = WideImage::load(base64_decode($buses[$i]->Photo));
                            $img = $img->resize(250,180);
                            $img = base64_encode($img);
                            echo("<tr>
                            <td class='d-none'>{$buses[$i]->id}</td>
                            <td>{$buses[$i]->Number}</td>
                            <td><img src='data:image/jpg;base64,{$img}'></td>
                            <td>{$buses[$i]->Route}</td>
                            <td>{$buses[$i]->Statement}</td>
                            <td>
                                <button class='btn btn-warning'>Изменить</button>
                                <button class='btn btn-danger'>Удалить</button>
                            </td>
                        </tr>");
                            
                        }
                        echo("</tbody>
                    </table>");
            } else {
                echo("<div>По запросу <i>{$number}</i> ничего не найдено");
            }
            echo("</div>
        </div>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
        <script src='https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js'></script>
        <script src='../Scripts/Admin/buses_scripts.js'></script>
    </body>
</html>");

        }
        else {
########______ОСНОВНАЯ___VIEW______######
print <<<POST
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Автобусы</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <div class="functional-container row">
                <button class="btn btn-success col-1" id="btn-open-container">Добавить</button>
                <a class="btn btn-default offset-sm-1" href="admin.php">На главную</a>
                <form method="GET" class="form-inline col">
                    <div class="form group offset-sm-4">
                        <input type="text" class="form-control" placeholder="Введите номер" id="find-input">
                        <button class="btn btn-primary" id="btn-find" type="button">Найти</button>
                    </div>
                </form>
            </div>
            <div class="creator-container row">
                <form method="POST" class="col">
                    <div class="form-group col-4">
                        <label for="col-form-label">Номер</label>
                        <input type="text" class="form-control" id="number">
                    </div>
                    <div class="form-group col-4">
                        <label for="col-form-label">Фотография</label>
                        <input type="file" class="form-control" id="photo">
                    </div>
                    <div class="form-group col-4">
                        <label for="col-form-label">№ маршрута</label>
                        <input type="text" class="form-control" id="route">
                    </div>
                    <div class="form-group col-4">
                        <label for="col-form-label">Состояние</label>
                        <select type="text" class="form-control" id="statement">
                            <option value='В ремонте'>В ремонте</option>
                            <option value='Рабочее'>Рабочее</option>
                        </select>
                    </div>
                    <button class="btn btn-primary row" id="btn-send" type="button">Отправить</button>
                </form>
            </div>
            <div class="row">
POST;
            require_once '../Classes/Bus.php';
            $buses = Bus::Show();

            if ($buses) {
                echo("<table class='table table-hover'>
                        <thead class='thead-dark' class='thead class='thead-dark'-dark'>
                            <th class='d-none'></th>
                            <th>Номер</th>
                            <th>Фото</th>
                            <th>№ маршрута</th>
                            <th>Состояние</th>
                            <th>Операции</th>
                        </thead class='thead-dark'>
                        <tbody>");
                $busesLength = count($buses);
                require_once '../Wideimage/lib/wideimage.php';
                for ($i=0; $i < $busesLength; $i++) {
                    $img = WideImage::load(base64_decode($buses[$i]->Photo));
                    $img = $img->resize(250, 180);
                    $img = base64_encode($img);
                    echo("<tr>
                            <td class='d-none'>{$buses[$i]->id}</td>
                            <td>{$buses[$i]->Number}</td>
                            <td><img src='data:image/jpg;base64,{$img}'></td>
                            <td>{$buses[$i]->Route}</td>
                            <td>{$buses[$i]->Statement}</td>
                            <td>
                                <button class='btn btn-warning'>Изменить</button>
                                <button class='btn btn-danger'>Удалить</button>
                            </td>
                        </tr>");
                }
                echo("</tbody>
                    </table>");
            } else {
                echo("<div>Вы не создали ни одного автобуса!");
            }
            echo("</div>
        </div>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
        <script src='https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js'></script>
        <script src='../Scripts/Admin/buses_scripts.js'></script>
    </body>
</html>");
        }
 }  elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($_POST['bus'] ?? '') {
            if ($_FILES['photo'] ?? '') {
                $photo = $_FILES['photo'];
                $inputData = json_decode($_POST['bus']);
                require_once '../Classes/Bus.php';
                
                if (Bus::Validate($inputData, $photo)) {
                    $bus = new Bus($inputData, $photo);
                    $bus->Create($bus);
                }
            } else {
                echo('Вы не загрузили фотографию!');
            }
            
        } elseif ($_POST['id'] ?? '') {
            require_once '../Classes/Bus.php';
            $id = $_POST['id'];
            Bus::Delete($id);
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