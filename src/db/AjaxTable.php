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

    public function paginate($array = []) 
    {       
  
        $this->thisClass .= parent::pagination($array) . parent::responsivePaginateResize($array);

            return $this;
    }

    public function run()
    {
         echo $this->thisClass;
    } 


}