<?php

use Ziminny\Paginate\db\Conn;
use Ziminny\Paginate\db\Person;

require "./vendor/autoload.php";



?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>

  <div class="container mt-5 ">

  <ul class="list-group ">
  <li class="list-group-item bg-light">Lista Dinâmica</li>
  <li class="list-group-item">
  
  <div class="input-group flex-nowrap mb-1">
  <div class="input-group-prepend">
    <span class="input-group-text" id="addon-wrapping">Pesquisa</span>
  </div>
  <input type="text" class="form-control" placeholder="Perquisar ..." aria-label="Username" aria-describedby="addon-wrapping" id="search_box" name="search_box">
</div>

<div id="dynamic_content" class="table-responsive"></div>


  </li>

</ul>

  </div>
  

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready( function() {
            
            function load_data(page, query = "") {

                    $.ajax( {
                        url:"consulta2.php",
                        method:"POST",
                        data: {
                            page:page ,query:query
                        },
                        success: function(response) {
                            $("#dynamic_content").html(response)
                        },
                        error: function(error) {
                              console.log(error)  
                        }
                    });

            }
            
          
                load_data(1);
                
                $(document).on("click",'.page-link', function(e) {
                   
                    var page = $(this).data('page_number');
                    var query = $("#search_box").val();
                    load_data(page,query);
                    console.log(page);
                    e.preventDefault();

                })

                $("#search_box").on('keyup', () => {
                  load_data(1,$("#search_box").val());
                })
            

        });
    </script>

 

</body>
</html>



