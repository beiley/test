<?php
class Yummy_ActiveRecord_Base {
	/**
	* 存放model当前属性的数据
	* @var array
	*/
	protected $_data = array();
	protected static $_dba = null;

	/**
	* Model 类名
	* @var string
	*/
    protected $className;
	/**
	 * Model对应的Table名
	 * @var string
	 * @access protected
	 */
    protected $tableName;
	
	/**
	* Model表的主键名
	* @var string
	*/
	protected $primaryKey = 'id';
    /**
     * 用于生成ID的Sequence Name
     *
     * @var string
     */
    protected $sequenceName;
	protected $id = '';
	
    function __construct(){
		if(is_null($this->className)){
            $this->className = get_class($this);
        }
        if($this->tableName == null) {
            $table_name = self::class2table($this->className);
            $this->tableName = $table_name;
        }
        if(is_null($this->sequenceName)){
            $this->sequenceName = $this->tableName;
        }
    }
	public static function class2table($class){
		$t = array_pop(explode('_',$class));
		return strtolower($t);
	}
	
	/**
	* 根据ActiveRecord的属性值添加数据库表中相应的记录
	*
	* @return bool
	* @access private
	*/
    private function _createRecord(){
		$sql = "INSERT INTO ".$this->tableName;
        foreach ($this->_attributes as $k => $v) {
	        if(!isset($this->_data[$k]))continue;
	        $columns[] = $k;
	        $values[] = trim($this->_data[$k]);
	        $holders[] = '?';
        }
        $sql.= ' ('.implode(', ',$columns).') VALUES ('.implode(', ',$holders).') ';
        try{
            $db = self::getDba();
			$db->execute($sql,$values);
            Yummy_Object::debug('_insertid is:'.$this->getId());
        }catch(Exception $e){
            Yummy_Object::error("Create Record failed,DBA Error:".$e->getMessage(),__METHOD__);
			throw new Exception('Create record error:'.$e->getMessage());
        }
    }
	
	/**
	* 根据ActiveRecord的属性值更新数据库表中相应的记录
	*
	* @return bool
	* @access private
	*/
    private function _updateRecord(){
        $sql = "UPDATE ".$this->tableName;
        foreach ($this->_attributes as $k => $v) {
            if(!isset($this->_data[$k])) continue;
            $pairs[] = " $k = ?";
            $values[] = trim($this->_data[$k]);
        }
        $sql .= ' SET '.implode(', ',$pairs).' ';
        $sql .= " WHERE $this->primaryKey=? ";
        $values[] = $this->getId();
        try{
            self::getDba()->execute($sql,$values);
            Yummy_Object::debug("the sql is:".$sql,__METHOD__);
        }catch(Exception $e){
            Yummy_Object::error("Update record failed,DBA error:".$e->getMessage(),__METHOD__);
			throw new Exception('Update record error:'.$e->getMessage());
        }
    }
	/**
	 * return ActiveRecordBase
	 *
	 */
	final public function save(){
		if($this->getId()>0){
			$this->_updateRecord();
		}else{
	        //如果是新建记录则生成主键的sequence值
	        if(is_null( $this->getId())){
	            $this->setId($this->genId());
	        }
            //create record
			$this->_createRecord();
		}
    }
	
	/**
	* 立即从数据库中删除符合条件的记录而不先创建ActiveRecord对象(callback将不被触发)
	*
	* @param string $condition
	* @param array $vars
	* @return ActiveRecordBase
	*/
    public function deleteAll($condition=null,$vars=array()){
        $sql = "DELETE  FROM ".$this->tableName;
        if(!empty($condition)){
            $sql .= " WHERE $condition";
        }
        self::getDba()->execute($sql,$vars);
        return $this;
    }
	/**
	* 立即从数据库中删除指定id的记录而不先创建对象(该对象的callback将不被触发)
	* 
	* @param mixed $id
	* @return ActiveRecordBase
	*/
    public function delete($id){
        return $this->deleteAll($this->primaryKey." = '$id'");
    }
	
