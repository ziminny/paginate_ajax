# Paginate Ajax


Paginação assincrôna com ajax e jQuery

### Iniciando

```shel
composer require ziminny/paginate_ajax
```

&nbsp;Em sua model insira os seguintes registros

```php
/*
* @return html
*/
public function yourTableModel()
{
          //instancie a classe passando como parametro a sua conexão   
         $select = new \Ziminny\Paginate\db\AjaxTable($this->connect());
         // linhas por pagina default 8
         //$select->rowPerPage = 8;

        /**
         *  configurações da tabela
         */

        $table =
        [

            /*
            * Post METHOD  
            */   
            'posts' => [
                'pages'  => 'page',    // pagina atual que se encontra
                'input'  => 'query'    // campo de pesquisa
            ],

             /**
              * Tabela principal
              */   
            'table'     => 'clients',  // nome da tabela do banco de dados
            'orderBy'   => 'name',        
            'where'     => 'name',     // where name = query
            'columns'   => 'id,name',  // colunas do banco de dados


            'personalizeFields' => [   // apelido p/ as colunas ex.:  [ID] [Código]

                    'id'   => 'Código',
                    'name' => 'Nome',

            ],

             /**
              *  Caso a tabela possua fk
              */   

            'innerJoin' => [


                'alias' => true, // default false , definir para true caso exista fk e queira usar os registros
                'table'   => 'address',  // nome da segunda tabela
                'compare' => 'id_address=id', // ON campo da tablela 1 = campo da tabela 2
                'columns' => 'street,num', // campos da segunda tabela
                'personalizeFields' => [

                    'street' => 'Rua',
                    'num' => 'Número'

                ]

            ],

            /**
             *  Contagem das visuaalizações
             */

            'viewsCount' => [

                    'position' => '', // start , center , end
                    'text'     => ''  // default = Total
            ]

            ];

            /**
             *  Configuração da paginação
             */

            $pag = [

                'paginateResponsive' => false, // se true  exibe três tamanhos diferentes de acordo com a tela
                'position' => 'center', // start , center , end
                'previous' => '', // default <<
                'next' => '' // default >>

            ];

         /**
          *   A paginação nao é obrigatória , basta retirar o método paginate
          * assim como as configurações , caso nao seja passada como parâmetro
          * assumira as conficurações pre definidas  
          */   
        $select->table($table)
                ->paginate($pag)
                ->run();
    }
```

&nbsp;Se preferir crie um arquivo p/ não chamar direto sua model  

```php
// Ex:. arquivo my_page.php
$datas = new MyClass();
$datas->yourTableModel() // return html
```


&nbsp;Na sua view onde os dados serão apresentados insira o seguinte código html , o input de pesquisa é opcional

```html
<input type="text" id="search_box">
<div id="yourDiv">
</div>
```

&nbsp;E por fim o a ajax , que sera responsavel pela troca de informação

```javascript
$(document).ready( function() {

            function load_data(page, query = "") {

                    $.ajax( {
                        url:"my_page.php",
                        method:"POST",
                        data: {
                            page:page ,query:query // page e query POST METHOD
                        },
                        success: function(response) {
                            $("#yourDiv").html(response) // conteudo , resposta
                        },
                        error: function(error) {
                              console.log(error)  
                        }
                    });
            }

                // carregamento da página , começa na página 1
                load_data(1);
                // a cada click no paginate envia a pagina que se encontra
                $(document).on("click",'.page-link', function(e) {

                    var page = $(this).data('page_number');
                    var query = $("#search_box").val();
                    load_data(page,query);
                    e.preventDefault();

                })
                // evento keyup
                $("#search_box").on('keyup', () => {
                  load_data(1,$("#search_box").val());
                })           
        });
```

&nbsp; A saida sera parecida com essa :

![Caption for the picture.](https://github.com/ziminny/paginate_ajax/blob/master/src/img/scr.png)
