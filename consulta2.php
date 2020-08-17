<?php
require "./vendor/autoload.php";

use Ziminny\Paginate\db\AjaxTable;
use Ziminny\Paginate\db\Conn;
use Ziminny\Paginate\db\Table;


$select = new AjaxTable();
$select->conn = Conn::self();
//$select->rowPerPage = 5;
$select->paginaateResponsive = true;

$table = [
    
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
            'age'  => 'Idade',
            
    ],
    'innerJoin' => [
        'alias' => true,
        'table'   => 'address',
        'compare' => 'id_address=id',
        'columns' => 'street,num',
        'personalizeFields' => [
            'street' => 'Rua',
            'num' => 'Número'
        ]

    ],
    'viewsCount' => [
        
            'position' => 'center',
            'text'     => 'Total de - ' 
    ]

    ];

    $paginate = [
        'paginateResponsive' => true,
        'position' => 'end',
        'previous' => '',
        'next' => ''
    ];

$select->table($table)
        ->paginate($paginate)
        ->run();