	/**
	* 数据库中是否存在符合条件的记录
	*
	* @param string $condition where clause
	* @param array $vars bind array
	* @return bool
	*/
    public function ifHas($condition,$vars=null){
        $count = $this->countIf($condition,$vars);
        return ($count>0);
    }
	
	/**
	* 查找匹配指定条件的记录的数量，如果没有匹配则返回0
	* @param string $condition
	* @param array $vars
	* @return int
	*/
    public function countIf($condition=null,$vars=null){
        $sql = 'SELECT COUNT(*) AS cnt FROM '.$this->tableName;
        if(!empty($condition)){
            $sql.=" WHERE $condition";
        }
        $row = self::getDba()->query($sql,1,1,$vars);
        return $row[0]['cnt'];
    }
	
	/**
	* 数据库中是否存在指定id的model对象
	*
	* @param mixed $id array of int or int
	* @return boolean
	*/
    public function has($id){
        if(is_array($id)){
            $condition = $this->primaryKey.' IN ('.$this->_createBindHolders(count($id)).')';
            $vars = $id;
        }else{
            $condition = $this->primaryKey."= ?";
            $vars=array($id);
        }
        $count = $this->countIf($condition,$vars);
        return ($count>0);
    }
	
	
	
	/**
	* 返回ActiveRecord使用的DBA实例
	*
	* @return Dba
	*/
	final public static function getDba(){
		if(is_null(self::$_dba)){
			self::$_dba = new Yummy_Dba();
		}
		return self::$_dba;
	}
	public function getTableName($tableName){
		$data = Yummy_Config::get("databases");
		$pre = $data["pre"];
		return $pre.$tableName;
	}
    public function getFiledName($filed){
        $data = Yummy_Config::get("databases");
        $pre = $data["pre"];
        return $pre.$filed;
    }
	/**
	*返回预编译的多个??????
	@param int $num
	@return string ???
	*/
	public function _createBindHolders($num){
		$holds= array_pad(array(),$num,'?');
        return implode(',',$holds);
	}
	
	/**
	* 设置当前ActiveRecord的属性值
	* @param  string $key
	* @param  mixed $value
	* @return ActiveRecordBase
	*/
	public function set($key,$value){
		//$this->_data[$key] = $this->$key = $value;
		$this->_data[$key] = $value;
		return $this;
	}
	/**
	* 获得当前ActiveRecord指定属性值
	*
	* @param string $key
	* @return mixed
	*/
	public function get($key){
		return isset($this->_data[$key])?$this->_data[$key]:null;
	}
	
	/**
	*
	* 通过直接指定SQL查找匹配的记录
	*
	* 这是一个底层的SQL查找方法,需要指定一个完整的sql语句，同时可以使用bindingVars。
	*
	* @param string $sql 执行查询的完整的SQL语句
	* @param boolean $readonly 对象是否只读
	* @param int $limit 每页记录数量，-1表示全部
	* @param int $page 分页的页索引(1-based)
	* @param array $vars 要传递的预编译参数数组，如果sql中使用了?这些占位符
	* @param boolean $raw 是否直接返回表记录数据，如果true,则不为这些数据创建对象
	* 
	* @return ActiveRecordBase
	*/
    public function findBySql($sql,$options=array()){
        $size=-1;
        $page=1;
        $vars=null;
        $_first=false;
        extract($options,EXTR_IF_EXISTS);
        try{
			$dba = self::getDba();
            $result = $dba->query($sql,$size,$page,$vars);
			//$result = $result['fields'];
            //hacking for findFirst,is it right?
            if($_first){
                $result = empty($result)?array():$result[0];
            }
        }catch(Exception $e){
            Yummy_Object::error("Dba Error:".$e->getMessage(),__METHOD__);
            throw new Exception("Find data failed:".$e->getMessage());
        }
        //created arrayobject     $this->_buildDataResult($result);
        return $result;
    }
	
