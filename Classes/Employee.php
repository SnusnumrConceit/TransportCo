<?php
class Employee implements IEmployee{
    protected $id;
    protected $lastName;
    protected $firstName;
    protected $middleName;
    protected $phoneNumber;
    protected $birthday;

    public function __construct($emp) {
        if ($emp->id ?? '') {
            $this->id = $emp->id;
        } else {
            $this->id = uniqid();    
        }
        $this->lastName = $emp->lastName;
        $this->firstName = $emp->firstName;
        $this->middleName = $emp->middleName;
        $this->phoneNumber = $emp->phone;
        $this->birthday = $emp->birthday;
    }

    public function Create($emp)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($db, $emp, 'create')) {
            $createEmployeeQuery = $db->prepare("CALL spCreateEmp (?, ?, ?, ?, ?, ?)");
            $createEmployeeQuery->execute(array($emp->id, $emp->lastName, $emp->firstName, $emp->middleName, $emp->birthday, $emp->phoneNumber));
        }
    }

    static function Delete($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $deleteEmployeeQuery = $db->prepare("CALL spDeleteEmp(?)");
        $deleteEmployeeQuery->execute(array($id));        
    }

    static function Get($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $getEmployeeQuery = $db->prepare('SELECT * FROM vemps WHERE id = ?');
        $getEmployeeQuery->execute(array($id));
        $selectedEmployeeQuery = $getEmployeeQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($selectedEmployeeQuery) == 1)   {
            return $selectedEmployeeQuery;
        } else {
            return false;
        }
        
    }

    static function Find($lastName)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $findEmployeeQuery = $db->prepare('SELECT * FROM vemps WHERE LName = ?');
        $findEmployeeQuery->execute(array($lastName));
        $currentEmployee = $findEmployeeQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($currentEmployee) != 0) {
            return $currentEmployee;
        } else {
            return false;
        }
         
    }
    public function Update($emp)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($db, $emp, 'update')) {
            $updateEmployeeQuery = $db->prepare("CALL spUpdateEmp(?, ?, ?, ?, ?, ?)");
            $updateEmployeeQuery->execute(array($emp->lastName, $emp->firstName, $emp->middleName, $emp->birthday, $emp->phoneNumber, $emp->id));            
        }
    }

    public function Validate($emp)
    {
            function ValidateLName($lastName)
            {
                try {
                    if ($lastName ?? '') {
                        $lastNameLength = mb_strlen($lastName);
                        if ($lastNameLength >= 3 && $lastNameLength <= 30) {
                            if (htmlspecialchars($lastName) == $lastName) {
                                if (trim($lastName) == $lastName ) {
                                    preg_match('/[А-ЯЁ]{1}[а-яё]{2,}/u', $lastName, $regLastName);

                                    if ($regLastName ?? '') {
                                        if ($regLastName[0] == $lastName) {
                                            return true;
                                        } else {
                                            throw new Exception("Uncorrect LName Error", 1);
                                            
                                        }
                                        
                                    } else {
                                        throw new Exception("Uncorrect LName Error", 1);
                                    }
                                    
                                } else {
                                    throw new Exception("Uncorrect LName Error", 1);
                                }
                                
                            } else {
                                throw new Exception("Uncorrect LName Error", 1);
                            }
                            
                        } else {
                            throw new Exception("Length LName Error", 1);
                        }
                        
                    } else {
                        throw new Exception("Empty LName Error", 1);
                    }
                    
                } catch (Exception $error) {
                    if ($error->getMessage() === 'Empty LName Error') {
                        echo('Вы не ввели фамилию!');
                    }
                    if ($error->getMessage() === 'Length LName Error') {
                        echo('Длина фамилии должна быть от 3 до 30 символов!');
                    }
                    if ($error->getMessage() === 'Uncorrect LName Error') {
                        echo('Фамилия должна состоять из русских букв!');
                    }
                }
            }

            function ValidateFName($firstName)
            {
                try {
                    if ($firstName ?? '') {
                        $firstNameLength = mb_strlen($firstName);
                        if ($firstNameLength >= 4 && $firstNameLength <= 15) {
                            if (htmlspecialchars($firstName) == $firstName) {
                                if (trim($firstName) == $firstName ) {
                                    preg_match('/[А-ЯЁ]{1}[а-яё]{2,}/u', $firstName, $regFirstName);

                                    if ($regFirstName ?? '') {
                                        if ($regFirstName[0] == $firstName) {
                                            return true;
                                        } else {
                                            throw new Exception("Uncorrect FName Error", 1);
                                            
                                        }
                                        
                                    } else {
                                        throw new Exception("Uncorrect FName Error", 1);
                                    }
                                    
                                } else {
                                    throw new Exception("Uncorrect FName Error", 1);
                                }
                                
                            } else {
                                throw new Exception("Uncorrect FName Error", 1);
                            }
                            
                        } else {
                            throw new Exception("Length FName Error", 1);
                        }
                        
                    } else {
                        throw new Exception("Empty FName Error", 1);
                    }
                    
                } catch (Exception $error) {
                    if ($error->getMessage() === 'Empty FName Error') {
                        echo('Вы не ввели имя!');
                    }
                    if ($error->getMessage() === 'Length FName Error') {
                        echo('Длина имени должна быть от 4 до 15 символов!');
                    }
                    if ($error->getMessage() === 'Uncorrect FName Error') {
                        echo('Имя должно состоять из русских букв!');
                    }
                }
            }

            function ValidateMName($middleName)
            {
                try {
                    if ($middleName ?? '') {
                        $middleNameLength = mb_strlen($middleName);
                        if ($middleNameLength >= 6 && $middleNameLength <= 24) {
                            if (htmlspecialchars($middleName) == $middleName) {
                                if (trim($middleName) == $middleName ) {
                                    preg_match('/[А-ЯЁ]{1}[а-яё]{2,}/u', $middleName, $regMiddleName);

                                    if ($regMiddleName ?? '') {
                                        if ($regMiddleName[0] == $middleName) {
                                            return true;
                                        } else {
                                            throw new Exception("Uncorrect MName Error", 1);
                                            
                                        }
                                        
                                    } else {
                                        throw new Exception("Uncorrect MName Error", 1);
                                    }
                                    
                                } else {
                                    throw new Exception("Uncorrect MName Error", 1);
                                }
                                
                            } else {
                                throw new Exception("Uncorrect MName Error", 1);
                            }
                            
                        } else {
                            throw new Exception("Length MName Error", 1);
                        }
                        
                    } else {
                        throw new Exception("Empty MName Error", 1);
                    }
                    
                } catch (Exception $error) {
                    if ($error->getMessage() === 'Empty MName Error') {
                        echo('Вы не ввели отчество!');
                    }
                    if ($error->getMessage() === 'Length MName Error') {
                        echo('Длина отчества должна быть от 6 до 24 символов!');
                    }
                    if ($error->getMessage() === 'Uncorrect MName Error') {
                        echo('Отчество должно состоять из русских букв!');
                    }
                }
            }

            function ValidatePhone($phone)
            {
                try {
                    if ($phone ?? '') {
                        if (strlen($phone) == 15) {
                            if (trim($phone) == $phone) {
                                if (htmlspecialchars($phone) == $phone) {
                                    preg_match('/[(][9][0-9]{2}[)][-][0-9]{3}[-][0-9]{2}[-][0-9]{2}/', $phone, $regPhone);
                                    if ($regPhone ?? '') {
                                        if ($regPhone[0] == $phone) {
                                            return true;
                                        } else {
                                            throw new Exception('Uncorrect Phone Error', 1);
                                        }
                                        
                                    } else {
                                        throw new Exception('Uncorrect Phone Error', 1);
                                    }
                                    
                                } else {
                                    throw new Exception('Uncorrect Phone Error', 1);
                                }
                                
                            } else {
                                throw new Exception('Uncorrect Phone Error', 1);
                            }
                            
                        } else {
                            throw new Exception("Length Phone Error", 1);
                            
                        }
                        
                    } else {
                        throw new Exception('Empty Phone Error', 1);
                    }
                    
                } catch (Exception $error) {
                    if ($error->getMessage() == 'Empty Phone Error') {
                        echo('Вы не ввели номер телефона!');
                    }
                    if ($error->getMessage() == 'Length Phone Error' || $error->getMessage() == 'Uncorrect Phone Error') {
                        echo('Наш сервис работает только с телефоннами номерами РФ!');
                    }
                }
            }

            function ValidateBday($birthday)
            {
                try {
                    if ($birthday ?? '') {
                        if (strlen($birthday) == 10) {
                            if (trim($birthday) == $birthday) {
                                if (htmlspecialchars($birthday) == $birthday) {
                                    preg_match('/(([0-9]{2})[\.]([0-9]{2})[\.]([0-9]{4}))/', $birthday, $regBirthday);
                                    if ($regBirthday ?? '') {
                                        if ($regBirthday[0] == $birthday) {
                                            $day =  (int)$regBirthday[2];
                                            $month = (int)$regBirthday[3];
                                            $year = (int)$regBirthday[4];
                                            //проверка на день
                                            if (($day >= 1) && ($day <= 31)) {
                                                if (($month >= 1) && ($month <= 12)) {
                                                    if ($month == 2) {
                                                        if ($day <= 28) {
                                                                    
                                                        } else if ($day == 29 && $year % 4 == 0) {
                                                            
                                                        } else {
                                                            throw new Exception('Uncorrect Bday Error');
                                                        }
                                                    }
                                                    if (($year >= 1950) && ($year <= 2000)) {
                                                        return true;
                                                    } else {
                                                        throw new Exception('Uncorrect Bday Error');
                                                    }
                                                } else {
                                                    throw new Exception('Uncorrect Bday Error');
                                                }
                                            } else {
                                                throw new Exception('Uncorrect Bday Error');    
                                            }
                                        } else {
                                            throw new Exception('Uncorrect Bday Error', 1);
                                        }
                                        
                                    } else {
                                        throw new Exception('Uncorrect Bday Error', 1);
                                    }
                                    
                                } else {
                                    throw new Exception('Uncorrect Bday Error', 1);
                                }
                                
                            } else {
                                throw new Exception('Uncorrect Bday Error', 1);
                            }
                            
                        } else {
                            throw new Exception('Length BdayError', 1);
                            }
                        
                    } else {
                        throw new Exception("Empty Bday Error", 1);
                    }
                    
                } catch (Exception $error) {
                    if ($error->getMessage() == 'Empty Bday Error') {
                        echo('Вы не ввели дату рождения!');
                    }
                    if ($error->getMessage() == 'Length Bday Error') {
                        echo('Неверный формат даты рождения!');
                    }
                    if ($error->getMessage() == 'Uncorrect Bday Error') {
                        echo('Неверный формат даты рождения!');
                    }
                }
            }
            if (ValidateLName($emp->lastName) && ValidateFName($emp->firstName) && ValidateMName($emp->middleName) && ValidateBday($emp->birthday) && ValidatePhone($emp->phone)) {
                return true;
            }
        }
    

    static function Show()
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $selectEmployeesQuery = $db->prepare("SELECT * FROM vemps");
        $selectEmployeesQuery->execute();
        $emps = $selectEmployeesQuery->fetchAll(PDO::FETCH_OBJ);
        $empsLength = count($emps);
        if ($empsLength != 0) {
            return $emps;
        } else {            
            return false;
        }
    }

    protected function CheckDublicates($db, $emp, $pointer)
    {
        if ($pointer === 'create') {
            $getEmployeeQuery = $db->prepare("CALL spCheckDublicatesEmp (?, ?, ?, ?)");
            $getEmployeeQuery->execute(array($emp->lastName, $emp->firstName, $emp->middleName, $emp->birthday));
            $currentEmployee = $getEmployeeQuery->fetchAll(PDO::FETCH_OBJ);
        
            if (count($currentEmployee) == 0) {                
                return true;
            } else {
                echo ('Такой пользователь уже существует');
                return false;
            }            
        }
        elseif ($pointer === 'update') {
            $getEmployeeQuery = $db->prepare("CALL spCheckDublicatesEmp (?, ?, ?, ?)");
            $getEmployeeQuery->execute(array($emp->lastName, $emp->firstName, $emp->middleName, $emp->birthday));
            $currentEmployee = $getEmployeeQuery->fetchAll(PDO::FETCH_OBJ);
            if (count($currentEmployee) == 0 || count($currentEmployee) == 1) {
                return true;
            } else {
                echo ('Такой пользователь уже существует');
                return false;
            }            
        }
    }

    public function AddTaxes($taxes)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $taxesLen = count($taxes->taxes);
        for ($i=0; $i < $taxesLen; $i++) { 
            $insertEmpTaxesQuery = $db->prepare('CALL spCreateEmpTaxes (?,?,?)');
            $insertEmpTaxesQuery->execute(array($taxes->emp_id, $taxes->taxes[$i], uniqid()));
        }
    }

    public function RemoveTax($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $deleteEmpTaxQuery = $db->prepare('CALL spDeleteEmpTax (?)');
        $deleteEmpTaxQuery->execute(array($id));
    }

    public function GetTaxes($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $getTaxesQuery = $db->prepare('SELECT * FROM vemptaxes WHERE Emp_id = ?');
        $getTaxesQuery->execute(array($id));
        $taxes = $getTaxesQuery->fetchAll(PDO::FETCH_OBJ);
        if ($taxes) {
            return $taxes;
        } else {
            return false;
        }
        
    }
    
    //нужна ли отдельная функция удаления штрафов при изменении??? Как её контролировать?
    protected function CheckRoute($route)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $checkRouteQuery = $db->prepare('SELECT * FROM routes WHERE Emp_id = ? OR Transport_id = ?');
        $checkRouteQuery->execute(array($route->emp, $route->transport));
        $result = $checkRouteQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($result) == 0) {
            return true;
        } else {
            echo('Данные пользователь и транспорт находятся на маршруте!');
        }
    }

    public function AddRoute($route)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        if (Employee::CheckRoute($route)) {
            $createRouteQuery = $db->prepare('CALL spCreateRoute (?,?)');
            $createRouteQuery->execute(array($route->emp, $route->transport));
        }
    }
    public function RemoveRoute($transport)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $deleteRouteQuery = $db->prepare('CALL spDeleteRoute(?)');
        $deleteRouteQuery->execute(array($transport));
    }

    public function GetRoute($emp)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $getRouteQuery = $db->prepare('SELECT * FROM vroutes WHERE Emp_id = ?');
        $getRouteQuery = $db->prepare('CALL spGetRoute(?)');
        $getRouteQuery->execute(array($emp));
        $route = $getRouteQuery->fetchAll(PDO::FETCH_OBJ);
        if ($route) {
            return $route;
        } else {
            return false;
        }
        
    }

    static function ShowRoutes() {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $updateTransport = $db->prepare('DROP TABLE IF EXISTS transport; CREATE TABLE transport AS SELECT * FROM buses UNION SELECT * FROM gazelles UNION SELECT * FROM trams UNION SELECT * FROM trolleies;');
        $updateTransport->execute();
        $updateTransport->closeCursor();
        $selectRoutesQuery = $db->prepare('SELECT * FROM transport INNER JOIN vroutes ON vroutes.Transport = Transport.id');
        $selectRoutesQuery->execute();
        $routes = $selectRoutesQuery->fetchAll(PDO::FETCH_OBJ);
        if ($routes) {
            return $routes;
        } else {
            return false;
        }
    }
}
    

interface IEmployee {
    function Create($emp);

    function Update($emp);

    function GetTaxes($id);

    function AddTaxes($tax);

    function RemoveTax($id);

    function AddRoute($route); //назначение на маршрут

    function RemoveRoute($emp); //снятие с маршрута

    function GetRoute($emp);
}
    
?>