<?php
class Tram implements ITram{
    protected $id;
    protected $number;
    protected $route;
    protected $statement;
    protected $photo;

    public function __construct($tram, $photo = null) {
        if ($tram->id ?? '') {
            $this->id = $tram->id;
        } else {
            $this->id = uniqid();
        }
        $this->number = $tram->number;
        if ($photo ?? '') {
            $this->photo = base64_encode(file_get_contents($photo['tmp_name']));
        } else {
            $this->photo = $photo;
        }
        $this->route = $tram->route;
        $this->statement = $tram->statement;        
    }

    public function Create($tram)
    {   
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($tram, $db, 'create')) {
            $createTramQuery = $db->prepare('CALL spCreateTram (?, ?, ?, ?, ?)');
            $createTramQuery->execute(array($tram->id, $tram->number, $tram->route, $tram->statement, $tram->photo));
        }
    }

    public function Update($tram)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($tram, $db, 'UPDATE')) {
            if ($tram->photo ?? '') {
                $updateTramQuery = $db->prepare('CALL spUpdateTramWithPhoto (?, ?, ?, ?, ?)');
                $updateTramQuery->execute(array($tram->number, $tram->route, $tram->statement, $tram->photo, $tram->id));
            } else {
                $updateTramQuery = $db->prepare('CALL spUpdateTram (?, ?, ?, ?)');
                $updateTramQuery->execute(array($tram->number, $tram->route, $tram->statement, $tram->id));
            }
            
            
        }
    }

    static function Delete($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $deleteTramQuery = $db->prepare('CALL spDeleteTram (?)');
        $deleteTramQuery->execute(array($id));
    }

    public function Get($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $getTramQuery = $db->prepare('SELECT * FROM vtrams WHERE id = ?');
        $getTramQuery->execute(array($id));
        $tram = $getTramQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($tram) == 1) {
            return $tram;
        } 
    }

    static function Find($number)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $findTramsQuery = $db->prepare('CALL spGetTramNumber (?)');
        $findTramsQuery->execute(array($number));
        $findTrams = $findTramsQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($findTrams) != 0) {
            return $findTrams;
        } else {
            return false;
        }
    }

    static function GetWorking()
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $selectBussQuery = $db->prepare('SELECT * FROM vtrams WHERE Statement <> "В ремонте"');
        $selectBussQuery->execute();
        $busess = $selectBussQuery->fetchAll(PDO::FETCH_OBJ);
        if ($busess) {
            return $busess;
        } else {
            return false;
        }
    }

    protected function CheckDublicates($tram, $db, $switch)
    {
        if ($switch === "create") {
            $dubclicateQuery = $db->prepare('CALL spGetTramNumber (?)');
            $dubclicateQuery->execute(array($tram->number));
            $currentTram = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (!$currentTram) {
                return true;
            } else {
                echo('Такой трамвай уже существует!');
            }
            
        } else if ($switch === "UPDATE"){
            $dubclicateQuery = $db->prepare('CALL spGetTramNumber (?)');
            $dubclicateQuery->execute(array($tram->number));
            $currentTram = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (count($currentTram) == 0 || count($currentTram) == 1) {
                return true;
            } else {
                echo('Такой трамвай уже существует!');
            }
        }
        
    }

    static function Show()
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $selectTramsQuery = $db->prepare('SELECT * FROM vtrams');
        $selectTramsQuery->execute();
        $trams = $selectTramsQuery->fetchAll(PDO::FETCH_OBJ);
        if ($trams) {
            return $trams;
        } else {
            return false;
        }
    }

    public function Validate($tram, $photo)
    {
        function ValidateNumber($number) {
            try {
                if ($number ?? '') {
                    if (is_numeric($number)) {
                        if (strlen($number) == 4) {
                            return true;
                        } else {
                            throw new Exception('Length Number Error', 1);
                        }                    
                    } else {
                        throw new Exception('Uncorrect Number Error', 1);
                    }       
                } else {
                    throw new Exception("Empty Number Error", 1);
                    
                }
                
            } catch (Exception $error){
                if ($error->getMessage() === 'Empty Number Error') {
                    echo("Вы не ввели номер трамвая!");
                }
                
                if ($error->getMessage() === 'Length Number Error') {
                    echo("Номер трамвая должен быть длиной 4 символа!");
                }
    
                if ($error->getMessage() === 'Uncorrect Number Error') {
                    echo("Номер трамвая должен состоять из цифр!");
                }
            }
        }
    
        function ValidatePhoto($photo)
        {
            try {
                if (substr($_SERVER['HTTP_REFERER'], -31, 8) === 'traminfo') {
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
                    echo('Вы не указали состояние трамвая!');
                }
    
                if ($error->getMessage() === 'Uncorrect Statement Error') {
                    echo('Вы указали некорректное состояние трамвая!');
                }
    
                
            }
        }
        if (ValidateNumber($tram->number) && ValidatePhoto($photo) && ValidateRoute($tram->route)
            && ValidateStatement($tram->statement)) {
            return true;
        }
    }    
}

interface ITram {
    function Create($tram);
    function Update($tram);
    function Validate($tram, $photo);
}

?>