	/**
	 * 查找记录，返回全部符合匹配条件的记录
	 *
	 * 本方法是支持各种find的核心操作。
	 * <p>
	 * 可以传递一个关联数组$options，用来说明查询的条件，options支持的选项key有:
	 * 
	 * condition: string,SQL查询的Where条件语句(不包括WHERE关键字)
	 * order: string,SQL的order语句，如 created_time DESC,name ASC,age ASC
	 * size: 一个整数，表示要分页时每页的记录数,-1表示不分页，返回全部
	 * page: 一个整数，表示返回页的索引号，如果设置了limit，则此参数默认为1
	 * select: string,默认情况下,select * FROM table,如果你希望用具体的字段限定来替换*,那么可以指定字段列表,如 'name,age'
	 * joins: string,SQL查询时需要附加的JOINS语句，比如"LEFT JOIN comments ON comments.post_id = id"
	 * groupby:string SQL GROUPBY条件
	 * vars: array,要传递的预编译参数数组，如果sql中使用了?这些占位符
	 * 
	 * @param array $options
	 * @return Doggy_ActiveRecord_Compat
	 * 
	 */
    public function find($options=array()){
        $sql = $this->_buildSqlByOptions($options);
        return $this->findBySql($sql,$options);
    }
	
	/**
	  * 根据选项构建SQL串
	  *
	  * @param array $options
	  * @return string
	  * @access protected
	  */
     protected function _buildSqlByOptions($options=array()){
        $condition=null;
        $order=null;
        $joins=null;
        $select=null;
        $groupby=null;
        extract($options,EXTR_IF_EXISTS);
        $sql ='SELECT ';
        $sql.=  $select? "$select ":'* ';
        $sql.= "FROM ".$this->tableName;
        
        if($joins){
         $sql .= " $joins ";
        }
        $sql.= !empty($condition)? " WHERE $condition ":'';
        $sql.= $groupby?" GROUP BY $groupby":'';
        $sql.= $order? " ORDER BY $order ":'';
        return trim($sql);
     }	
	
	
	
	
	/**
	 * 查找并返回匹配的第一条记录
	 *
	 * @param array $options condition or other options to find
	 * @return ActiveRecordBase
	 */
    public function findFirst($options=array()){
        $options['size']=1;
        $options['page']=1;
        $options['_first']=true;
        return $this->find($options);
    }
	/**
	 * 查找匹配指定ID的记录
	 *
	 * @param mixed $id  待查找的记录的id or id array 
	 * @param array $options  find options array,see #find
	 * @return ActiveRecordBase
	 */
    public function findById($id=null,$options=array(),$other=null){
        
        if(is_null($id))$id= $this->getId();
        
        if(is_array($id)){
            if(is_null($other)){
            	$options['condition'] = $this->primaryKey." in (".$this->_createBindHolders(count($id)).") ";
            }else{
            	$options['condition'] = $this->primaryKey." in (".$this->_createBindHolders(count($id)).") "." AND ".$other;
            }
            $options['vars'] = $id;
            return $this->find($options);
        }else{
            
            $options['condition']  = $this->primaryKey." = ? ";
            $options['vars'] = array($id);
            
            return $this->findFirst($options);
        }
    }
	/**
	* 返回ID值
	*
	* @return mixed
	*/
	public function getId(){
		return $this->get($this->primaryKey);
	}
    /**
     * 生成用于新Record的ID号
     *
     * 默认情况下，使用RecordActive的类名作为SequenceName，从DBA中
     * 返回一个Seq的当前值。
     * 你可以重写此方法来实现自己的ID生成策略
     *
     * @return int
     */
	public function genId(){
		return self::getDba()->genSeq($this->sequenceName);
	}
 }
?>