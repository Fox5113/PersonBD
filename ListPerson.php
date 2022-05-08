<?php
spl_autoload_register(function ($class_name)
{
    include $class_name . '.php';
    if (!class_exists($class_name, false)) {
        throw new LogicException("Unable to load class: $class_name");
    }
});

class ListPerson
{
    public $listId;
    public $arr;
    function __construct($listId)
    {
        
            $this->GetList($listId);
        
    }
    public function GetList($listId)
    {
        if(class_exists(Person::class))
        {
            foreach ($listId as &$item) {
                $person = new Person($item);
                $this->arr->add($person);
            }
        }
    }

    public function DeleteList()
    {
        foreach ($this->arr as &$item) {

            $item->delete();
        }
    }
}

