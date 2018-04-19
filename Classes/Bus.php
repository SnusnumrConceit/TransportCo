<?php
class Bus implements IBus{
    protected $id;
    protected $number;
    protected $route;
    protected $statement;
    protected $photo;

    public function __construct($bus, $photo = null) {
        if ($bus->id ?? '') {
            $this->id = $bus->id;
        } else {
            $this->id = uniqid();
        }
        $this->number = $bus->number;
        if ($photo ?? '') {
            $this->photo = base64_encode(file_get_contents($photo['tmp_name']));
        } else {
            $this->photo = $photo;
        }
        $this->route = $bus->route;
        $this->statement = $bus->statement;
    }

    public function Create($bus)
    {   
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($bus, $db, 'create')) {
            $createBusQuery = $db->prepare('CALL spCreateBus (?, ?, ?, ?, ?)');
            $createBusQuery->execute(array($bus->id, $bus->number, $bus->route, $bus->statement, $bus->photo));
        }
    }

    public function Update($bus)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($bus, $db, 'UPDATE')) {
            if ($bus->photo ?? '') {
                $updateBusQuery = $db->prepare('CALL spUpdateBusWithPhoto (?, ?, ?, ?, ?)');
                $updateBusQuery->execute(array($bus->number, $bus->route, $bus->statement, $bus->photo, $bus->id));
            } else {
                $updateBusQuery = $db->prepare('CALL spUpdateBus (?, ?, ?, ?)');
                $updateBusQuery->execute(array($bus->number, $bus->route, $bus->statement, $bus->id));
            }
            
            
        }
    }

    static public function Delete($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $deleteBusQuery = $db->prepare('CALL spDeleteBus (?)');
        $deleteBusQuery->execute(array($id));
    }

    static function Get($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $getBusQuery = $db->prepare('SELECT * FROM vbuses WHERE id = ?');
        $getBusQuery->execute(array($id));
        $bus = $getBusQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($bus) == 1) {
            return $bus;
        } 
    }

    static function Find($number)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $findBussQuery = $db->prepare('CALL spGetBusNumber (?)');
        $findBussQuery->execute(array($number));
        $findBuss = $findBussQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($findBuss) != 0) {
            return $findBuss;
        } else {
            return false;
        }
    }

    protected function CheckDublicates($bus, $db, $switch)
    {
        if ($switch === "create") {
            $dubclicateQuery = $db->prepare('CALL spGetBusNumber (?)');
            $dubclicateQuery->execute(array($bus->number));
            $currentBus = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (!$currentBus) {
                return true;
            } else {
                echo('Такой автобус уже существует!');
            }
            
        } else if ($switch === "UPDATE"){
            $dubclicateQuery = $db->prepare('CALL spGetBusNumber (?)');
            $dubclicateQuery->execute(array($bus->number));
            $currentBus = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (count($currentBus) == 0 || count($currentBus) == 1) {
                return true;
            } else {
                echo('Такой автобус уже существует!');
            }
        }
        
    }

    static function Show()
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $selectBussQuery = $db->prepare('SELECT * FROM vbuses');
        $selectBussQuery->execute();
        $buss = $selectBussQuery->fetchAll(PDO::FETCH_OBJ);
        if ($buss) {
            return $buss;
        } else {
            return false;
        }
    }

    static function GetWorking()
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $selectBussQuery = $db->prepare('SELECT * FROM vbuses WHERE Statement <> "В ремонте"');
        $selectBussQuery->execute();
        $buss = $selectBussQuery->fetchAll(PDO::FETCH_OBJ);
        if ($buss) {
            return $buss;
        } else {
            return false;
        }
    }

    public function Validate($tram, $photo)
    {
        function ValidateNumber($number) {
            try {
                if ($number ?? '') {
                    if (mb_strlen($number) == 11) {
                        preg_match('/([А-Я]{2}[0-9]{4}[6][4][R][U][S])/u', $number, $regNumber);
                        if ($regNumber ?? '') {
                            if ($regNumber[0] === $number) {
                                return true;
                            } else {
                                throw new Exception('Uncorrect Number Error', 1);    
                            }
                        } else {
                            throw new Exception('Uncorrect Number Error', 1);
                        }                    
                    } else {
                        throw new Exception('Length Number Error', 1);
                    }       
                } else {
                    throw new Exception("Empty Number Error", 1);
                    
                }
                
            } catch (Exception $error){
                if ($error->getMessage() === 'Empty Number Error') {
                    echo("Вы не ввели номер автобуса!");
                }
                
                if ($error->getMessage() === 'Length Number Error') {
                    echo("Номер автобуса должен быть длиной 11 символов!");
                }
    
                if ($error->getMessage() === 'Uncorrect Number Error') {
                    echo("Неверный формат номера автобуса!");
                }
            }
        }
    
        function ValidatePhoto($photo)
        {
            try {
                if (substr($_SERVER['HTTP_REFERER'], -29, 7) === 'businfo') {
                    if (!($photo ?? '')) {
                        return true;
                    }
                }
                if (is_uploaded_file($photo['tmp_name'])) {
                    if ($photo['size'] <= 2*1024*1024) {
                        $ext = substr($photo['name'], -3, 3);
                        $arrExt = ['jpg', 'png', 'JPG', 'PNG'];
                        if (in_array($ext, $arrExt)) {
                            return true;
                        } else {
                            throw new Exception("Extension Photo Error", 1);
                        }
                    } else {
                        throw new Exception("Size Photo Error", 1);
                    }
                } else {
                    throw new Exception("Download Photo Error", 1);
                }
                
            } catch (Exception $error) {
                if ($error->getMessage() === 'Download Photo Error') {
                    echo('Вы не загрузили фотографию!');
                }
                
                if ($error->getMessage() === 'Download Photo Error') {
                    echo('Размер фотографии не должен превышать более 2 Мбайт!');
                }
    
                if ($error->getMessage() === 'Download Photo Error') {
                    echo('Фотография должна быть с расширением jpg или png!');
                }
            }
        }
    
        function ValidateRoute($route)
        {
            try {
                if ($route ?? '') {
                    if (is_numeric($route)) {
                        if ($route > 0 && $route <= 300) {
                            return true;
                        } else {
                            throw new Exception("Length Route Error", 1);
                        }
                    } else {
                        throw new Exception("Uncorrect Route Error", 1);
                    }
                } else {
                    throw new Exception("Empty Route Error", 1);
                    
                }
                
            } catch (Exception $error) {
                if ($error->getMessage() == 'Empty Route Error') {
                    echo('Вы не ввели номер маршрута!');
                }
    
                if ($error->getMessage() == 'Uncorrect Route Error') {
                    echo('Номер маршрута должен состоять из цифр!');
                }
    
                if ($error->getMessage() == 'Length Route Error') {
                    echo('Номер маршрута не должен превышать 300!');
                }
            }
        }
    
        function ValidateStatement($statement)
        {
            try {
                if ($statement ?? '') {
                    $arrStatements = ['В ремонте', 'Рабочее'];
                    if (in_array($statement, $arrStatements)) {
                        return true;
                    } else {
                        throw new Exception("Uncorrect Statement Error", 1);
                    }
                    
                } else {
                    throw new Exception("Empty Statement Error", 1);
                }
                
            } catch (Exception $error) {
                if ($error->getMessage() == 'Empty Statement Error') {
                    echo('Вы не указали состояние автобуса!');
                }
    
                if ($error->getMessage() === 'Uncorrect Statement Error') {
                    echo('Вы указали некорректное состояние автобуса!');
                }
    
                
            }
        }
        if (ValidateNumber($tram->number) && ValidatePhoto($photo) && ValidateRoute($tram->route)
            && ValidateStatement($tram->statement)) {
            return true;
        }
    }
}

interface IBus {
    function Create($bus);
    function Update($bus);
}

?>