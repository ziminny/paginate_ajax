<?php

namespace Ziminny\Paginate\db;

abstract class AbstractModelController {
    // linhas por página
    public $rowPerPage = 8;
    // Conexão do bando de dados
    public $conn = null;
    // caso esteja na primera pagina
    private $start = 0;
    // total de linhas encontradas , se @param $postQuery nao for definito busta todos os registros
    private $totalRow;
    protected $page;
    // array , transformo os dados em array "id,nome,idade" [ [0] => 'id' [1] => 'nome' [2] => 'idade ]
    private $columnsTable; 
    // Caso queira personalizar o nome das colunas
    private $personalizeColumnsName;
    private $personalizeColumnsNameInnserJoin;
    private $positionPagination = 'center';

    private $newRowsInTableInnerJoin;

    public function __construct($connection)
    {
        if($this->conn == null)
        $this->conn = $connection;
    }

    /**
     * @param $array  -> recebe um array dos dados e convertido em objeto 
     */
    protected function createTable($array) 
    {
        // /utils/functions.php filtra os dados e converte em objeto
        $datas = initConfigs($array);

       
        $alias = $datas->innerJoin['alias'];
        


        $this->personalizeColumnsName = $datas->columnsTable;
        $this->personalizeColumnsNameInnserJoin = $datas->innerJoin['personalizeFields'];
        $this->page = 1;
        // retiro todos os espaços exemplo : { id , nome , idade} - > {id,nome,idade}
        $datas->columns = str_replace(' ' , '' ,$datas->columns);

        if($datas->pages > 1) :
            $this->start = (($datas->pages - 1) * $this->rowPerPage);
            $this->page = $datas->pages;
        endif;

        $isFrom = $datas->columns == '' ? '*' : $datas->columns;
                    // tranformo em um array contendo os cabeçalhos da tabela
            $joinAlias = '';
            $join = '';
            
            $this->columnsTable = explode(",",$datas->columns);

            if($alias) :
                $explodeJoin = explode(',',$datas->innerJoin['columns']);
                $mergeJoinWithSelect = array_merge($this->columnsTable,$explodeJoin);
               
                for ($i=0; $i <count($mergeJoinWithSelect) ; $i++) { 
                    if(count($this->columnsTable) <= $i) : 
                        $this->newRowsInTableInnerJoin[$i] = $datas->innerJoin['table'].$mergeJoinWithSelect[$i];
                        $joinAlias .= $datas->innerJoin['table'].".".$mergeJoinWithSelect[$i] . " as " .$this->newRowsInTableInnerJoin[$i] . ",";  
                    else:
                        $this->newRowsInTableInnerJoin[$i] = $datas->table.$mergeJoinWithSelect[$i];
                        $joinAlias .= $datas->table.".".$mergeJoinWithSelect[$i] . " as " .$this->newRowsInTableInnerJoin[$i] . ",";  
                       
                    endif;
                    
                       
                }
                $datas->innerJoin['columns'] = str_replace(" ", "",$datas->innerJoin['columns']);
                $ecplodeInnerJoin = explode(",",$datas->innerJoin['columns']);
                $this->columnsTable = array_merge($this->columnsTable , $ecplodeInnerJoin);
                $joinAlias = substr($joinAlias , 0 ,-1);
                $innerJoinTaableName = $datas->innerJoin['table'];

                $compare = str_replace(" " , "" , $datas->innerJoin['compare']);
                $explodeCompare = explode("=",$compare);
                $join = ' INNER JOIN '. $innerJoinTaableName . ' ON ' . $datas->table.'.'.$explodeCompare[0] . '=' . $innerJoinTaableName .'.'.$explodeCompare[1];
            else:
                $joinAlias = $isFrom;
                $this->newRowsInTableInnerJoin = $this->columnsTable;
            endif;
         
        $query = '
                SELECT '.$joinAlias .' from '. $datas->table .$join .  '
        ';

        if($datas->input != '' ) : 
            $query .= '
            WHERE '. $datas->where .' LIKE "%' . str_replace(' ' , '%' , $datas->input) .'%"  
            ';

        endif;

        if($datas->orderBy != '') :
            $query .= '
                ORDER BY '. $datas->orderBy .' ASC
            ';
        endif;

        //$filter = " {$query} LIMIT {$this->start} , {$this->rowPerPage}";
        $filter = $query . ' LIMIT ' . $this->start . ' , ' . $this->rowPerPage. '';

        // Executo a primeira query p/ pegar a quantidade de registros
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        // pego o total de resgitros
        $this->totalRow = $stmt->rowCount();
        // executo a querry de acordo com a pagina
        // exemplo : se esta na pagina 5 pego os registros somente da pagina 5
        $stmt = $this->conn->prepare($filter);
        $stmt->execute();

        $result = $stmt->fetchAll();

        return $this->createTableBootstrap($result , $datas);

    }

