<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
print <<<ENTER
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Вход</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
</head>
<body>
<div class="modal-dialog">
    <div class="modal-content">

    <div class="modal-header">
        <h4 class="modal-title">Вход</h4>
    </div>
    <div class="modal-body">
    <form method="POST">
        <div class="form-group">
        <label for="login">Логин</label>
        <input type="text" name="login" id="login" class="form-control">
        </div>
        <div class="form-group">
        <label for="pass">Пароль</label>
        <input type="password" name="pass" id="pass" class="form-control">
        </div>
    </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="btn-enter">Войти</button>
      </div>
</div>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
<script src='Scripts/Index/enter.js'></script>
</body>
</html>
ENTER;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['user'] ?? '') {
        $user = json_decode($_POST['user']);
        $logins = ['admin'];
        $passwords = ['admin'];
        $hashes = [];
        for ($i=0; $i < count($passwords); $i++) { 
            array_push($hashes, password_hash($passwords[$i], PASSWORD_DEFAULT));
        }
        function Validate ($user, $logins, $hashes)
        {
            function ValidateLogin ($login) {
                try {
                    if ($login ?? '') {
                        $loginLen = strlen($login);
                        if ($loginLen >= 5 && $loginLen <= 24) {
                            if (trim($login) === $login) {
                                if (htmlspecialchars($login) === $login) {
                                    preg_match('/[a-zA-Z][a-zA-Z0-9]+/', $login, $regLogin);
                                    if ($regLogin ?? '') {
                                        if ($regLogin[0] === $login) {
                                            return true;
                                        } else {
                                            throw new Exception("Uncorrect Login Error", 1);
                                        }
                                        
                                    } else {
                                        throw new Exception("Uncorrect Login Error", 1);
                                    }
                                    
                                } else {
                                    throw new Exception("Uncorrect Login Error", 1);
                                }
                                
                            } else {
                                throw new Exception("Uncorrect Login Error", 1);
                            }
                            
                        } else {
                            throw new Exception("Length Login Error", 1);
                        }
                        
                    } else {
                        throw new Exception("Empty Login Error", 1);
                    }
                    
                } catch (Exception $error) {
                    $errors['login'] = '';
                    if ($error->getMessage() === 'Empty Login Error') {
                        $errors['login'] = 'Вы не ввели логин!';
                    }
                    if ($error->getMessage() === 'Length Login Error') {
                        $errors['login'] = 'Логин должен быть длиной от 5 до 24 символов!';
                    }
                    if ($error->getMessage() === 'Uncorrect Login Error') {
                        $errors['login'] = 'Логин не может начинаться с цифры и должен состоять из цифр и английских букв!';
                    }
                    $errors = json_encode($errors);
                    echo($errors);
                }
            }
            function ValidatePass($pass) {
                try {
                    if ($pass ?? '') {
                        $passLen = strlen($pass);
                        if ($passLen >= 5 && $passLen <= 24) {
                            if (trim($pass) === $pass) {
                                if (htmlspecialchars($pass) === $pass) {
                                    preg_match('/[a-zA-Z0-9]+/', $pass, $regPass);
                                    if ($regPass ?? '') {
                                        if ($regPass[0] === $pass) {
                                            return true;
                                        } else {
                                            throw new Exception('Uncorrect Pass Error', 1);
                                        }
                                        
                                    } else {
                                        throw new Exception('Uncorrect Pass Error', 1);
                                    }
                                    
                                } else {
                                    throw new Exception('Uncorrect Pass Error', 1);
                                }
                                
                            } else {
                                throw new Exception('Uncorrect Pass Error', 1);
                            }
                            
                        } else {
                            throw new Exception('Length Pass Error', 1);
                        }
                        
                    } else {
                        throw new Exception('Empty Pass Error', 1);
                    }
                    

                } catch (Exception $error) {
                    $errors['pass'] = '';
                    if ($error->getMessage() === 'Empty Pass Error') {
                        $errors['pass'] = 'Вы не ввели пароль!';
                    }
                    if ($error->getMessage() === 'Length Pass Error') {
                        $errors['pass'] = 'Пароль должен быть длиной от 5 до 24 символов!';
                    }
                    if ($error->getMessage() === 'Uncorrect Pass Error') {
                        $errors['pass'] = 'Пароль должен состоять из цифр и английских букв!';
                    }
                    $errors = json_encode($errors);
                    echo($errors);
                }
            }
            if (ValidateLogin($user->login) && ValidatePass($user->pass)) {
                if (in_array($user->login, $logins) && in_array($user->pass, $hashes)) {
                    $_SESSION['name'] = 'admin';
                    header('location: Admin/admin.php');
                } else {
                    echo('Неверные данные!');
                }
            }
        }
        Validate($user, $logins, $passwords);
    } else {
        header('location: index.php');
    }
}