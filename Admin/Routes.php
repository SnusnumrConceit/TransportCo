<?php
session_start();
if ($_SESSION ?? '') {
    if ($_SESSION['name'] === 'admin') {
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
print <<<ROUTES
    <!DOCTYPE html>
    <html>
        <head>
            <title>Маршруты</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
            </head>
        <body>
        <div class='container'>
        <a class="btn btn-default" href="admin.php">На главную</a>
        <h2>Маршруты</h2>
ROUTES;
    require_once '../Classes/Employee.php';
    $routes = Employee::ShowRoutes();
    if ($routes) {
        $routesLen = count($routes);
        print "<table class=\"table\">
        <thead class='thead-dark'>
            <th class='d-none'></th>
            <th>ФИО</th>
            <th>Номер маршрута</th>
            <th>Номер транспорта</th>
            <th>Операции</th>
        </thead class='thead-dark'>
        <tbody>";
        for ($i=0; $i < $routesLen; $i++) { 
            echo("<tr>
                    <td class='d-none'>{$routes[$i]->Emp_id}</td>
                    <td>{$routes[$i]->Employee}</td>
                    <td>{$routes[$i]->Route}</td>
                    <td>{$routes[$i]->Number}</td>
                    <td><button class='btn btn-warning'>Снять с маршрута</button></td>
                </tr>");
        }
        echo('</tbody></table>');
        echo('</div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="../Scripts/Admin/routes_scripts.js"></script>
        </body>
        </html>');
    } else {
        echo('<div>Ни один из <i><a href="emps.php">Работников</a></i> не назначен на маршрут!</div>');
    }
}
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['route'] ?? '') {
        $route = json_decode($_POST['route']);
        require_once '../Classes/Employee.php';
        Employee::AddRoute($route);
    }
    if ($_POST['emp'] ?? '') {
        require_once '../Classes/Employee.php';
        $emp = $_POST['emp'];
        Employee::RemoveRoute($emp);
    }
} else {
    echo('Ничего не пришло!');
}
} else {
    header('location: ../index.php');
}
} else {
header('location: ../index.php');
}
?>