    /**
     * Cria uma tabela com uma cor default que pode ser personalizada 
     * @param $resul -> consulta retornada pelo banco de dados
     */
    private function createTableBootstrap($result , $array) 
    {
              $flex = (!isset($array->viewsCount['position']) || $array->viewsCount['position'] == '') ? 'end' : $array->viewsCount['position'];
              $total = (!isset($array->viewsCount['text']) || $array->viewsCount['text'] == '')  ? 'Total - ' : $array->viewsCount['text'];  
        $output = '
             <label class="d-flex justify-content-' .$flex.' mr-1">'.$total. $this->totalRow . '</label>
             <table class="table table-striped border table-borded">
             <tr>
                '. $this->collumnsInTable().'
                
             </tr>';

                if($this->totalRow > 0) :

                    foreach($result as $row) :
                            $output .= '
                                <tr>
                                '. $this->rowsInTable($row) .'
                               
                                </tr>
                            ';
                    endforeach;
                else :

                        $output .= '
                                <tr>
                                    <td colspan="2">Sem registro !</td>
                        ';

            endif;
        $output .= '</table>';

    return  $output;

    }
    /**
     * @return html td
     * retorna as colunas
     */
    private function rowsInTable($row)
     {
         $output = '';
        for ($i=0; $i < count($this->newRowsInTableInnerJoin); $i++) : 
            $output .= "
                <td>" . $row[$this->newRowsInTableInnerJoin[$i]] . "</td>
            ";
        endfor;
        return $output;
     }
     /**
      * @return html th
      * retorna as linha com os registros
      */
     private function collumnsInTable() {
        $joinPersolaliseFields = array_merge($this->personalizeColumnsName,$this->personalizeColumnsNameInnserJoin);
        $columns = '';      
        for ($i=0; $i <count($this->newRowsInTableInnerJoin) ; $i++) :
        
                if(isset($this->personalizeColumnsName[$this->columnsTable[$i]] )) :
            $columns .="<th>". $this->personalizeColumnsName[$this->columnsTable[$i]] ."</th>";
                elseif(isset($this->personalizeColumnsNameInnserJoin[$this->columnsTable[$i]]) ):
                    $columns .="<th>". $this->personalizeColumnsNameInnserJoin[$this->columnsTable[$i]] ."</th>";
                else:    
                    $columns .="<th>". $this->columnsTable[$i] ."</th>";
                endif;
            endfor;
        return $columns;
     }

     /**
      * @return html
      *retorna um html contendo a paginação
      */
     protected function pagination($array) {

            if(is_array($array)) :
                $array  = (object) $array;
            endif;
            $this->positionPagination = isset($array->position) ? $array->position : $this->positionPagination;
        $output2 = '

        <div class="d-flex justify-content-'.$this->positionPagination.' p-0 mt-1" id="main-div-pagination">
        <ul class="pagination" id="ul-paginate-responsive">
';
        // arredondo p cima a quantidade de links quanditade de linhas dividido pela quandidade de registros
        $totalLinks = ceil($this->totalRow/$this->rowPerPage);  
        $page_array = $this->fillArray($totalLinks);

        $previous_link = '';
        $next_link = '';
        $page_link = '';

        $prevLinkName = (isset($array->previous) && $array->previous != '') ? $array->previous : '&laquo;';
        $nextLinkName = (isset($array->next) && $array->next != '') ? $array->next : '&raquo';

    for ($count=0; $count < count($page_array); $count++) :
            if($this->page == $page_array[$count]) :
                $page_link .= '
                    <li class="page-item active">
                        <a class="page-link" href="">' .$page_array[$count].'
                        <span class="sr-only">(current)</span>
                        </a>
                    </li>
                ';

            $previous_id = $page_array[$count] - 1;
           
                        if($previous_id > 0) :
                            $previous_link = '
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)" data-page_number="'.$previous_id.'">'. $prevLinkName .' </a>
                                </li>
                            ';
                        else:
                            $previous_link = '
                            <li class="page-item disabled">
                            <a class="page-link" href="#">'. $prevLinkName .'  </a>
                            </li> 
                            ';
                            
