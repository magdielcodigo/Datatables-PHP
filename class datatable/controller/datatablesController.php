<?php
require_once '../helper/Datatables.php';

class DatatablesController{
    function set(){
        $query = 'SELECT * FROM datos_test';
        $list_columns = array('id', 'nombre', 'edad', 'telefono');
        return (new DataTables($_POST, $query, $list_columns))->process();
    }
}

echo (new DatatablesController())->set();