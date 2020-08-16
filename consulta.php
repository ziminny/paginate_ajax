<?php
require "./vendor/autoload.php";
use Ziminny\Paginate\db\Conn;

$connect = Conn::self();

$limit = 5;
$page = 1;

if($_POST['page'] > 1) {

    $start = (($_POST['page'] - 1) * $limit);
    $page = $_POST['page'];

}
else {

    $start = 0;

}

$query = "
        SELECT * FROM person 
";


if($_POST['query'] != "") {

    $query .= ' 
        WHERE name LIKE "%' . str_replace(' ' , '%' , $_POST['query']) .'%"   
    ';
}

$query .= '
        ORDER BY name ASC
';

$filter_query = $query . ' LIMIT ' . $start . ' , ' . $limit. '';

$statement = $connect->prepare($query);
$statement->execute();

$total_data = $statement->rowCount();

$statement = $connect->prepare($filter_query);
$statement->execute();

$result = $statement->fetchAll();

$output = '
    <label> Total de dados - ' . $total_data . '</label>
    <table class="table table-striped table-borded">
        <tr>
            <th>ID </th>
            <th>Nome </th>
            <th>Idade </th>
        </tr>    
';

if($total_data > 0) {

    foreach($result as $row) {
            $output .= '
                <tr>
                    <td> '.$row['id'].'</td>
                    <td> '.$row['name'].'</td>
                    <td> '.$row['age'].'</td>
                </tr>
            ';
    }

}
else {

    $output .= '
            <tr>
                <td colspan="2">Sem registro !</td>
    ';

}

$output .= '
        </table>
        <br/>
        <div align="center">
            <ul class="pagination">

';

$total_links = ceil($total_data/$limit);

$previous_link = '';

$next_link = '';

$page_link = '';

if($total_links > 4) {

    if($page < 5) {
            for ($count=1; $count <=5 ; $count++) { 
                    $page_array[] = $count;
            }
            $page_array[] = "...";
            $page_array[] = $total_links;
    }
    else {
        $end_limit = $total_links -5;
           if($page > $end_limit) {
                $page_array[] = 1;
                $page_array[] = "...";

                for ($count = $end_limit; $count<=$total_links ; $count++) {
                    $page_array[] = $count;
                } 
           }
           else {
            $page_array[] = 1; 
            $page_array[] = "...";

            for ($count=$page - 1; $count <= $page + 1 ; $count++) { 
                $page_array[] = $count;
                }
                $page_array = "...";
                $page_array[] = $total_links;
           }

    }

} // fim primeiro IF $total_links > 4
else {

    for ($count = 1; $count <=$total_links; $count++) {
        $page_array[] = $count;
    }

}

for ($count=0; $count < count($page_array); $count++) { 
        if($page == $page_array[$count]) {
            $page_link .= '
                <li class="page-item active">
                    <a class="page-link" href="">' .$page_array[$count].'
                      <span class="sr-only">(current)</span>
                    </a>
                </li>
            ';

            $previous_id = $page_array[$count] - 1;

            if($previous_id > 0) {
                $previous_link = '
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" data-page_number="'.$previous_id.'">Anterior </a>
                    </li>
                ';
            }
            else {
                $previous_link = '
                <li class="page-item disabled">
                   <a class="page-link" href="#">Anterior </a>
                </li> 
                ';
                
            }

            $next_id = $page_array[$count] + 1;

            if($next_id > $total_links) {
                $next_link = '
                    <li class="page-item disabled">
                      <a class="page-link" href="#">Próximo </a>
                    </li> 
                ';
            }
         
            else {

                $next_link = '
                  <li class="page-item">
                   <a class="page-link" href="javascript:void(0)" data-page_number="'.$next_id.'">Próximo </a>
                  </li>
                ';

            }


        } // final if  
        else {

                if($page_array[$count] == '...') {

                    $page_link .= '
                       <li class="page-item disabled">
                          <a class="page-link" href="#">...</a>
                       </li>       
                    ';

                }
                else {
                    $page_link .= '
                      <li class="page-item">
                        <a class="page-link" href="" data-page_number="'.$page_array[$count].'">'.$page_array[$count].' </a>
                      </li> 
                    ';
                }

        }
}

$output .= $previous_link . $page_link . $next_link;


echo $output;



       