                        endif;

            $next_id = $page_array[$count] + 1;

                        if($next_id > $totalLinks) :
                                $next_link = '
                                <li class="page-item disabled">
                                <a class="page-link" href="#"> '. $nextLinkName .' </a>
                                </li>';

                        else:
                            $next_link = '
                            <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" data-page_number="'.$next_id.'">'. $nextLinkName .' </a>
                            </li>
                            ';
                        endif;


             else : // final if  
                        if($page_array[$count] == '...') :

                            $page_link .= '
                            <li class="page-item disabled">
                                <a class="page-link" href="#">...</a>
                            </li>       
                            ';
                        else:
                            $page_link .= '
                            <li class="page-item">
                                <a class="page-link" href="" data-page_number="'.$page_array[$count].'">'.$page_array[$count].' </a>
                            </li> 
                            ';
                        endif;
            endif;
    endfor;

return $output2 .=$previous_link . $page_link . $next_link . "</ul></div>" ;

     }


/**
 * @return array
 * retorna um array contendo todos os links
 */
     private function fillArray($totalLinks) 
     {
        $page_array = [];
        $end_limit = $totalLinks -5;
        $ifLimit = $end_limit <= 0 ? 4 : 5;
        // se só conter 4 páginas nao adiciona as [...]
        if($totalLinks > 4) :
            // link atual que esta , se até quatro a diciona [...] uma unidade antes do final ex.: [1] [2] [3] [4] [5] [...] [10]
            if($this->page < 5 && $end_limit >0) :
                        for ($count=1; $count <=$ifLimit ; $count++) :
                                $page_array[] = $count;
                        endfor;
                        $page_array[] = "...";
                        $page_array[] = $totalLinks;
                        
            elseif($end_limit <= 0 && $this->page < 4 ):

                for ($count=1; $count <= $ifLimit ; $count++) :
                    $page_array[] = $count;
            endfor;
            $page_array[] = "...";
            $page_array[] = $totalLinks;

            else:
                  
                   if($this->page > $end_limit) :
                    
                        $page_array[] = 1;
                    
                        $page_array[] = "...";
                        
                     
                            $end_limit = $end_limit <= 0 ? 2 : $end_limit;
                            for ($count = $end_limit; $count<=$totalLinks ; $count++) :
                                $page_array[] = $count;
                            endfor;
                    else:

                   
                    $page_array[] = 1; 
                    $page_array[] = "...";
                          
                            for ($count= $this->page - 1; $count <= $this->page + 1 ; $count++) : 
                                $page_array[] = $count;
                            endfor;
                        $page_array[] = "...";
                        $page_array[] = $totalLinks;

                    endif;
            endif;      
        else :
            for ($count = 1; $count <=$totalLinks; $count++) :
                $page_array[] = $count;
            endfor;
        endif;

        return $page_array;
     }

     protected function responsivePaginateResize($array) {
        if(is_array($array)):
            $array = (object) $array;
        endif;

        $this->positionPagination = $array->position;
     if($array->paginateResponsive) :
        $js = '';
        $handle = '';
        $file = '';
        $folder = $_SERVER['DOCUMENT_ROOT'] . "/config_paginate";
        // crio uma parta no diretorio raiz
        if(!file_exists($folder))
        mkdir($folder);
        // faço a copia dos arquivos p/ a pasta
        if(!file_exists($folder . "/responsive_paginate.js"))    
        copy(dirname(__DIR__) . "/js/responsive_paginate.js" , $_SERVER['DOCUMENT_ROOT'] . "/config_paginate/responsive_paginate.js");

        if ($handle = opendir($_SERVER['DOCUMENT_ROOT']. '/config_paginate')) {
            
            while (false !== ($file = readdir($handle))) {
                if (is_file($_SERVER['DOCUMENT_ROOT'] . '/config_paginate//' . $file)) {
                    // verifico de o protocolo é http ou
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                    // adiciono todos os arquinos na pasta    
                    $js .= '<script src="'.$protocol . $_SERVER['HTTP_HOST']. '/config_paginate'.'/'.$file . '" type="text/javascript"></script>' . "\n";            
                }
            }
            closedir($handle);
            return $js;
        }
        
    endif;
     }
 
}