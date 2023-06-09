<?php
require_once '../helper/Datatables.php';

class DatatablesController{
    function set(){
        $list_columns = array('id', 'nombre', 'edad', 'telefono');
        $config = array('db_type'=>'mysql',
                        'table'=>'datos_test',
                        'conditions'=>'',
                        'extra'=>array());
        return (new DataTables($_POST, $list_columns, $config))->process();
    }
}

echo (new DatatablesController())->set();