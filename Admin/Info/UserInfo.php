<?php
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($_GET['emp'] ?? '') {
            require_once '../../Classes/Employee.php';
            $emp = new Employee();
            $id = $_GET['emp'];
            $emp = $emp->Get($id);
            if ($emp ?? '') {                
                print <<<USER
<!DOCTYPE html>
<html>
    <head>
        <title>Пользователи</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    </head>
    <body>
         <div class="container">
                <a class="btn btn-default" href="../emps.php">Назад</a>
                <h2>Пользователь {$emp[0]->LName} {$emp[0]->FName} {$emp[0]->MName}</h2>
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
                    <button type="button" id="btnSubmitEdit" class="btn btn-success">Отправить</button>    
                </form>
            </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="../../Scripts/emp_info_scripts.js"></script>      
    </body>
</html>

USER;

        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['emp'] ?? '') {

    }
}

?>