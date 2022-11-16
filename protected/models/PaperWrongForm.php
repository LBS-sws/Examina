<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class PaperWrongForm extends CFormModel
{
	/* User Fields */
	public $id = 0;

    public $employee_id;
    public $employee_code;
    public $employee_name;

	public $wrong_num;
	public $wrong_date;
	public $wrong_type;//0:模擬練習 1:綜合測驗 2：錯題糾正
	public $take_id;
	public $list_choose;
	public $choose_id;
    public $title_id;

    public $menu_code;
    public $menu_name;
    public $menu_id;
    public $code_pre="04";

    public $paper_list=array();//試卷試題

    public $error_num=0;//错题总数量
    public $title_id_list=array();//試卷試題的所有试题id 例如：array(3,2,5,1);
    public $choose;

    private $deleteErrorList = array();
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('study','ID'),
            'employee_code'=>Yii::t('study','Correction of staff'),
            'employee_name'=>Yii::t('study','Test Employee'),
            'wrong_num'=>Yii::t('study','wrong number'),
            'wrong_date'=>Yii::t('study','wrong date'),
            'wrong_type'=>Yii::t('study','wrong root'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, menu_id, title_id_list, choose_id, choose','safe'),
            array('title_id_list','required'),
            array('menu_id','validateMenuId'),
            array('title_id_list','validateTitle'),
		);
	}

    public function validateTitle($attribute, $params){
        $this->deleteErrorList = array();
        if(!empty($this->title_id_list)){
            $this->error_num = Yii::app()->db->createCommand()->select("count(a.id)")
                ->from("exa_wrong_title a")
                ->where('a.menu_id=:menu_id and a.employee_id=:employee_id',
                    array(':menu_id'=>$this->menu_id,':employee_id'=>$this->employee_id)
                )->queryScalar();
            if(count($this->title_id_list)!=$this->error_num){
                $message = "试题数量异常，请刷新重试";
                $this->addError($attribute,$message);
                return false;
            }
            $session = Yii::app()->session;
            $code = isset($session['menu_code'])?$session['menu_code']:"dd";
            foreach ($this->title_id_list as $item){
                $row = Yii::app()->db->createCommand()->select("a.id,a.wrong_num")
                    ->from("exa_wrong_title a")
                    ->leftJoin("exa_setting b","a.menu_id=b.id")
                    ->where('a.title_id=:title_id and b.menu_code=:code and a.employee_id=:employee_id',
                        array(':title_id'=>$item,':code'=>$code,':employee_id'=>$this->employee_id)
                    )->queryRow();
                if(!$row){
                    $message = "试题不存在，请刷新重试";
                    $this->addError($attribute,$message);
                    return false;
                }else{
                    //总共有的选项
                    $chooseStr = key_exists($item,$this->choose_id)?$this->choose_id[$item]:0;
                    $chooseList = explode(",",$chooseStr);
                    sort($chooseList);
                    //用户的选择
                    $choose = key_exists($item,$this->choose)?$this->choose[$item]:array();
                    sort($choose);
                    $chooseRows = Yii::app()->db->createCommand()->select("a.id,a.judge")
                        ->from("exa_chapter_title_choose a")
                        ->where('a.title_id=:id and a.display=1',
                            array(':id'=>$item)
                        )->order("a.id asc")->queryAll();
                    $okList = array();//所有正確答案
                    $okArr = array();//所有選項
                    if($chooseRows){
                        foreach ($chooseRows as $chooserRow){
                            $okArr[]=$chooserRow["id"];
                            if($chooserRow["judge"]==1){
                                $okList[]=$chooserRow["id"];
                            }
                        }
                    }
                    if($okArr!=$chooseList){
                        $message = "選項不存在，请刷新重试";
                        $this->addError($attribute,$message);
                        return false;
                    }
                    if($okList==$choose){//用戶做对了
                        $this->deleteErrorList[]=array(
                            "id"=>$row["id"],
                            "success"=>true
                        );
                    }else{
                        $this->deleteErrorList[]=array(
                            "id"=>$row["id"],
                            "success"=>false,
                            "wrong_num"=>$row["wrong_num"]+1,
                            'choose_id'=>implode(",",$choose),//用戶選擇的選項(多選用逗號分割)
                            'list_choose'=>$chooseStr,//選項順序
                            'wrong_date'=>date("Y-m-d H:i:s"),//错误时间
                        );
                    }
                }
            }
        }
    }

    public function validateMenuId($attribute, $params){
        $row = Yii::app()->db->createCommand()
            ->select("menu_code,menu_name")
            ->from("exa_setting")
            ->where("id=:id", array(':id'=>$this->menu_id))->queryRow();
        if ($row){
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
        }else{
            $message = "数据异常请刷新重试";
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()
            ->select("a.*,b.menu_code,b.menu_name")
            ->from("exa_wrong_title a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where("a.id=:id and a.display=1 and a.employee_id={$this->employee_id}", array(':id'=>$index))->queryRow();
		if ($row){
            $this->id = $row['id'];
            $this->title_id = $row["title_id"];
            $this->choose_id = $row["choose_id"];
            $this->list_choose = $row["list_choose"];
            $this->wrong_num = $row["wrong_num"];
            $this->wrong_date = $row["wrong_date"];
            $this->wrong_type = $row["wrong_type"];
            $this->take_id = $row["take_id"];
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;

            $this->paper_list = $this->setPaPerList();
            return true;
		}
		return false;
	}

	public function retrieveErrorData($menu_id)
	{
        $row = Yii::app()->db->createCommand()
            ->select("menu_code,menu_name")
            ->from("exa_setting")
            ->where("id=:id", array(':id'=>$menu_id))->queryRow();
        if($row){
            $this->menu_id = $menu_id;
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;

            $list = $this->getErrorListForMenuId();
            $this->paper_list = ChapterArticleModel::resetRandomList($list,count($list));
            return true;
        }
		return false;
	}

    //獲取所有错误试题
    private function getErrorListForMenuId(){
        $list = array();
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.title_type,a.title_code,a.name,a.remark,a.chapter_id")
            ->from("exa_wrong_title b")
            ->leftJoin("exa_chapter_title a","a.id=b.title_id")
            ->where("b.menu_id=:menu_id and b.employee_id={$this->employee_id}",array(':menu_id'=>$this->menu_id))
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["id"]] = $row;
                $chooseList = Yii::app()->db->createCommand()
                    ->select("id,choose_name,judge")
                    ->from("exa_chapter_title_choose")
                    ->where('title_id=:id and display=1',array(':id'=>$row["id"]))->queryAll();
                if($chooseList){
                    foreach ($chooseList as $choose){
                        $list[$row["id"]]["chooseList"][$choose["id"]] = $choose;
                    }
                }
            }
        }
        return $list;
    }

    //獲取試題
    private function setPaPerList(){
        $list = array();
        $row = Yii::app()->db->createCommand()
            ->select("a.id,a.title_type,a.title_code,a.name,a.remark,a.chapter_id")
            ->from("exa_chapter_title a")
            ->where("a.id=:id",array(':id'=>$this->title_id))
            ->queryRow();
        if($row){
            $row['is_correct'] = 0;
            $row['choose_id'] = $this->choose_id;
            $row['list_choose'] = $this->list_choose;
            $orderId = explode(",",$row['list_choose']);
            $i=1;
            $orderSql = "ORDER BY case id ";
            foreach ($orderId as $id){
                $orderSql.=" when {$id} then {$i} ";
                $i++;
            }
            $orderSql.="end asc";
            $list[$row["id"]] = $row;
            $chooseList = Yii::app()->db->createCommand()
                ->select("id,choose_name,judge")
                ->from("exa_chapter_title_choose")
                ->where("id in ({$row['list_choose']}) {$orderSql}")
                ->queryAll();
            if($chooseList){
                foreach ($chooseList as $choose){
                    $list["{$row["id"]}"]["chooseList"]["{$choose["id"]}"] = $choose;
                }
            }
        }
        return $list;
    }

    //获取测验单名称(简称)
    public static function getMarkedlyName($take_id){
        $row = Yii::app()->db->createCommand()->select("b.name")
            ->from("exa_take a")
            ->leftJoin("exa_markedly b","a.markedly_id=b.id")
            ->where('a.id=:id',array(':id'=>$take_id))->queryRow();
        if($row){
            return $row["name"];
        }else{
            return $take_id;
        }
    }

	public function addWrongTitle($menu_id,$title_id,$choose_id,$list_choose,$wrong_type=0){
        $uid = Yii::app()->user->id;
        $row = Yii::app()->db->createCommand()->select("a.id,a.menu_id,a.show_num,a.success_num")
            ->from("exa_chapter_title a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where('a.id=:id and a.display=1 and a.menu_id=:menu_id',
                array(':id'=>$title_id,':menu_id'=>$menu_id)
            )->queryRow();
        if(!$row){//试题不存在
            return array("status"=>0,"message"=>"试题不存在3");;
        }else{
            //总共有的选项
            $chooseList = is_array($list_choose)?$list_choose:array($list_choose);
            sort($chooseList);
            //用户的选择
            $choose = is_array($choose_id)?$choose_id:array($choose_id);
            sort($choose);
            $chooseRows = Yii::app()->db->createCommand()->select("a.id,a.judge")
                ->from("exa_chapter_title_choose a")
                ->where('a.title_id=:id and a.display=1',
                    array(':id'=>$title_id)
                )->order("a.id asc")->queryAll();
            $okList = array();//所有正確答案
            $okArr = array();//所有選項
            if($chooseRows){
                foreach ($chooseRows as $chooserRow){
                    $okArr[]=$chooserRow["id"];
                    if($chooserRow["judge"]==1){
                        $okList[]=$chooserRow["id"];
                    }
                }
            }
            if($okArr!=$chooseList){//選項不存在
                return array("status"=>0,"message"=>"選項不存在2");
            }
            if($okList!=$choose){//用戶做錯了題
                $choose = implode(",",$choose);
                $chooseList = implode(",",$chooseList);
                $this->saveWrongData($row,$choose,$chooseList,$wrong_type);
                return array("status"=>1,"message"=>"录入成功");
            }else{
                return array("status"=>0,"message"=>"答对了，不需要保存1");
            }
        }
    }

    public function saveWrongData($titleRow,$choose_id,$list_choose,$wrong_type=0){
	    $uid = Yii::app()->user->id;
        $bool = Yii::app()->db->createCommand()->select("id,wrong_num")
            ->from("exa_wrong_title")
            ->where('title_id=:title_id and employee_id=:employee_id',
                array(':title_id'=>$titleRow["id"],':employee_id'=>$this->employee_id)
            )->queryRow();
        if($bool){
            $wrongNum = $bool["wrong_num"]+1;
            Yii::app()->db->createCommand()->update('exa_wrong_title', array(
                'choose_id'=>$choose_id,//用戶選擇的選項(多選用逗號分割)
                'list_choose'=>$list_choose,//選項順序
                'wrong_date'=>date("Y-m-d H:i:s"),//错误时间
                'wrong_type'=>empty($wrong_type)?0:1,//错误来源 0:模擬練習 1:綜合測驗 2：錯題糾正
                'take_id'=>$wrong_type,//错误来源 0:章节练习 int:综合测试id
                'display'=>1,
                'wrong_num'=>$wrongNum,
                'luu'=>$uid
            ),"id={$bool["id"]}");
        }else{
            Yii::app()->db->createCommand()->insert('exa_wrong_title', array(
                'menu_id'=>$titleRow["menu_id"],
                'employee_id'=>$this->employee_id,
                'title_id'=>$titleRow["id"],
                'choose_id'=>$choose_id,//用戶選擇的選項(多選用逗號分割)
                'list_choose'=>$list_choose,//選項順序
                'wrong_date'=>date("Y-m-d H:i:s"),//错误时间
                'wrong_type'=>empty($wrong_type)?0:1,//错误来源 0:模擬練習 1:綜合測驗 2：錯題糾正
                'take_id'=>$wrong_type,//错误来源 0:章节练习 int:综合测试id
                'lcu'=>$uid
            ));
        }
    }

    //错题纠正的保存
    public function saveData(){
        if(!empty($this->deleteErrorList)){
            $uid = Yii::app()->user->id;
            foreach ($this->deleteErrorList as $row){
                if($row["success"]){
                    Yii::app()->db->createCommand()->update('exa_wrong_title', array(
                        'display'=>0,
                        'luu'=>$uid
                    ),"id={$row["id"]}");
                }else{
                    Yii::app()->db->createCommand()->update('exa_wrong_title', array(
                        'choose_id'=>$row["choose_id"],//用戶選擇的選項(多選用逗號分割)
                        'list_choose'=>$row["list_choose"],//選項順序
                        'wrong_date'=>$row["wrong_date"],//错误时间
                        'wrong_num'=>$row["wrong_num"],//错误次数
                        'wrong_type'=>2,//错误来源 0:模擬練習 1:綜合測驗 2：錯題糾正
                        'take_id'=>0,
                        'display'=>1,
                        'luu'=>$uid
                    ),"id={$row["id"]}");
                }
            }
        }
    }
}
