<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class MenuSetForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $menu_name;
	public $menu_code;
	public $display=1;
	public $z_index=0;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('study','ID'),
            'menu_code'=>Yii::t('study','menu code'),
            'menu_name'=>Yii::t('study','menu name'),
            'display'=>Yii::t('study','display'),
            'z_index'=>Yii::t('study','z_index'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, menu_code, menu_name, z_index, display','safe'),
			array('menu_code,menu_name','required'),
			array('menu_name','validateName'),
			array('menu_code','validateCode'),
			array('menu_code','validateDel','on'=>array("delete")),
            array('z_index', 'numerical', 'integerOnly'=>true),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $row = Yii::app()->db->createCommand()->select("id")->from("exa_setting")
            ->where('menu_name=:menu_name and id!=:id',
                array(':menu_name'=>$this->menu_name,':id'=>$id))->queryRow();
        if($row){
            $message = "菜单名称已存在，请重新命名";
            $this->addError($attribute,$message);
        }
    }
	public function validateCode($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $row = Yii::app()->db->createCommand()->select("id")->from("exa_setting")
            ->where('menu_code=:menu_code and id!=:id',
                array(':menu_code'=>$this->menu_code,':id'=>$id))->queryRow();
        if($row||in_array($this->menu_code,array("TP","SC","SS","EM"))){
            $message = "菜单编号已存在，请重新命名";
            $this->addError($attribute,$message);
        }
    }


    //刪除验证
	public function validateDel($attribute, $params){
        $row = Yii::app()->db->createCommand()->select()->from("exa_markedly")
            ->where('menu_id=:id', array(':id'=>$this->id))->queryRow();
        if ($row){
            $message = "该菜单已有测验单，无法删除";
            $this->addError($attribute,$message);
            return false;
        }
        $row = Yii::app()->db->createCommand()->select()->from("exa_chapter_class")
            ->where('menu_id=:id', array(':id'=>$this->id))->queryRow();
        if ($row){
            $message = "该菜单已有试题章节，无法删除";
            $this->addError($attribute,$message);
            return false;
        }
        $row = Yii::app()->db->createCommand()->select()->from("exa_study_class")
            ->where('menu_id=:id', array(':id'=>$this->id))->queryRow();
        if ($row){
            $message = "该菜单已有学习指南，无法删除";
            $this->addError($attribute,$message);
            return false;
        }
        return true;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select()->from("exa_setting")
            ->where("id=:id", array(':id'=>$index))->queryRow();
		if ($row)
		{
            $this->id = $row['id'];
            $this->menu_code = $row['menu_code'];
            $this->menu_name = $row['menu_name'];
            $this->display = $row['display'];
            $this->z_index = $row['z_index'];
		}
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveStaff($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			var_dump($e);
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveStaff(&$connection)
	{
		$sql = '';
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from exa_setting where id = :id ";
				break;
			case 'new':
				$sql = "insert into exa_setting(
							menu_code,menu_name, display, z_index, lcu
						) values (
							:menu_code,:menu_name, :display, :z_index, :lcu
						)";
				break;
			case 'edit':
				$sql = "update exa_setting set
							menu_code = :menu_code, 
							menu_name = :menu_name, 
							display = :display, 
							z_index = :z_index,  
							luu = :luu
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':menu_name')!==false)
			$command->bindParam(':menu_name',$this->menu_name,PDO::PARAM_STR);
		if (strpos($sql,':menu_code')!==false)
			$command->bindParam(':menu_code',$this->menu_code,PDO::PARAM_STR);
		if (strpos($sql,':display')!==false)
			$command->bindParam(':display',$this->display,PDO::PARAM_INT);
		if (strpos($sql,':z_index')!==false)
			$command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);

		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->setScenario("edit");
        }
        $this->saveMenuFile();
        return true;
	}

	private function saveMenuFile(){
	    $list = array();
	    $arr = array(
	        array("itemName"=>"Study guide","action"=>"StudyClass","num"=>"01"),//學習指南
	        array("itemName"=>"Mock test","action"=>"MockChapter","num"=>"02"),//模擬考試
            array("itemName"=>"Markedly test","action"=>"MarkedlyTest","num"=>"07"),//综合测验
	        array("itemName"=>"Study mutual","action"=>"Mutual","num"=>"03"),//學習互動
	        array("itemName"=>"Study mutual audit","action"=>"MutualAudit","num"=>"10"),//審核學習互動
	        array("itemName"=>"Paper Wrong","action"=>"PaperWrong","num"=>"04"),//错题集
	        array("itemName"=>"Paper My","action"=>"PaperMy","num"=>"05"),//我的测验单
	        array("itemName"=>"Conclude Paper","action"=>"concludePaper","num"=>"06"),//测验统计
	        array("itemName"=>"Conclude Staff","action"=>"concludeStaff","num"=>"08"),//员工统计
	        array("itemName"=>"Conclude Question","action"=>"concludeQuestion","num"=>"09"),//试题统计
	        array("itemName"=>"Video link hits","action"=>"videoHits","num"=>"11"),//链接点击量
        );
        $rows = Yii::app()->db->createCommand()->select("id,menu_name,menu_code")->from("exa_setting")
            ->where("display=1")->order("z_index asc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $items=array();
                foreach ($arr as $item){
                    $items[$item["itemName"]]=array(
                        "access"=>$row['menu_code'].$item['num'],
                        "url"=>"/{$item['action']}/index?index={$row['id']}&menu_code={$row['menu_code']}"
                    );
                }
                $list[$row["menu_name"]]=array(
                    'access'=>$row['menu_code'],
                    'icon'=>'fa-pencil',
                    'items'=>$items
                );
            }
        }
        $file=Yii::app()->basePath.'/config/menuExtra.php';
        $menuitems=array();
        if (file_exists($file)){
            $menuitems = require($file);
        }
        $file=Yii::app()->basePath.'/config/menu.php';
        $list = array_merge($list,$menuitems);
        $text='<?php return '.var_export($list,true).';';
        if(false!==fopen($file,'w+')){
            file_put_contents($file,$text);
        }else{
            var_dump("文件不存在");die();
        }
    }
}
