<?php

require_once 'cnx.php';

Class DataTables{
    private $daw;
    private $start;
    private $length;
    private $search_key;
    private $order_column_index;
    private $order_column;
    private $list_columns;
    private $columns_prepared;
    private $query;
    private $filters;
    private $data;
    private $dict;
    private $cnx;
    private $db_slc;
    private $conditions;
    private $table;
    private $extra_data;
    function __construct($post, $list_columns, $config){
        $this->daw = $post['draw'];
        $this->start = $post['start'];
        $this->length = $post['length'];
        $this->search_key = $post['search']['value'];
        $this->order_column_index = $post['order'][0]['column'];
        $this->order_column = $post['order'][0]['dir'];
        $this->extra_data = $config['extra'];
        $this->list_columns = $list_columns;
        $this->filters = '';
        $this->data = array();
        $this->dict = array();
        $this->db_slc = $config['db_type'];
        $this->conditions = $config['conditions'] != ''?array('AND '.$config['conditions'], 'WHERE '. $config['conditions']):array('','');        
        $this->table = $config['table'];
        if($this->db_slc == 'sqlserver'){
            $this->cnx = new BDsql();
            foreach($list_columns as $col){
                if(strpos(strtoupper($this->table), 'INNER') != false){
                    $columns_prepared .= $col.",";
                }else{
                    $columns_prepared .= "[".$col."],";
                }
            }
            $this->columns_prepared = str_replace("DTCHANGENAME","AS",rtrim($columns_prepared, ","));
        }else{
            $this->cnx = (new Cnx())->connection();
            $this->columns_prepared = str_replace("DTCHANGENAME","AS",implode(',',$list_columns));
        }
        $this->query = 'SELECT '.$this->columns_prepared.' FROM ' . $config['table'] . ($config['conditions'] != ''?' WHERE '.$config['conditions']:'');
    }
    function process(){
        if($this->search_key){
            if(!strstr(strtoupper($this->query), 'WHERE') && $this->db_slc != 'sqlserver'){
                $this->filters = ' WHERE ';
            }else if(strstr(strtoupper($this->query), 'WHERE') && $this->db_slc != 'sqlserver'){
                $this->filters = ' AND ';
            }
            if($this->db_slc == 'sqlserver'){
                $this->filters = ' WHERE ';
            }
            foreach($this->list_columns as $cols){
                if(count(explode("DTCHANGENAME", $cols))>1){
                    [$column, $name] = explode("DTCHANGENAME", $cols);
                }else{
                    $column = $cols;
                }
                $this->filters .= $column . " LIKE '%" . $this->search_key . "%' OR ";
            }
            if($this->db_slc == 'sqlserver'){
                $this->cnx->query('SELECT '.$this->columns_prepared.' FROM '.$this->table.' '.rtrim($this->filters, " OR ") . ' ' . $this->conditions[0]);
                $this->data = $this->cnx->getResults();
            }else{
                $res = $this->cnx->query($this->query . rtrim($this->filters, " OR ") . ' LIMIT ' . '10');
            }
        }else{
            $normalize_list_columns = array();
            foreach($this->list_columns as $cols){
                $normalize_list_columns[] = str_replace("DTCHANGENAME", "AS", $cols);
            }
            $column_to_oder = $normalize_list_columns[$this->order_column_index];
            $order = $this->order_column == 'asc' ? $column_to_oder . ' ASC' : $column_to_oder . ' DESC';
            if($this->db_slc == 'sqlserver'){
                $endPagination = $this->start + $this->length;
                if(((int) $endPagination - 10) != 0){
                    $endPagination = ($this->start + $this->length) - 1;
                }
                $this->cnx->query("SELECT * FROM (SELECT TOP(SELECT COUNT(*) FROM ".$this->table." ".$this->conditions[1].") ROW_NUMBER() OVER (ORDER BY (SELECT 0)) AS [Count], ".$this->columns_prepared." FROM ".$this->table." ". $this->conditions[1]. " ORDER BY ".$order.") AS a WHERE ([Count] BETWEEN ".$this->start." AND ".$endPagination.")");
                $this->data = $this->cnx->getResults();
            }else{
                $res = $this->cnx->query($this->query . ' ORDER BY ' . $order . ' LIMIT ' . $this->length . ' OFFSET ' . $this->start);
            }
        }
        if(!empty($res) && $res->num_rows > 0 && $this->db_slc != 'sqlserver'){
            while($row = $res->fetch_assoc()){
                $this->data[] = $row;
            }
        }
        $this->returnData();
    }
    function returnData(){
        if($this->db_slc == 'sqlserver'){
            if($this->search_key){
                $this->cnx->query('SELECT '.$this->columns_prepared.' FROM '.$this->table.' '.rtrim($this->filters, " OR ").' '.$this->conditions[0]);
            }else{
                $this->cnx->query("SELECT COUNT(*) AS 'Count' FROM ".$this->table. " ". $this->conditions[1]);
            }
            $count_records = $this->cnx->getResults();
            $count_records = isset($count_records[0]['Count']) ? $count_records[0]['Count']: 0;
        }else{
            if($this->search_key){
                $result = $this->cnx->query($this->query . rtrim($this->filters, " OR ") . ' LIMIT ' . '10');
            }else{
                $result = $this->cnx->query($this->query);
            }
            $count_records = $result->num_rows;
        }
        $this->dict['recordsTotal'] = $count_records; 
        $this->dict['recordsFiltered'] = $count_records;
        $this->dict['draw'] = $this->daw;
        $this->dict['data'] = $this->data;
        $this->dict['extra'] = $this->extra_data;

        echo json_encode($this->dict, JSON_PRETTY_PRINT);
    }
}