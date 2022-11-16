<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class MarkedlyTestForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $dis_name;
	public $name;
	public $start_time;
	public $end_time;
	public $exa_num;
	public $join_must;

	public $lcd;
	public $bumen;
    public $bumen_ex="全部";

    public $menu_code;
    public $menu_name;
    public $menu_id;
    public $code_pre="07";
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('study','ID'),
            'name'=>Yii::t('study','test name'),
            'dis_name'=>Yii::t('study','test display'),
            'exa_num'=>Yii::t('study','question num'),

            'lcd'=>Yii::t('study','Participate in time'),

            'join_must'=>Yii::t('study','Test Type'),
            'bumen_ex'=>Yii::t('study','Article all'),
            'bumen'=>Yii::t('study','Article all'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, menu_id, name, dis_name, exa_num, bumen, bumen_ex, join_must','safe'),
			array('menu_id,name,exa_num','required'),
            array('menu_id','validateID'),
			array('name','validateName'),
			array('exa_num','validateExaNum'),
            array('id','validateDel','on'=>array("delete")),
            array('exa_num', 'numerical', 'min'=>1, 'integerOnly'=>true),
		);
	}

    public function validateExaNum($attribute, $params){
        $sql = "";
        if(!empty($this->bumen)){
            $sql = " and b.id in (-1{$this->bumen}-2)";//$bumen=",1,2,3,"
        }
        $sum = Yii::app()->db->createCommand()
            ->select("count(a.id)")
            ->from("exa_chapter_title a")
            ->leftJoin("exa_chapter_class b","b.id=a.chapter_id")
            ->where("b.menu_id=:id and a.display=1 and b.display=1 {$sql}",array(':id'=>$this->menu_id))
            ->queryScalar();
        if($sum<$this->exa_num){
            $message = "试题随机数量不能大于题库总数：{$sum}";
            $this->addError($attribute,$message);
        }
    }

    public function validateID($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id,menu_code,menu_name")
            ->from("exa_setting")
            ->where('id=:id',array(':id'=>$this->menu_id))->queryRow();
        if(!$row){
            $message = "数据异常请刷新重试";
            $this->addError($attribute,$message);
        }else{
            $this->menu_id = $row["id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
        }
    }

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $row = Yii::app()->db->createCommand()->select("id")->from("exa_markedly")
            ->where('name=:name and id!=:id and menu_id=:menu_id',
                array(':name'=>$this->name,':id'=>$id,':menu_id'=>$this->menu_id))->queryRow();
        if($row){
            $message = "测验单名称已存在，请重新命名";
            $this->addError($attribute,$message);
        }
    }

    //刪除验证
	public function validateDel($attribute, $params){
        $row = Yii::app()->db->createCommand()->select()->from("exa_take")
            ->where('markedly_id=:markedly_id', array(':markedly_id'=>$this->id))->queryRow();
        if ($row){
            $message = "已有员工参加测验单无法删除";
            $this->addError($attribute,$message);
        }
    }

    //章节查詢
    public static function searchArticle($article,$menu_id){
        $suffix = Yii::app()->params['envSuffix'];
        $arr = array();
        $sql = "";
        if(!empty($article)){
            $sql.="and chapter_name like '%$article%' ";
        }
        $rows = Yii::app()->db->createCommand()->select("id,chapter_name")
            ->from("exa_chapter_class")
            ->where("menu_id=:id $sql",array(":id"=>$menu_id))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["chapter_name"];
            }
        }
        return $arr;
    }

    public function retrieveNewData($menu_id){
        $row = Yii::app()->db->createCommand()->select("id,menu_code,menu_name")
            ->from("exa_setting")
            ->where('id=:id',array(':id'=>$menu_id))->queryRow();
        if($row){
            $this->menu_id = $row["id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            return true;
        }
        return false;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("a.*,b.menu_code,b.menu_name")
            ->from("exa_markedly a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where("a.id=:id", array(':id'=>$index))->queryRow();
		if ($row){
            $this->id = $row['id'];
            $this->dis_name = $row['dis_name'];
            $this->name = $row['name'];
            $this->exa_num = $row['exa_num'];
            $this->join_must = $row['join_must'];
            $this->bumen = $row['bumen'];
            $this->bumen_ex = $row['bumen_ex'];

            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            return true;
		}
		return false;
	}

    public function saveData(){
        $uid = Yii::app()->user->id;
        switch ($this->getScenario()){
            case "new":
                Yii::app()->db->createCommand()->insert("exa_markedly", array(
                    'menu_id'=>$this->menu_id,
                    'name'=>$this->name,
                    'dis_name'=>$this->dis_name,
                    'exa_num'=>$this->exa_num,
                    'join_must'=>$this->join_must,
                    'bumen'=>$this->bumen,
                    'bumen_ex'=>$this->bumen_ex,
                    'display'=>1,
                    'take_sum'=>0,
                    'success_ratio'=>0,
                    'lcu'=>$uid,
                ));
                $this->id = Yii::app()->db->getLastInsertID();
                $this->changeJoinMust();
                break;
            case "edit":
                Yii::app()->db->createCommand()->update('exa_markedly', array(
                    'name'=>$this->name,
                    'dis_name'=>$this->dis_name,
                    'exa_num'=>$this->exa_num,
                    'join_must'=>$this->join_must,
                    'bumen'=>$this->bumen,
                    'bumen_ex'=>$this->bumen_ex,
                    'luu'=>$uid,
                ), "id={$this->id}");
                $this->changeJoinMust();
                break;
            case "delete":
                Yii::app()->db->createCommand()->delete('exa_markedly', 'id=:id', array(':id'=>$this->id));
                break;
            default:
                break;
        }
    }


    private function changeJoinMust(){
        if($this->join_must == 1){
            Yii::app()->db->createCommand()->update("exa_markedly",array("join_must"=>0),"id!=:id",array("id"=>$this->id));
        }
    }
}
