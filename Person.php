<?php

function validateDate($date, $format = 'Y-m-d')
{
    $date = preg_replace('/(.*)Z$/', '${1}+00:00', $date);
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

class Person
{
    public $id, $name, $surname,  $birthday, $male, $city;
    function __construct()
    {
        $parameters = func_get_args();
        $i = func_num_args();
        switch ($i)
        {
            case 1:
                $this->getObjfromBd(func_get_args(0));
                break;
            case 6:
                $this->id = func_get_arg(0);
                $this->name = func_get_arg(1);
                $this->surname = func_get_arg(2);
                $this->birthday = func_get_arg(3);
                $this->male = func_get_arg(4);
                $this->city = func_get_arg(5);
                $this->save();
                break;
        }
    }
    function validate()
    {
        if(!is_numeric($this->id))
        {
            return false;
        }
        if(!ctype_alpha($this->name) || !ctype_alpha($this->surname))
        {
            return false;
        }
        if(!var_dump(validateDate($this->birthday)))
        {
            return false;
        }
        if($this->male != 0 && $this->male != 1)
        {
            return false;
        }
        if(!ctype_alpha($this->city))
        {
            return false;
        }
        return true;
    }

    function getObjfromBd()
    {
        try {
            $conn = new PDO('mysql:host=localhost;dbname=testdb1', 'root', 'mypassword');
            $sql = "SELECT * FROM Person WHERE id = {$this->id} ";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            if($stmt->rowCount() > 0){
                foreach ($stmt as $row)
                {
                  $this->id =  $row['id'];
                  $this->name = $row['name'];
                  $this->surname = $row['surname'];
                  $this->birthday = $row['birthday'];
                  $this->male = $row['male'];
                  $this->city = $row['city'];
                  if(!$this->validate())
                  {
                    echo 'Validate failed';
                  }
                }
            }
            else{
                echo 'Info not found';
            }
        }
        catch (PDOException $e) {
            echo 'Database error: ' . $e->getMessage();
        }
    }

     function save()
     {
         if ($this->validate())
        {
            try {
                $conn = new PDO('mysql:host=localhost;dbname=dbname', 'root', 'mypassword');
                $sql = "INSERT INTO Person (id, name, surname, birthday, male, city ) VALUES (' {$this->id}', '{$this->name}', '{$this->surname}' , '{$this->birthday}' , '{$this->male}', '{$this->city}')";
                $conn->exec($sql);
                echo 'Person saved successfully';
            }
            catch (PDOException $e) {
                echo 'Database error: ' . $e->getMessage();
            }
        }
        else
        {
            echo 'Validate failed';
        }
    }

     function delete()
     {
        try {
            $conn = new PDO('mysql:host=localhost;dbname=dbname', 'root', 'mypassword');
            $sql = "DELETE FROM Person WHERE id = '{$this->id}'";
            $affectedRowsNumber = $conn->exec($sql);
            echo 'Remuved successfully';
        }
        catch (PDOException $e) {
            echo 'Database error: ' . $e->getMessage();
        }
     }

     static function convertedDate($Date)
     {
        $date_a = new DateTime($Date);
        $date_b = new DateTime();
        $interval = $date_b->diff($date_a);
        return $interval->format('%Y');
     }

     static function convertMale($male)
     {
         if ($male == 1)
         {
             return 'Муж';
         }
         elseif ($male == 0)
         {
            return 'Жен';
         }
     }

     function formate($date, $male)
     {
        if(isset($date) && !isset($male))
        {
            $person =  new Person($this->id, $this->name, $this->surname, $this->convertedDate($date), $this->male, $this->city);
            return $person;
        }
        elseif(!isset($date) && isset($male))
        {
            $person =  new Person($this->id, $this->name, $this->surname, $this->birthday, $this->convertMale($male), $this->city);
            return $person;
        }
        elseif(isset($date) && isset($male))
        {
            $person =  new Person($this->id, $this->name, $this->surname, $this->convertedDate($date), $this->convertMale($male), $this->city);
            return $person;
        }
        else
        {
            $person =  new Person($this->id, $this->name, $this->surname, $this->birthday, $this->male, $this->city);
            return $person;
        }
     }
}
