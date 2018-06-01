<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class CategoryForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $bumen;
	public $name;
	public $bumen_ex="全部";
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('examina','ID'),
            'bumen_ex'=>Yii::t('examina','department'),
            'name'=>Yii::t('examina','category name'),
            'bumen'=>Yii::t('examina','department'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, bumen, name, bumen_ex','safe'),
			array('name','required'),
			array('name','validateName'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("exa_type")
            ->where('name=:name and id!=:id',
                array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('examina','category name'). Yii::t('examina',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

    //刪除验证
	public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("exa_title")
            ->where('type_id=:type_id', array(':type_id'=>$this->id))->queryAll();
        if ($rows){
            return false;
        }
        return true;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select()->from("exa_type")
            ->where("id=:id", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->name = $row['name'];
				$this->bumen = $row['bumen'];
                $this->bumen_ex = $row['bumen_ex'];
				break;
			}
		}
		return true;
	}

    //獲取類別名稱
	public function getCategoryNameToId($id)
	{
        $rows = Yii::app()->db->createCommand()->select("name")->from("exa_type")
            ->where("id=:id", array(':id'=>$id))->queryRow();
        if($rows){
            return $rows["name"];
        }
		return $id;
	}

    //獲取類別列表
	public function getCategoryList()
	{
	    $arr= array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("id,name")->from("exa_type")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
		return $arr;
	}

    //獲取類別列表(僅自己部門)
	public function getCategoryListOnly()
	{
        $bumen = Yii::app()->user->bumen();
	    $arr= array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("id,name")->from("exa_type")->where("bumen LIKE '%,$bumen,%' or bumen=''")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
		return $arr;
	}

    //所有城市列表
	public function getAllCityList(){
        $suffix = Yii::app()->params['envSuffix'];
	    $arr = array(""=>"全部");
        $rows = Yii::app()->db->createCommand()->select()->from("security$suffix.sec_city")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["code"]] = $row["name"];
            }
        }
	    return $arr;
    }

    //部門查詢
	public function searchDepartment($city,$department){
        $suffix = Yii::app()->params['envSuffix'];
	    $arr = array();
        $sql = "";
        if(!empty($city)){
            $sql.="and city='$city' ";
        }
        if(!empty($department)){
            $sql.="and name like '%$department%' ";
        }
        $rows = Yii::app()->db->createCommand()->select()->from("hr$suffix.hr_dept")
            ->where("type = 0 $sql")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
	    return $arr;
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
                $sql = "delete from exa_type where id = :id ";
				break;
			case 'new':
				$sql = "insert into exa_type(
							bumen_ex, name, bumen
						) values (
							:bumen_ex, :name, :bumen
						)";
				break;
			case 'edit':
				$sql = "update exa_type set
							bumen_ex = :bumen_ex, 
							name = :name, 
							bumen = :bumen
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':bumen_ex')!==false)
			$command->bindParam(':bumen_ex',$this->bumen_ex,PDO::PARAM_STR);
		if (strpos($sql,':bumen')!==false)
			$command->bindParam(':bumen',$this->bumen,PDO::PARAM_STR);
		if (strpos($sql,':name')!==false)
			$command->bindParam(':name',$this->name,PDO::PARAM_STR);
		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
        return true;
	}
}
