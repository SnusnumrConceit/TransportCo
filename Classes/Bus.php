<?php
class Bus implements IBus{
    protected $id;
    protected $number;
    protected $route;
    protected $statement;
    protected $photo;

    public function Create($buses)
    {   
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($buses, $db, 'create')) {
            $createBusQuery = $db->prepare('CALL spCreateBus (?, ?, ?, ?, ?)');
            $createBusQuery->execute(array($buses->id, $buses->number, $buses->route, $buses->statement, $buses->photo));
        }
    }

    public function Update($buses)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($buses, $db, 'UPDATE')) {
            if ($buses->photo ?? '') {
                $updateBusQuery = $db->prepare('CALL spUpdateBusWithPhoto (?, ?, ?, ?, ?)');
                $updateBusQuery->execute(array($buses->number, $buses->route, $buses->statement, $buses->photo, $buses->id));
            } else {
                $updateBusQuery = $db->prepare('CALL spUpdateBus (?, ?, ?, ?)');
                $updateBusQuery->execute(array($buses->number, $buses->route, $buses->statement, $buses->id));
            }
            
            
        }
    }

    public function Delete($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $deleteBusQuery = $db->prepare('CALL spDeleteBus (?)');
        $deleteBusQuery->execute(array($id));
    }

    public function Get($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $getBusQuery = $db->prepare('SELECT * FROM vbuses WHERE id = ?');
        $getBusQuery->execute(array($id));
        $buses = $getBusQuery->fetchAll(PDO::FETCH_OBJ);
        if (count($buses) == 1) {
            return $buses;
        } 
    }

    public function Find($number)
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

    protected function CheckDublicates($buses, $db, $switch)
    {
        if ($switch === "create") {
            $dubclicateQuery = $db->prepare('CALL spGetBusNumber (?)');
            $dubclicateQuery->execute(array($buses->number));
            $currentBus = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (!$currentBus) {
                return true;
            } else {
                echo('Такой троллейбус уже существует!');
            }
            
        } else if ($switch === "UPDATE"){
            $dubclicateQuery = $db->prepare('CALL spGetBusNumber (?)');
            $dubclicateQuery->execute(array($buses->number));
            $currentBus = $dubclicateQuery->fetchAll(PDO::FETCH_OBJ);
            if (count($currentBus) == 0 || count($currentBus) == 1) {
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
        $selectBussQuery = $db->prepare('SELECT * FROM vbuses');
        $selectBussQuery->execute();
        $busess = $selectBussQuery->fetchAll(PDO::FETCH_OBJ);
        if ($busess) {
            return $busess;
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

    public function Set($buses, $photo = null)
    {
        if ($buses->id ?? '') {
            $this->id = $buses->id;
        } else {
            $this->id = uniqid();
        }
        $this->number = $buses->number;
        if ($photo ?? '') {
            $this->photo = base64_encode(file_get_contents($photo['tmp_name']));
        } else {
            $this->photo = $photo;
        }
        $this->route = $buses->route;
        $this->statement = $buses->statement;
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

interface IBus {
    function Show();
    function Create($buses);
    function Update($buses);
    function Delete($id);
    function Get($id);
    function Find($number);
    function Validate($buses, $photo);
    function Set($buses, $photo);
}

?>