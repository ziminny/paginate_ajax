<?php

namespace Ziminny\Paginate\db;

use PDO;

/**
 * Static class , conenction
 */

 class Conn {

   private static $c;

    public static function self() {

        try {
            self::$c = new PDO("mysql:host=mysql;dbname=testar;port=3306","root","root");
        } catch (\Throwable $th) {
            echo $th;
            die();
        }
        return self::$c;
    }
 }