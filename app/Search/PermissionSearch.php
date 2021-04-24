<?php
namespace App\Search;
use App\Models\Permission;

class PermissionSearch{
    use Property;


    const SORT_ASC = 1;
    const SORT_DESC = -1;

    private $sort_map = [
        self::SORT_ASC=>'asc', 
        self::SORT_DESC=>'desc' 
    ];

    private $_page = 1;
    private $_page_size = 10;
    private $_order_by = 'created_at';
    private $_sort = self::SORT_DESC;
    private $_id;
    private $_name;
    private $_status;
    private $_query;
    private $_total;
    private $_pages;
    private $_list;

    public function setPage($val){
        $this->_page = $val; 
    }

    public function setPage_size($val){
        $this->_page_size = $val; 
    }

    public function setOrder_by($val){
        $allowed = ['created_at','id'];
        if(!in_array($val,$allowed)){
            throw new \Exception('not allowed order by field',101); 
        }
        $this->_order_by = $val; 
    }

    public function setSort($val){
        $allowed = [self::SORT_DESC,self::SORT_ASC];
        if(!in_array($val,$allowed)){
            throw new \Exception('not allowed sort field',101); 
        }
        $this->_sort = $val; 
    }

    public function setStatus($val){
        $this->_query = $this->_query->where('status','=',$val);
        $this->_status = $val; 
    }

    public function setId($val){
        $this->_query = $this->_query->where('id','=',$val);
        $this->_id = $val; 
    }

    public function search($params){
        $this->_query = new Permission(); 
        foreach($params as $k=>$v){
            $this->$k = $v; 
        }
        $offset = $this->_page_size*($this->_page - 1);
        $this->_total = $this->_query->count();
        $this->_query = $this->_query->skip($offset)
             ->take($this->_page_size)
             ->orderBy($this->_order_by,$this->sort_map[$this->_sort]);
        $this->_list = $this->_query->get();
        $this->_pages = ceil($this->_total/$this->_page_size);
        return $this->toArray();
    }

    public function one($params){
        $this->_query = new Permission(); 
        foreach($params as $k=>$v){
            $this->$k = $v; 
        }
        $data = $this->_query->first();
        return $data;
    }


    public function getTotal(){
        return $this->_total; 
    }

    public function getPages(){
        return $this->_pages; 
    }

    public function getPage(){
        return $this->_page; 
    }

    public function getPage_size(){
        return $this->_page_size; 
    }

    public function getList(){
        return $this->_list; 
    }

    public function toArray(){
        return [
            'page'=>$this->page, 
            'pages'=>$this->pages, 
            'page_size'=>$this->page_size, 
            'total'=>$this->total, 
            'list'=>$this->list, 
        ]; 
    }

}
