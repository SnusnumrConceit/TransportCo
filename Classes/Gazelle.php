<?php
class Gazelle implements IGazelle{
    protected $id;
    protected $number;
    protected $route;
    protected $statement;
    protected $photo;

    public function Create($gazelles)
    {   
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($gazelles, $db, 'create')) {
            $createGazelleQuery = $db->prepare('CALL spCreateGazelle (?, ?, ?, ?, ?)');
            $createGazelleQuery->execute(array($gazelles->id, $gazelles->number, $gazelles->route, $gazelles->statement, $gazelles->photo));
        }
    }

    public function Update($gazelles)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($gazelles, $db, 'UPDATE')) {
            if ($gazelles->photo ?? '') {
                $updateGazelleQuery = $db->prepare('CALL spUpdateGazelleWithPhoto (?, ?, ?, ?, ?)');
                $updateGazelleQuery->execute(array($gazelles->number, $gazelles->route, $gazelles->statement, $gazelles->photo, $gazelles->id));
            } else {
                $updateGazelleQuery = $db->prepare('CALL spUpdateGazelle (?, ?, ?, ?)');
                $updateGazelleQuery->execute(array($gazelles->number, $gazelles->route, $gazelles->statement, $gazelles->id));
            }
            
            
        }
    }

    public function Delete($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $deleteGazelleQuery = $db->prepare('CALL spDeleteGazelle (?)');
        $deleteGazelleQuery->execute(array($id));
    }

    public function Get($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $getGazelleQuery = $db->prepare('SELECT * FROM vgazelles WHERE id = ?');
        $getGazelleQuery->execute(array($id));
        $gazelles = $getGazelleQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($gazelles) == 1) {
            return $gazelles;
        } 
    }

    public function Find($number)
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

    protected function CheckDublicates($gazelles, $db, $switch)
    {
        if ($switch === "create") {
            $dubclicateQuery = $db->prepare('CALL spGetGazelleNumber (?)');
            $dubclicateQuery->execute(array($gazelles->number));
            $currentGazelle = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (!$currentGazelle) {
                return true;
            } else {
                echo('Такой троллейбус уже существует!');
            }
            
        } else if ($switch === "UPDATE"){
            $dubclicateQuery = $db->prepare('CALL spGetGazelleNumber (?)');
            $dubclicateQuery->execute(array($gazelles->number));
            $currentGazelle = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (count($currentGazelle) == 0 || count($currentGazelle) == 1) {
                return true;
            } else {
                echo('Такой троллейбус уже существует!');
            }
        }
        
    }

    function Show()
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $selectGazellesQuery = $db->prepare('SELECT * FROM vgazelles');
        $selectGazellesQuery->execute();
        $gazelless = $selectGazellesQuery->fetchAll(PDO::FETCH_OBJ);
        if ($gazelless) {
            return $gazelless;
        } else {
            return false;
        }
    }

    public function Validate($tram, $photo)
    {
        if ($this->ValidateNumber($tram->number) && $this->ValidatePhoto($photo) && $this->ValidateRoute($tram->route)
            && $this->ValidateStatement($tram->statement)) {
            return true;
        }
    }

    public function Set($gazelles, $photo = null)
    {
        if ($gazelles->id ?? '') {
            $this->id = $gazelles->id;
        } else {
            $this->id = uniqid();
        }
        $this->number = $gazelles->number;
        if ($photo ?? '') {
            $this->photo = base64_encode(file_get_contents($photo['tmp_name']));
        } else {
            $this->photo = $photo;
        }
        $this->route = $gazelles->route;
        $this->statement = $gazelles->statement;
        return $this;
    }

protected function ValidateNumber($number) {
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

    protected function ValidatePhoto($photo)
    {
        try {
            if (substr($_SERVER['HTTP_REFERER'], -13, 11) === 'gazelleinfo') {
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

    protected function ValidateRoute($route)
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

    protected function ValidateStatement($statement)
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
}

interface IGazelle {
    function Show();
    function Create($gazelles);
    function Update($gazelles);
    function Delete($id);
    function Get($id);
    function Find($number);
    function Validate($gazelles, $photo);
    function Set($gazelles, $photo);
}

?>