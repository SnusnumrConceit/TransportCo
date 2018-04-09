<?php
/*session_start();
    if ($_SESSION ?? '') {
        if ($_SESSION['name'] === 'admin') {*/
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {        
        if ($_POST['emp'] ?? '') {
            $inputData = json_decode($_POST['emp']);                        
            require_once '../Classes/Employee.php';
            $emp = new Employee();
            if($emp->CheckData($inputData)) {
                $emp = $emp->SetData($inputData);
                $emp->Create($emp);
            }            
        } 
        elseif ($_POST['id'] ?? '') {
            $id = $_POST['id'];            
            require_once '../Classes/Employee.php';
            $emp = new Employee();
            $emp->Delete($id);
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
            <div>
                <button id="btn-open-create-emp-container" class="btn btn-success">Добавить</button>
                <a class="btn btn-default" href="admin.php">На главную</a>
            </div>
            <div class="form-group create-emp-container">
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
                        <label for="phone-number">Номер телефона</label>
                        <input type="text" class="form-control" id="phone-number">
                    </div>
                    <div class="form-group">
                        <label for="birthday">Дата рождения</label>
                        <input type="text" class="form-control" id="birthday">
                    </div>
                    <button type="button" id="btnSubmit" class="btn btn-success">Отправить</button>    
                </form>
            </div>
            <div class="find-emp-container">                
                <form method="GET">
                    <input class="form-control" type="text" id="emp" value="{$inputData}" placeholder="Введите фамилию работника">
                    <button id="btn-find-emp" class="btn btn-primary">Найти</button>
                </form>
            </div>
            <div>
                    <h2>Работники</h2>
USER;
                    require_once '../Classes/Employee.php';
                    $emp = new Employee();
                    $findlessEmployees = $emp->Find($inputData);            
                    if ($findlessEmployees) {
                        $empsLength = count($findlessEmployees);
                        print "<table class=\"table table-bordered\">
                                        <thead>
                                            <th>id</th>
                                            <th>Фамилия</th>
                                            <th>Имя</th>
                                            <th>Отчество</th>
                                            <th>Номер телефона</th>
                                            <th>Дата рождения</th>
                                            <th>Операции</th>
                                        </thead>
                                        <tbody>";

                    for ($i=0; $i < $empsLength; $i++) { 
                        print "<tr>
                                <td>{$findlessEmployees[$i]->id}</td>
                                <td>{$findlessEmployees[$i]->Login}</td>
                                <td>{$findlessEmployees[$i]->LName}</td>
                                <td>{$findlessEmployees[$i]->FName}</td>
                                <td>{$findlessEmployees[$i]->MName}</td>
                                <td>{$findlessEmployees[$i]->Phone}</td>
                                <td><button class=\"btn btn-warning\">Изменить</button><button class=\"btn btn-danger\">Удалить</button></td>
                                </tr>";
                    }
                    print "</tbody>
                </div>
            </div>
        </div>  
        <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js\"></script>
        <script src=\"../Scripts/emps_scripts.js\"></script>      
    </body>
</html>";
            } else {
                print "<div>По запросу <i>{$inputData}</i> не найдено ни одного работника</div>
                </div>
            </div>
        </div>  
        <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js\"></script>
        <script src=\"../Scripts/emps_scripts.js\"></script>      
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
            <div>
                <button id="btn-open-create-emp-container" class="btn btn-success">Добавить</button>
                <a class="btn btn-default" href="admin.php">На главную</a>
            </div>
            <div class="form-group create-emp-container">
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
                        <label for="phone-number">Номер телефона</label>
                        <input type="text" class="form-control" id="phone-number">
                    </div>
                    <div class="form-group">
                        <label for="birthday">Дата рождения</label>
                        <input type="text" class="form-control" id="birthday">
                    </div>
                    <button type="button" id="btnSubmit" class="btn btn-success">Отправить</button>    
                </form>
            </div>
            <div class="find-emp-container">                
                <form method="GET">
                    <input class="form-control" type="text" id="emp" placeholder="Введите фамилию работника">
                    <button id="btn-find-emp" class="btn btn-primary">Найти</button>
                </form>
            </div>
            <div>
                <h2>Работники</h2>
USERS;
                        require_once '../Classes/Employee.php';
                        $emp = new Employee();
                        $result = $emp->Show();
                        if($result){
                            print "<table class=\"table table-bordered\">
                                    <thead>
                                        <th>id</th>
                                        <th>Логин</th>
                                        <th>Фамилия</th>
                                        <th>Имя</th>
                                        <th>Отчество</th>
                                        <th>Номер телефона</th>
                                        <th>Операции</th>
                                    </thead>
                                    <tbody>";
                            for ($i=0; $i < count($result); $i++) { 
                                print "<tr>
                                            <td>{$result[$i]->id}</td>
                                            <td>{$result[$i]->Login}</td>                                            
                                            <td>{$result[$i]->LName}</td>
                                            <td>{$result[$i]->FName}</td>
                                            <td>{$result[$i]->MName}</td>
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
        <script src=\"../Scripts/emps_scripts.js\"></script>      
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