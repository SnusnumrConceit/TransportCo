<?php
class Tax implements ITax {
    protected $id;
    protected $desc;
    protected $size;

    public function Create($tax)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        if ($this->CheckDublicates($db, $tax, 'create')) {
            $createTaxQuery = $db->prepare('CALL spCreateTax (?, ?, ?)');
            $createTaxQuery->execute(array($tax->id, $tax->desc, $tax->size));    
        }
    }

    protected function CheckDublicates($db, $tax, $switch)
    {
        if ($switch == 'create') {
            $checkTaxQuery = $db->prepare('SELECT * FROM vtaxes WHERE Description = ?');
            $checkTaxQuery->execute(array($tax->desc));
            $findlessTax = $checkTaxQuery->fetchAll();
            if (count($findlessTax) == 0) {
                return true;
            } else {
                echo('Такой штраф уже существует');
            }
            
        } elseif ($switch == 'update') {
            $checkTaxQuery = $db->prepare('SELECT * FROM vtaxes WHERE Description = ?');
            $checkTaxQuery->execute($tax->desc);
            $findlessTax = $checkTaxQuery->fetchAll();
            if (count($findlessTax) == 0 || count($findlessTax) == 1) {
                return true;
            } else {
                echo('Такой штраф уже существует');
            }
        }
        
    }

    public function Update($tax)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $updateTaxQuery = $db->prepare('CALL spUpdateTax (?,?,?)');
        $updateTaxQuery->execute(array($tax->desc, $tax->size, $tax->id));
    }

    public function Delete($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $deleteTaxQuery = $db->prepare('CALL spDeleteTax (?)');
        $deleteTaxQuery->execute(array($id));
    }

    public function Find($desc)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $findTaxQuery = $db->prepare('SELECT * FROM vtaxes WHERE Description = ?');
        $findTaxQuery->execute(array($desc));
        $taxes = $findTaxQuery->fetchAll(PDO::FETCH_OBJ);
        if($taxes) {
            return $taxes;
        } else {
            return false;
        }
    }

    public function Get($id)
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $getTaxQuery = $db->prepare('SELECT * FROM vtaxes WHERE id = ?');
        $getTaxQuery->execute(array($id));
        $tax = $getTaxQuery->fetchAll(PDO::FETCH_OBJ);
        if ($tax) {
            return $tax;
        } else {
            return false;
        }
    }

    public function Show()
    {
        require_once 'DbConnect.php';
        $db = DbConnect();
        $selectTaxesQuery = $db->prepare('SELECT * FROM vtaxes');
        $selectTaxesQuery->execute();
        $taxes = $selectTaxesQuery->fetchAll(PDO::FETCH_OBJ);
        if ($taxes) {
            return $taxes;
        } else {
            return false;
        }
    }

    public function Set($tax)
    {
        if ($tax->id ?? '') {
            $this->id = $tax->id;
        } else {
            $this->id = uniqid();
        }
        $this->desc = $tax->desc;
        $this->size = $tax->size;
        return $this;
    }

    public function Validate($tax)
    {
        if ($this->ValidateDesc($tax->desc) && $this->ValidateSize($tax->size)) {
            return true;
        }
    }

    protected function ValidateDesc($desc) {
        try {
            if ($desc ?? '') {
                $descLen = mb_strlen($desc);
                if (($descLen <= 100) && ($descLen >= 0)) {
                    preg_match('/([а-яёА-ЯЁ0-9\/ ])+/u', $desc, $regDesc);
                    if ($regDesc ?? '') {
                        if ($regDesc[0] == $desc) {
                            return true;
                        } else {
                            throw new Exception("Uncorrect Description Error", 1);
                        }
                    } else {
                        throw new Exception("Uncorrect Description Error", 1);
                    }
                } else {
                    throw new Exception("Length Desc Error", 1);
                }
            } else {
                throw new Exception("Empty Desc Error", 1);
                
            }
        } catch (Exception $error) {
            if ($error->getMessage() === 'Empty Desc Error') {
                echo('Вы не ввели описание штрафа!');
            }
            if ($error->getMessage() === 'Length Desc Error') {
                echo('Длина штрафа не должна превышать 100 символов!');
            }
            if ($error->getMessage() === 'Uncorrect Desc Error') {
                echo('Описание штрафа должно состоять из букв русского алфавита, цифр и слеша!');
            }
        }
    }

    protected function ValidateSize($size)
    {
        try {
            if ($size ?? '') {
                if (is_numeric($size)) {
                    if ($size > 0 && $size <= 50000) {
                        return true;
                    } else {
                        throw new Exception("Length Size Error", 1);
                    }
                } else {
                    throw new Exception("Uncorrect Size Error", 1);
                }
                
            } else {
                throw new Exception("Empty Size Error", 1);
            }
            
        } catch (Exception $error) {
            if ($error->getMessage() == 'Empty Size Error') {
                echo('Вы не указали размер штрафа!');
            }
            if ($error->getMessage() == 'Uncorrect Size Error') {
                echo('Размер штрафа должен состоять из цифр!');
            }
            if ($error->getMessage() == 'Empty Size Error') {
                echo('Размер штрафа не может превышать 50 тыс. рублей!');
            }
        }
    }
}

interface ITax {
    function Show();
    function Create($tax);
    function Update($tax);
    function Delete($id);
    function Get($id);
    function Find($desc);
    function Validate($tax);
    function Set($tax);
}