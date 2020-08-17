<?php

namespace Ziminny\Paginate\db;

trait TraitInitConfig {

    private function  initConfigs($datas) {

        $array['pages']   = filter_var($_POST[$datas['posts']['pages']],FILTER_SANITIZE_NUMBER_INT);
        $array['input']   = filter_var($_POST[$datas['posts']['input']],FILTER_SANITIZE_STRING);
        $array['table']   = $datas['table'];
        $array['orderBy'] = $datas['orderBy'];
        $array['where']   = $datas['where'];
        $array['columns'] = $datas['columns'];
        $array['viewsCount'] = $datas['viewsCount'];
        $array['innerJoin'] = $datas['innerJoin'];
    
        // $array['columns'] = str_replace(" " , "",$array['collumns']);
        // $columnsArray = explode(",",$array['columns']);
    
        if(isset($datas['personalizeFields'])) :
            $array['columnsTable'] = $datas['personalizeFields'];
        endif;
    
    
        return (object) $array;
    }


}