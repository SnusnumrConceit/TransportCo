<?php
session_start();
if ($_SESSION ?? '') {
    if ($_SESSION['name'] === 'admin') {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($_GET['emp'] ?? '') {
            require_once '../../Classes/Employee.php';
            $id = $_GET['emp'];
            $emp = Employee::Get($id);
            if ($emp ?? '') {                
                print <<<USER
<!DOCTYPE html>
<html>
    <head>
        <title>Пользователи</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../Styles/Admin/empinfo_styles.css">
    </head>
    <body>
         <div class="container">
                <a class="btn btn-default" href="../emps.php">Назад</a>
                <h2>Пользователь {$emp[0]->LName} {$emp[0]->FName} {$emp[0]->MName}</h2>
                <div class='row'>
                    <div class='emp-info-container col-4'>
                    <form method="POST">
                        <div class="form-group">
                            <label for="last-name">Фамилия</label>
                            <input type="text" class="form-control" id="last-name" value="{$emp[0]->LName}">
                        </div>
                        <div class="form-group">
                            <label for="first-name">Имя</label>
                            <input type="text" class="form-control" id="first-name" value="{$emp[0]->FName}">
                        </div>
                        <div class="form-group">
                            <label for="middle-name">Отчество</label>
                            <input type="text" class="form-control" id="middle-name" value="{$emp[0]->MName}">
                        </div>
                        <div class="form-group">
                            <label for="phone-number">Номер телефона</label>
                            <input type="text" class="form-control" id="phone-number" value="{$emp[0]->Phone}">
                        </div>
                        <div class="form-group">
                            <label for="birthday">Дата рождения</label>
                            <input type="text" class="form-control" id="birthday" value="{$emp[0]->Birthday}">
                        </div>
                        <button type="button" id="btn-send" class="btn btn-success">Отправить</button>    
                    </form>
                    </div>
                    <div class='taxes-creator-container col-4'>
                        <button class='btn btn-outline-danger' type='button' id='open-tax-creator-container'>Оштрафовать</button>
                        <div class='tax-creator-container'>
USER;
                    require_once '../../Classes/Tax.php';
                    $taxes = Tax::Show();
                    if ($taxes) {
                        $taxesLen = count($taxes);
                        for ($i=0; $i < $taxesLen; $i++) { 
                            echo("<div class='form-group'>
                                <label class='col-form-label'>
                                <input type='checkbox' value='{$taxes[$i]->id}'>{$taxes[$i]->Description}
                                </label>
                            </div>");
                        }
                        echo('<button class="btn btn-danger" id="btn-tax">Отправить</button>');
                    } else {
                        echo('<div>Прежде чем оштрафовать пользователя сначала создайте <i><a href="../taxes.php">Штрафы</a></i></div>');
                    }
                    echo("</div>
                    </div>
                <div class='col-4'>
                    <button class='btn btn-outline-primary' type='button' id='open-route-creator-container'>На маршрут</button>
                    <div class='routes-creator-container'>
                    <form method='POST'>
                    <section class='buses-container'>
                        <h4>Автобусы</h4>
                        <div class='row'>");
                        require_once '../../Classes/Bus.php';
                        $buses = Bus::GetWorking();
                        if ($buses) {
                            $busesLen = count($buses);
                            for ($i=0; $i < $busesLen; $i++) { 
                                echo("<div class='col'><label for='buses'><input value='{$buses[$i]->id}' type='radio'>{$buses[$i]->Number}</label>");
                            }
                        } else {
                            echo('<div class="col">Все автобусы сломаны. <i><a href="../Buses.php">Подробнее</a></i></div>');
                        }

                        echo("</div>
                    </section>
                    <section class='gazelles-container'>
                        <h4>Маршрутные такси</h4>
                        <div class='row'>");
                        require_once '../../Classes/Gazelle.php';
                        $gazelles = Gazelle::GetWorking();
                        if ($gazelles) {
                            $gazellesLen = count($gazelles);
                            for ($i=0; $i < $gazellesLen; $i++) { 
                                echo("<div class='col'><label for='gazelles'><input value='{$gazelles[$i]->id}' type='radio'>{$gazelles[$i]->Number}</label>");
                            }
                        } else {
                            echo('<div class="col">Все маршрутные такси сломаны. <i><a href="../gazelles.php">Подробнее</a></i></div>');
                        }
                        echo("</div>
                    </section>
                    <section class='trams-contrainer'>
                        <h4>Трамваи</h4>
                        <div class='row'>");
                        require_once '../../Classes/Tram.php';
                        $trams = Tram::GetWorking();
                        if ($trams) {
                            $tramsLen = count($trams);
                            for ($i=0; $i < $tramsLen; $i++) { 
                                echo("<div class='col'><label for='trams'><input value='{$trams[$i]->id}' type='radio'>{$trams[$i]->Number}</label>");
                            }
                        } else {
                            echo('<div class="col">Все трамваи сломаны. <i><a href="../trams.php">Подробнее</a></i></div>');
                        }
                        echo("</div>
                    </section>
                    <section class='trolleies-container'>
                        <h4>Троллейбусы</h4>
                        <div class='row'>");
                        require_once '../../Classes/Trolley.php';
                        $trolleies = Trolley::GetWorking();
                        if ($trolleies) {
                            $trolleiesLen = count($trolleies);
                            for ($i=0; $i < $trolleiesLen; $i++) { 
                                echo("<div class='col'><label for='trolleies'><input value='{$trolleies[$i]->id}' type='radio'>{$trolleies[$i]->Number}</label>");
                            }
                        } else {
                            echo('<div class="col">Все троллейбусы сломаны.  <i><a href="../trolleies.php">Подробнее</a></i></div>');
                        }
                        echo("</div>
                    </section>
                    <button type='button' class='btn btn-primary' id='btn-route'>Назначить</button>
                    </form>
                    </div>
                </div>
                </div>
                <div class='row'>
                    <div class='taxes-container col-sm-5'>
                    <h3>Штрафы</h3>");

                $empTaxes = Employee::GetTaxes($id);
                if ($empTaxes) {
                    $empTaxesLen = count($empTaxes);
                    $list = '';
                    $resultTax = 0;
                    for ($i=0; $i < $empTaxesLen; $i++) { 
                        $list.="<li><span class='d-none'>{$empTaxes[$i]->id}</span>{$empTaxes[$i]->Description}: {$empTaxes[$i]->Size} руб. <button class='btn btn-sm btn-danger btn-tax-delete'>Удалить</button></li>";
                        $resultTax += $empTaxes[$i]->Size;
                    }
                    echo('<ul class="col">'.$list."</ul><h4>Итого: {$resultTax} руб.</h4>");
                } else {
                    echo('<div>Штрафы отсутствуют</div>');
                }
                
            echo('</div>
                    <div class="route-container col-sm-6">
                        <h3>Маршрут</h3>');
                    $route = Employee::GetRoute($id);
                        if ($route) {
                            for ($i=0; $i < count($route); $i++) { 
                                echo("<div class='route-info'>
                                    <div><span class='d-none'>{$route[0]->Transport}</span>{$route[0]->Employee} {$route[0]->Number} №{$route[0]->Route}<button class='btn btn-sm btn-warning' id='btn-remove-route'>Снять с маршрута</button></div>
                                </div>");
                            }
                        } else {
                            echo('<div>Не назначен на маршрут.</div>');
                        }
                        
                        
            echo('</div>
                </div>
            </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="../../Scripts/Admin/Info/emp_info_scripts.js"></script>      
    </body>
</html>');
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['emp'] ?? '') {
        require_once '../../Classes/Employee.php';
        $emp = json_decode($_POST['emp']);
        if (Employee::Validate($emp)) {
            $emp = new Employee($emp);
            $emp->Update($emp);
        }
    }
    if ($_POST['taxes'] ?? '') {
        require_once '../../Classes/Employee.php';
        $taxes = json_decode($_POST['taxes']);
        Employee::AddTaxes($taxes);
    }
    if ($_POST['tax'] ?? '') {
        require_once '../../Classes/Employee.php';
        $tax = $_POST['tax'];
        Employee::RemoveTax($tax);
    }
}
} else {
    header('location: ../index.php');
}
} else {
header('location: ../index.php');
}