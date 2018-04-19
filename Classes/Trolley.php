<?php
class Trolley implements ITrolley{
    protected $id;
    protected $number;
    protected $route;
    protected $statement;
    protected $photo;

    public function __construct($trolley, $photo = null) {
        if ($trolley->id ?? '') {
            $this->id = $trolley->id;
        } else {
            $this->id = uniqid();
        }
        $this->number = $trolley->number;
        if ($photo ?? '') {
            $this->photo = base64_encode(file_get_contents($photo['tmp_name']));
        } else {
            $this->photo = $photo;
        }
        $this->route = $trolley->route;
        $this->statement = $trolley->statement;
    }

    public function Create($trolley)
    {   
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($trolley, $db, 'create')) {
            $createTrolleyQuery = $db->prepare('CALL spCreateTrolley (?, ?, ?, ?, ?)');
            $createTrolleyQuery->execute(array($trolley->id, $trolley->number, $trolley->route, $trolley->statement, $trolley->photo));
        }
    }

    public function Update($trolley)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($trolley, $db, 'UPDATE')) {
            if ($trolley->photo ?? '') {
                $updateTrolleyQuery = $db->prepare('CALL spUpdateTrolleyWithPhoto (?, ?, ?, ?, ?)');
                $updateTrolleyQuery->execute(array($trolley->number, $trolley->route, $trolley->statement, $trolley->photo, $trolley->id));
            } else {
                $updateTrolleyQuery = $db->prepare('CALL spUpdateTrolley (?, ?, ?, ?)');
                $updateTrolleyQuery->execute(array($trolley->number, $trolley->route, $trolley->statement, $trolley->id));
            }
            
            
        }
    }

    public function Delete($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $deleteTrolleyQuery = $db->prepare('CALL spDeleteTrolley (?)');
        $deleteTrolleyQuery->execute(array($id));
    }

    public function Get($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $getTrolleyQuery = $db->prepare('SELECT * FROM vtrolleies WHERE id = ?');
        $getTrolleyQuery->execute(array($id));
        $trolley = $getTrolleyQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($trolley) == 1) {
            return $trolley;
        } 
    }

    public function Find($number)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $findTrolleysQuery = $db->prepare('CALL spGetTrolleyNumber (?)');
        $findTrolleysQuery->execute(array($number));
        $findTrolleys = $findTrolleysQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($findTrolleys) != 0) {
            return $findTrolleys;
        } else {
            return false;
        }
    }

    function GetWorking()
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $selectBussQuery = $db->prepare('SELECT * FROM vtrolleies WHERE Statement <> "В ремонте"');
        $selectBussQuery->execute();
        $busess = $selectBussQuery->fetchAll(PDO::FETCH_OBJ);
        if ($busess) {
            return $busess;
        } else {
            return false;
        }
    }

    protected function CheckDublicates($trolley, $db, $switch)
    {
        if ($switch === "create") {
            $dubclicateQuery = $db->prepare('CALL spGetTrolleyNumber (?)');
            $dubclicateQuery->execute(array($trolley->number));
            $currentTrolley = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (!$currentTrolley) {
                return true;
            } else {
                echo('Такой троллейбус уже существует!');
            }
            
        } else if ($switch === "UPDATE"){
            $dubclicateQuery = $db->prepare('CALL spGetTrolleyNumber (?)');
            $dubclicateQuery->execute(array($trolley->number));
            $currentTrolley = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (count($currentTrolley) == 0 || count($currentTrolley) == 1) {
                return true;
            } else {
                echo('Такой троллейбус уже существует!');
            }
        }
        
    }

    static function Show()
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $selectTrolleysQuery = $db->prepare('SELECT * FROM vtrolleies');
        $selectTrolleysQuery->execute();
        $trolleys = $selectTrolleysQuery->fetchAll(PDO::FETCH_OBJ);
        if ($trolleys) {
            return $trolleys;
        } else {
            return false;
        }
    }

    static function Validate($tram, $photo)
    {
        function ValidateNumber($number) {
            try {
                if ($number ?? '') {
                    if (mb_strlen($number) == 4) {
                        preg_match('/([0-9]{4})/', $number, $regNumber);
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
                    echo("Вы не ввели номер троллейбуса!");
                }
                
                if ($error->getMessage() === 'Length Number Error') {
                    echo("Номер троллейбуса должен быть длиной 4 символа!");
                }
    
                if ($error->getMessage() === 'Uncorrect Number Error') {
                    echo("Неверный формат номера троллейбуса!");
                }
            }
        }
    
        function ValidatePhoto($photo)
        {
            try {
                if (substr($_SERVER['HTTP_REFERER'], -37, 11) === 'trolleyinfo') {
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
                    echo('Вы не указали состояние троллейбуса!');
                }
    
                if ($error->getMessage() === 'Uncorrect Statement Error') {
                    echo('Вы указали некорректное состояние троллейбуса!');
                }
    
                
            }
        }
        if (ValidateNumber($tram->number) && ValidatePhoto($photo) && ValidateRoute($tram->route)
            && ValidateStatement($tram->statement)) {
            return true;
        }
    }
}

interface ITrolley {
    function Create($trolley);
    function Update($trolley);
}

?>