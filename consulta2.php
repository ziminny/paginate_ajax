<?php
require "./vendor/autoload.php";

use Ziminny\Paginate\db\AjaxTable;
use Ziminny\Paginate\db\Conn;
use Ziminny\Paginate\db\Table;


$select = new AjaxTable();
$select->conn = Conn::self();
//$select->rowPerPage = 5;
$select->paginaateResponsive = true;

$array = [
    
    'posts' => [
        'pages'  => 'page',
        'input'  => 'query'
    ],
    'table'     => 'person',
    'orderBy'   => 'name',
    'where'     => 'name',
    'columns'   => 'id,name, age',
    'personalizeFields' => [
            'id'   => 'Código',
            'name' => 'Nome',
            'age'  => 'Idade'
    ]

    ];

$select->table($array)->paginate()->run();