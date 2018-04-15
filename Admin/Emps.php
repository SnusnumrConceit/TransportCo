<?php
/*session_start();
    if ($_SESSION ?? '') {
        if ($_SESSION['name'] === 'admin') {*/
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {        
        if ($_POST['emp'] ?? '') {
            $inputData = json_decode($_POST['emp']);                        
            require_once '../Classes/Employee.php';
            if(Employee::Validate($inputData)) {
                $emp = new Employee($inputData);
                $emp->Create($emp);
            }            
        } 
        elseif ($_POST['id'] ?? '') {
            $id = $_POST['id'];            
            require_once '../Classes/Employee.php';
            Employee::Delete($id);
        }        
        else {
            echo('Ничего не пришло');
        }        
    }
    #####_____ПОИСКОВАЯ VIEW________########
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($_GET['emp'] ?? '') {
            $inputData = $_GET['emp'];
                print <<<USER
<!DOCTYPE html>
<html>
    <head>
        <title>Работники</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
    </head>
    <body>
         <div class="container">
         <div class='row'>
                <button id="btn-open-container" class="btn btn-success">Добавить</button>
                <a class="btn btn-default" href="admin.php">На главную</a>
                <form method="GET" class='form-inline col'>
                    <div class="form-group find-emp-container offset-sm-4">                
                        <input class="form-control" type="text" id="find-input" placeholder="Введите фамилию" value="{$inputData}">
                        <button id="btn-find" class="btn btn-primary" type="button">Найти</button>
                    </div>
                </form>
            </div>
            <div class="form-group creator-container">
                <form method="POST">
                    <div class="form-group">
                        <label for="last-name">Фамилия</label>
                        <input type="text" class="form-control" id="last-name">
                    </div>
                    <div class="form-group">
                        <label for="first-name">Имя</label>
                        <input type="text" class="form-control" id="first-name">
                    </div>
                    <div class="form-group">
                        <label for="middle-name">Отчество</label>
                        <input type="text" class="form-control" id="middle-name">
                    </div>
                    <div class="form-group">
                        <label for="birthday">Дата рождения</label>
                        <input type="text" class="form-control" id="birthday">
                    </div>
                    <div class="form-group">
                        <label for="phone-number">Номер телефона</label>
                        <input type="text" class="form-control" id="phone-number">
                    </div>
                    <button type="button" id="btn-send" class="btn btn-success">Отправить</button>    
                </form>
            </div>
            <div>
                    <h2>Работники</h2>
USER;
                    require_once '../Classes/Employee.php';
                    $emp = Employee::Find($inputData);
                    if ($emp) {
                        $empsLength = count($emp);
                        print "<table class=\"table table-bordered\">
                                        <thead class='thead-dark'>
                                            <th class='d-none'></th>
                                            <th>Фамилия</th>
                                            <th>Имя</th>
                                            <th>Отчество</th>
                                            <th>Номер телефона</th>
                                            <th>Дата рождения</th>
                                            <th>Операции</th>
                                        </thead class='thead-dark'>
                                        <tbody>";

                    for ($i=0; $i < $empsLength; $i++) { 
                        print "<tr>
                                <td class='d-none'>{$emp[$i]->id}</td>
                                <td>{$emp[$i]->LName}</td>
                                <td>{$emp[$i]->FName}</td>
                                <td>{$emp[$i]->MName}</td>
                                <td>{$emp[$i]->Birthday}</td>
                                <td>{$emp[$i]->Phone}</td>
                                <td><button class=\"btn btn-warning\">Изменить</button><button class=\"btn btn-danger\">Удалить</button></td>
                                </tr>";
                    }
                    print "</tbody>
                </div>
            </div>
        </div>  
        <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js\"></script>
        <script src=\"https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js\"></script>
        <script src=\"../Scripts/Admin/emps_scripts.js\"></script>    
    </body>
</html>";
            } else {
                print "<div>По запросу <i>{$inputData}</i> не найдено ни одного работника</div>
                </div>
            </div>
        </div>  
        <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js\"></script>
        <script src=\"../Scripts/Admin/emps_scripts.js\"></script>      
    </body>
</html>";
            }
        }         
    #####_____ОСНОВНАЯ VIEW________########
    else {
print <<<USERS
<!DOCTYPE html>
<html>
    <head>
        <title>Работники</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
    </head>
    <body>
         <div class="container">
            <div class='row'>
                <button id="btn-open-container" class="btn btn-success">Добавить</button>
                <a class="btn btn-default" href="admin.php">На главную</a>
                <form method="GET" class='form-inline col'>
                    <div class="form-group find-emp-container offset-sm-4">                
                        <input class="form-control" type="text" id="find-input" placeholder="Введите фамилию">
                        <button id="btn-find" class="btn btn-primary" type="button">Найти</button>
                    </div>
                </form>
            </div>
            <div class="form-group creator-container">
                <form method="POST">
                    <div class="form-group">
                        <label for="last-name">Фамилия</label>
                        <input type="text" class="form-control" id="last-name">
                    </div>
                    <div class="form-group">
                        <label for="first-name">Имя</label>
                        <input type="text" class="form-control" id="first-name">
                    </div>
                    <div class="form-group">
                        <label for="middle-name">Отчество</label>
                        <input type="text" class="form-control" id="middle-name">
                    </div>
                    <div class="form-group">
                        <label for="birthday">Дата рождения</label>
                        <input type="text" class="form-control" id="birthday">
                    </div>
                    <div class="form-group">
                        <label for="phone-number">Номер телефона</label>
                        <input type="text" class="form-control" id="phone-number">
                    </div>
                    <button type="button" id="btn-send" class="btn btn-success">Отправить</button>    
                </form>
            </div>
            <div>
                <h2>Работники</h2>
USERS;
                        require_once '../Classes/Employee.php';
                        //$emp = new Employee();
                        $result = Employee::Show();
                        if($result){
                            print "<table class=\"table table-bordered\">
                                    <thead class='thead-dark'>
                                        <th class='d-none'></th>
                                        <th>Фамилия</th>
                                        <th>Имя</th>
                                        <th>Отчество</th>
                                        <th>Дата рождения</th>
                                        <th>Номер телефона</th>
                                        <th>Операции</th>
                                    </thead class='thead-dark'>
                                    <tbody>";
                            for ($i=0; $i < count($result); $i++) { 
                                print "<tr>
                                            <td class='d-none'>{$result[$i]->id}</td>
                                            <td>{$result[$i]->LName}</td>
                                            <td>{$result[$i]->FName}</td>
                                            <td>{$result[$i]->MName}</td>
                                            <td>{$result[$i]->Birthday}</td>
                                            <td>{$result[$i]->Phone}</td>
                                            <td><button class=\"btn btn-warning\">Изменить</button><button class=\"btn btn-danger\">Удалить</button></td>
                                        </tr>";
                            }
                        } else {
                            echo('Вы не создали ни одного работника');                            
                        }
print               "</tbody>
                </div>
            </div>
        </div>  
        <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js\"></script>
        <script src=\"https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js\"></script>
        <script src=\"../Scripts/Admin/emps_scripts.js\"></script>      
    </body>
</html>";
        }
    }
/*} else {
        header('location: ../enter.php');
    }
} else {
    header('location: ../enter.php');
}*/
?>