<?php
class Employee implements IEmployee{
    protected $id;
    protected $lastName;
    protected $firstName;
    protected $middleName;
    protected $phoneNumber;
    protected $birthday;

    public function Create($emp)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($db, $emp, 'create')) {
            $createEmployeeQuery = $db->prepare("CALL spCreateEmp (?, ?, ?, ?, ?, ?, ?)");
            $createEmployeeQuery->execute(array($emp->id, $emp->lastName, $emp->firstName, $emp->middleName, $emp->birthday, $emp->phoneNumber));
        }
        if (substr($_SERVER['HTTP_REFERER'], -9, 9) === 'index.php') {
            setcookie("Account[{$emp->id}]", $emp->password, time() + 3600, '/');
        }
    }

    public function Delete($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $deleteEmployeeQuery = $db->prepare("CALL spDeleteEmp(?)");
        $deleteEmployeeQuery->execute(array($id));        
    }

    public function Get($id)
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

    public function Find($lastName)
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
            $updateEmployeeQuery = $db->prepare("CALL spUpdateEmp(?, ?, ?, ?, ?)");
            $updateEmployeeQuery->execute(array($emp->login, $emp->lastName, $emp->firstName, $emp->middleName, $emp->id));            
        }
    }

    public function Set($emp)
    {
        $this->id = uniqid();
        $this->lastName = $emp->lastName;
        $this->firstName = $emp->firstName;
        $this->middleName = $emp->middleName;
        $this->phoneNumber = $emp->phoneNumber;
        $this->birthday = $emp->birthday;
        return $this;
    }

    public function Validate($emp)
    {
        try {
            #проверка на обязательность поля
            if (strlen($emp->login) != 0 && strlen($emp->pass) != 0 && strlen($emp->lastName) != 0 && 
            strlen($emp->firstName) != 0 && strlen($emp->middleName) !=0 && strlen($emp->phoneNumber) != 0) {                
                $loginLength = strlen($emp->login);
                $passLength = strlen($emp->pass);
                $firstNameLength = mb_strlen($emp->firstName);
                $middleNameLength = mb_strlen($emp->middleName);
                $lastNameLength = mb_strlen($emp->lastName);
                $phoneNumberLength = strlen($emp->phoneNumber);
                #проверка на длину поля
                if ($loginLength >= 6 && $loginLength <= 24 &&
                    $passLength >= 6 && $passLength <= 24 &&
                    $firstNameLength >= 4 && $firstNameLength <= 15 &&
                    $lastNameLength >= 3 && $lastNameLength <= 30 &&
                    $middleNameLength >=6 && $middleNameLength <= 24 &&
                    $phoneNumberLength == 15) {
                        #проверка на наличие XSS-атаки в поле
                        if (htmlspecialchars($emp->login) == $emp->login &&
                            htmlspecialchars($emp->pass) == $emp->pass &&
                            htmlspecialchars($emp->lastName) == $emp->lastName &&
                            htmlspecialchars($emp->firstName) == $emp->firstName &&
                            htmlspecialchars($emp->middleName) == $emp->middleName &&
                            htmlspecialchars($emp->phoneNumber) == $emp->phoneNumber) {
                                #проверка на наличие пробелов в поле
                                if (trim($emp->login) == $emp->login &&
                                    trim($emp->pass) == $emp->pass &&
                                    trim($emp->lastName) == $emp->lastName &&
                                    trim($emp->firstName) == $emp->firstName &&
                                    trim($emp->middleName) == $emp->middleName &&
                                    trim($emp->phoneNumber) == $emp->phoneNumber) {                                        
                                        #проверка на соответствие регуляркам                                        
                                        preg_match('/[A-Za-z]{1,}[a-zA-Z0-9_.]{5,}/', $emp->login, $regLogin);
                                        preg_match('/[A-Za-z]{1,}[a-zA-Z0-9_.]{5,}/', $emp->pass, $regPass);
                                        preg_match('/[A-ZА-ЯЁ]{1}[a-zа-яё]{2,}/u', $emp->lastName, $regLastName);
                                        preg_match('/[A-ZА-ЯЁ]{1}[a-zа-яё]{3,}/u', $emp->firstName, $regFirstName);
                                        preg_match('/[A-ZА-ЯЁ]{1}[a-zа-яё]{5,}/u', $emp->middleName, $regMiddleName);
                                        preg_match('/[(][9][0-9]{2}[)][-][0-9]{3}[-][0-9]{2}[-][0-9]{2}/', $emp->phoneNumber, $regPhoneNumber);
                                        
                                        if (($regLogin ?? '') && ($regPass ?? '') && ($regLastName ?? '') && 
                                            ($regFirstName ?? '') && ($regMiddleName ?? '') && ($regPhoneNumber ?? '')) {
                                                if ($regLogin[0] == $emp->login &&
                                                $regPass[0] == $emp->pass &&                                            
                                                $regLastName[0] == $emp->lastName &&
                                                $regFirstName[0] == $emp->firstName &&
                                                $regMiddleName[0] == $emp->middleName &&
                                                $regPhoneNumber[0] == $emp->phoneNumber) {
                                                    return true;
                                                } else {
                                                    throw new Exception('Wrong Data Error', 1);
                                                }    
                                        } else {
                                            throw new Exception('Wrong Data Error', 1);
                                        }    
                                                                    
                                } else {
                                    throw new Exception('Wrong Data Error', 1);    
                                }                        
                        } else {
                            throw new Exception('Wrong Data Error', 1);                        
                        }                    
                } else {
                    throw new Exception('Length Data Error', 1);
                }
            } else {
                throw new Exception('Empty Data Error', 1);
            }
        }
        catch (Exception $error) {            
            if ($error->getMessage() === 'Empty Data Error') {                
                if (strlen($emp->login) == 0) {                    
                    echo("Вы не ввели пароль! \n");                                        
                } 
                if (strlen($emp->pass) == 0) {
                    echo("Вы не ввели пароль! \n");                    
                } 
                if (mb_strlen($emp->lastName) == 0) {
                    echo("Вы не ввели фамилию! \n");                    
                }
                if (mb_strlen($emp->firstName) == 0) {
                    echo("Вы не ввели имя! \n");
                }
                if (mb_strlen($emp->middleName) == 0) {
                    echo("Вы не ввели отчество!\n");
                }
                if(strlen($emp->phoneNumber) == 0) {
                    echo('Вы не ввели номер телефона!');
                }
                return false;
            }
            if ($error->getMessage() === 'Length Data Error') {
                if (strlen($emp->login) < 6 || strlen($emp->login) > 24) {
                    echo("Логин должен быть от 6 до 24 символов! \n");                    
                } 
                if (strlen($emp->pass) < 6 || strlen($emp->pass) > 24) {
                    echo("Пароль должен быть от 6 до 24 символов! \n");                    
                } 
                if (strlen($emp->lastName) < 3 || strlen($emp->lastName) > 30) {
                    echo("Фамилия должна быть от 3 до 30 символов! \n");                    
                }
                if (strlen($emp->firstName) < 4 || strlen($emp->firstName) > 15 ) {
                    echo("Имя должно быть от 4 до 15 символов! \n");                    
                }
                if (strlen($emp->middleName) < 6 || strlen($emp->middleName) > 24) {
                    echo("Отчество должно быть от 6 до 24 символов! \n");                    
                }
                if ($phoneNumberLength != 15) {
                    echo('Наш сервис работает только с телефоннами номерами РФ!');
                }
                return false;
            }
            if ($error->getMessage() === 'Wrong Data Error') {
                if (htmlspecialchars($emp->login) != $emp->login && trim($emp->login) != $emp->login || !($regLogin ?? '')) {
                    if ($regLogin[0] != $emp->login) {
                        echo("Логин должен состоять из латинских букв, цифр, точки и знака подчёркивания! \n");
                    } else {
                        echo("Логин должен состоять из латинских букв, цифр, точки и знака подчёркивания! \n");
                    }                                   
                }
                if ($regLogin ?? '') {
                    if ($regLogin[0] != $emp->login) {
                        echo("Логин должен состоять из латинских букв, цифр, точки и знака подчёркивания! \n");
                    } 
                }
                if (htmlspecialchars($emp->pass) != $emp->pass && trim($emp->pass) != $emp->pass || !($regPass ?? '')) {
                    if ($regPass[0] != $emp->pass) {
                        echo("Пароль должен состоять из латинских букв, цифр, точки и знака подчёркивания! \n");
                    } else {
                        echo("Пароль должен состоять из латинских букв, цифр, точки и знака подчёркивания! \n");
                    }                    
                }
                if ($regPass ?? '') {
                    if ($regPass[0] != $emp->pass) {
                        echo("Пароль должен состоять из латинских букв, цифр, точки и знака подчёркивания! \n");
                    } 
                }
                if (htmlspecialchars($emp->lastName) != $emp->lastName && trim($emp->lastName) != $emp->lastName || !($regLastName ?? '')) {
                    if (($regLastName[0] ?? '') && $regLastName[0] == $emp->lastName) {
                        echo("Фамилия должна начинаться с заглавной буквы и состоять из латинских или кириллистических букв! \n");
                    } else {
                        echo("Фамилия должна начинаться с заглавной буквы и состоять из латинских или кириллистических букв! \n");           
                    }
                }
                if ($regLastName ?? '') {
                    if ($regLastName[0] != $emp->lastName) {
                        echo("Фамилия должна начинаться с заглавной буквы и состоять из латинских или кириллистических букв! \n");
                    } 
                }
                if (htmlspecialchars($emp->firstName) != $emp->firstName && trim($emp->firstName) != $emp->firstName || !($regFirstName ?? '')) {
                    if (($regFirstName[0] ?? '') && $regFirstName[0] == $emp->firstName) {
                        echo("Имя должно начинаться с заглавной буквы и состоять из латинских или кириллистических букв \n");
                    } else {
                        echo("Имя должно начинаться с заглавной буквы и состоять из латинских или кириллистических букв \n");
                    }                    
                }
                if ($regFirstName ?? '') {
                    if ($regFirstName[0] != $emp->firstName) {
                        echo("Имя должно начинаться с заглавной буквы и состоять из латинских или кириллистических букв \n");
                    } 
                }
                if (htmlspecialchars($emp->middleName) != $emp->middleName && trim($emp->middleName) != $emp->middleName || !($regMiddleName ?? '')) {
                    if (($regMiddleName[0] ?? '') && $regMiddleName[0] == $emp->middleName) {
                        echo("Отчество должно начинаться с заглавной буквы и состоять из латинских или кириллистических букв \n");
                    } else {
                        echo("Отчество должно начинаться с заглавной буквы и состоять из латинских или кириллистических букв \n");
                    }                                        
                }
                if ($regMiddleName ?? '') {
                    if ($regMiddleName[0] != $emp->middleName) {
                        echo("Отчество должно начинаться с заглавной буквы и состоять из латинских или кириллистических букв \n");
                    } 
                }

                if (htmlspecialchars($emp->phoneNumber) != $emp->phoneNumber && trim($emp->phoneNumber) != $emp->phoneNumber || !($regPhoneNumber ?? '')) {
                    if (($regPhoneNumber[0] ?? '') && $regPhoneNumber[0] == $emp->phoneNumber) {
                        echo('Наш сервис работает только с телефоннами номерами РФ!');
                    } else {
                        echo('Наш сервис работает только с телефоннами номерами РФ!');
                    }                                        
                }
                if ($regPhoneNumber ?? '') {
                    if ($regPhoneNumber[0] != $emp->phoneNumber) {
                        echo('Наш сервис работает только с телефоннами номерами РФ!');
                    } 
                }
                return false;
            }
        }
    }

    public function Show()
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
            $getEmployeeQuery = $db->prepare("SELECT * from vemps WHERE Birthday = ?");
            $getEmployeeQuery->execute(array($emp->birthday));
            $currentEmployee = $getEmployeeQuery->fetchAll(PDO::FETCH_OBJ);
            if (count($currentEmployee) == 0) {                
                return true;
            } else {
                echo ('Такой пользователь уже существует');
                return false;
            }            
        }
        elseif ($pointer === 'update') {
            $getEmployeeQuery = $db->prepare("SELECT * from vemps WHERE Birthday = ?");
            $getEmployeeQuery->execute(array($emp->birthday));
            $currentEmployee = $getEmployeeQuery->fetchAll(PDO::FETCH_OBJ);
            if (count($currentEmployee) == 0 || count($currentEmployee) == 1) {
                return true;
            } else {
                echo ('Такой пользователь уже существует');
                return false;
            }            
        }
    }
}
    

interface IEmployee {
    function Create($emp);

    function Delete($id);
    
    function Get($id);

    function Find($lastName);
  
    function Update($emp);

    function Set($emp);

    function Validate($emp);

    function Show();
}
    
?>