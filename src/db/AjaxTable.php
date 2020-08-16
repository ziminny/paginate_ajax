<?php

namespace Ziminny\Paginate\db;

use PDO;

class AjaxTable extends AbstractModelController{

    public $thisClass;

    public function __construct($connection = null)
    {
     
        parent::__construct($connection);
    }

    public function table($array) 
    {
          $this->thisClass =  parent::createTable($array); 
          return $this;

    }

    public function paginate() 
    {       $this->thisClass .= parent::pagination();
            return $this;
    }

    public function run()
    {
         echo $this->thisClass;
    } 



}