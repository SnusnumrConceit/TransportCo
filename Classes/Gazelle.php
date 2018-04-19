<?php
class Gazelle implements IGazelle{
    protected $id;
    protected $number;
    protected $route;
    protected $statement;
    protected $photo;

    public function __construct($gazelle, $photo = null) {
        if ($gazelle->id ?? '') {
            $this->id = $gazelle->id;
        } else {
            $this->id = uniqid();
        }
        $this->number = $gazelle->number;
        if ($photo ?? '') {
            $this->photo = base64_encode(file_get_contents($photo['tmp_name']));
        } else {
            $this->photo = $photo;
        }
        $this->route = $gazelle->route;
        $this->statement = $gazelle->statement;
    }

    public function Create($gazelle)
    {   
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($gazelle, $db, 'create')) {
            $createGazelleQuery = $db->prepare('CALL spCreateGazelle (?, ?, ?, ?, ?)');
            $createGazelleQuery->execute(array($gazelle->id, $gazelle->number, $gazelle->route, $gazelle->statement, $gazelle->photo));
        }
    }

    public function Update($gazelle)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($gazelle, $db, 'UPDATE')) {
            if ($gazelle->photo ?? '') {
                $updateGazelleQuery = $db->prepare('CALL spUpdateGazelleWithPhoto (?, ?, ?, ?, ?)');
                $updateGazelleQuery->execute(array($gazelle->number, $gazelle->route, $gazelle->statement, $gazelle->photo, $gazelle->id));
            } else {
                $updateGazelleQuery = $db->prepare('CALL spUpdateGazelle (?, ?, ?, ?)');
                $updateGazelleQuery->execute(array($gazelle->number, $gazelle->route, $gazelle->statement, $gazelle->id));
            }
            
            
        }
    }

    static function Delete($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $deleteGazelleQuery = $db->prepare('CALL spDeleteGazelle (?)');
        $deleteGazelleQuery->execute(array($id));
    }

    static function Get($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $getGazelleQuery = $db->prepare('SELECT * FROM vgazelles WHERE id = ?');
        $getGazelleQuery->execute(array($id));
        $gazelle = $getGazelleQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($gazelle) == 1) {
            return $gazelle;
        } 
    }

    static function Find($number)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $findGazellesQuery = $db->prepare('CALL spGetGazelleNumber (?)');
        $findGazellesQuery->execute(array($number));
        $findGazelles = $findGazellesQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($findGazelles) != 0) {
            return $findGazelles;
        } else {
            return false;
        }
    }

    static function GetWorking()
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $selectBussQuery = $db->prepare('SELECT * FROM vgazelles WHERE Statement <> "В ремонте"');
        $selectBussQuery->execute();
        $busess = $selectBussQuery->fetchAll(PDO::FETCH_OBJ);
        if ($busess) {
            return $busess;
        } else {
            return false;
        }
    }

    protected function CheckDublicates($gazelle, $db, $switch)
    {
        if ($switch === "create") {
            $dubclicateQuery = $db->prepare('CALL spGetGazelleNumber (?)');
            $dubclicateQuery->execute(array($gazelle->number));
            $currentGazelle = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (!$currentGazelle) {
                return true;
            } else {
                echo('Такое маршрутное такси уже существует!');
            }
            
        } else if ($switch === "UPDATE"){
            $dubclicateQuery = $db->prepare('CALL spGetGazelleNumber (?)');
            $dubclicateQuery->execute(array($gazelle->number));
            $currentGazelle = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (count($currentGazelle) == 0 || count($currentGazelle) == 1) {
                return true;
            } else {
                echo('Такое маршрутное такси уже существует!');
            }
        }
        
    }

    static function Show()
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $selectGazellesQuery = $db->prepare('SELECT * FROM vgazelles');
        $selectGazellesQuery->execute();
        $gazelles = $selectGazellesQuery->fetchAll(PDO::FETCH_OBJ);
        if ($gazelles) {
            return $gazelles;
        } else {
            return false;
        }
    }

    static function Validate($tram, $photo)
    {
        function ValidateNumber($number) {
            try {
                if ($number ?? '') {
                    if (mb_strlen($number) == 11) {
                        preg_match('/([А-Яа-я]{2}[0-9]{4}[6][4][R][U][S])/u', $number, $regNumber);
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
                    echo("Вы не ввели номер маршрутного такси!");
                }
                
                if ($error->getMessage() === 'Length Number Error') {
                    echo("Номер маршрутного такси должен быть длиной 11 символов!");
                }
    
                if ($error->getMessage() === 'Uncorrect Number Error') {
                    echo("Неверный формат номера маршрутного такси!");
                }
            }
        }
    
        function ValidatePhoto($photo)
        {
            try {
                if (substr($_SERVER['HTTP_REFERER'], -37, 11) === 'gazelleinfo') {
                    if (!($photo ?? '')) {
                        return true;
                    }
                }
                if (($photo['tmp_name']?? '') && (is_uploaded_file($photo['tmp_name']))) {
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
                
                if ($error->getMessage() === 'Size Photo Error') {
                    echo('Размер фотографии не должен превышать более 2 Мбайт!');
                }
    
                if ($error->getMessage() === 'Extension Photo Error') {
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

interface IGazelle {
    function Create($gazelle);
    function Update($gazelle);
}

?>