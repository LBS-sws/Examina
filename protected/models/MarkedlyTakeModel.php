<?php

class MarkedlyTakeModel extends CFormModel
{
    public $id;
    public $employee_id;
    public $employee_code;
    public $employee_name;
    public $menu_id;
    public $menu_name;
    public $menu_code;

    public $markedly_id;
    public $markedly_name;
    public $markedly_dis_name;
    public $join_must;//测验单类型

    public $exa_num;//试题总数
    public $bumen;//章节

    public $chapter_list=array();//所有試題
    public $paper_list=array();//試卷試題


    public $title_id_list=array();//試卷試題的所有试题id 例如：array(3,2,5,1);
    public $choose_id=array();//试题选项的顺序 例如：array(title_id=>'2,1,3,4');
    public $choose=array();//試卷試題的客户选择 例如：array(title_id=>1);

    private $updateTitleList = array();
    private $errorNum=0;
    private $successNum=0;

    protected $code_pre="07";

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('study','ID'),
            'markedly_name'=>Yii::t('study','test name'),
            'markedly_dis_name'=>Yii::t('study','test display'),
            'exa_num'=>Yii::t('study','question num'),
            'join_must'=>Yii::t('study','Test Type'),
            'employee_name'=>Yii::t('study','Test Employee'),

            'remark'=>Yii::t('study','Interpretation'),
            'name'=>Yii::t('study','question name'),
            'answer'=>Yii::t('study','correct answer'),
            'answer_a'=>Yii::t('study','wrong answer A'),
            'answer_b'=>Yii::t('study','wrong answer B'),
            'answer_c'=>Yii::t('study','wrong answer C'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            //array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, menu_id, markedly_id, title_id_list, choose_id, choose','safe'),
            array('markedly_id,title_id_list','required'),
            array('markedly_id','validateMarkedly'),
            array('title_id_list','validateTitle'),
        );
    }

    public function validateTitle($attribute, $params){
        $this->updateTitleList = array();
        $this->successNum = 0;
        $this->errorNum = 0;
        if(!empty($this->title_id_list)){
            if(count($this->title_id_list)!=$this->exa_num){
                $message = "试题数量异常，请刷新重试";
                $this->addError($attribute,$message);
                return false;
            }
            $session = Yii::app()->session;
            $code = isset($session['menu_code'])?$session['menu_code']:"dd";
            foreach ($this->title_id_list as $item){
                $row = Yii::app()->db->createCommand()->select("a.id,a.show_num,a.success_num")
                    ->from("exa_chapter_title a")
                    ->leftJoin("exa_setting b","a.menu_id=b.id")
                    ->where('a.id=:id and a.display=1 and b.menu_code=:code',
                        array(':id'=>$item,':code'=>$code)
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
                    if($okList!=$choose){//用戶做錯了題
                        $this->errorNum++;
                    }else{
                        $this->successNum++;
                        $row["success_num"]++;
                    }

                    $this->updateTitleList[]=array(
                        "id"=>$row["id"],
                        "show_num"=>$row["show_num"]+1,
                        "success_num"=>$row["success_num"],
                        "chooseStr"=>$chooseStr,
                        "choose"=>implode(",",$choose),
                        "success_bool"=>$okList!=$choose?0:1,//正確：1 錯誤：0
                    );
                }
            }
        }
    }

    public function validateMarkedly($attribute, $params){
        $row = Yii::app()->db->createCommand()
            ->select("a.id,a.menu_id,a.bumen,a.name,a.dis_name,a.exa_num,a.join_must,b.menu_name,b.menu_code")
            ->from("exa_markedly a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where('a.id=:id',array(':id'=>$this->markedly_id))->queryRow();
        if ($row){
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->markedly_name = $row["name"];
            $this->markedly_dis_name = $row["dis_name"];
            $this->exa_num = $row["exa_num"];
            $this->join_must = $row["join_must"];
            $this->bumen = $row["bumen"];
        }else{
            $message = "数据异常请刷新重试";
            $this->addError($attribute,$message);
        }
    }

    public function retrieveMarkedlyData($markedly_id){
        $row = Yii::app()->db->createCommand()
            ->select("a.menu_id,a.bumen,a.name,a.dis_name,a.exa_num,a.join_must,b.menu_name,b.menu_code")
            ->from("exa_markedly a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where('a.id=:id',array(':id'=>$markedly_id))->queryRow();
        if ($row){
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->markedly_id = $markedly_id;
            $this->markedly_name = $row["name"];
            $this->markedly_dis_name = $row["dis_name"];
            $this->exa_num = $row["exa_num"];
            $this->join_must = $row["join_must"];
            $this->bumen = $row["bumen"];

            $this->chapter_list=ChapterArticleModel::getBumenChapterList($this->menu_id,$this->bumen);
            $this->paper_list = ChapterArticleModel::resetRandomList($this->chapter_list,$this->exa_num);

            return true;
        }
        return false;
    }

    public function saveData(){
        $paperWrongModel = new PaperWrongForm();
        $paperWrongModel->employee_id = $this->employee_id;
        $uid = Yii::app()->user->id;
        $sum = count($this->title_id_list);
        Yii::app()->db->createCommand()->insert('exa_take', array(
            'menu_id'=>$this->menu_id,
            'markedly_id'=>$this->markedly_id,
            'employee_id'=>$this->employee_id,
            'title_num'=>$this->successNum,
            'title_sum'=>$sum,
            'just_bool'=>$this->join_must==1?1:0,
            'title_id_list'=>implode(",",$this->title_id_list),
            'success_ratio'=>$sum===0?0:round($this->successNum/$sum,2),
            'lcu'=>$uid
        ));
        $this->id = Yii::app()->db->getLastInsertID();
        foreach ($this->updateTitleList as $row){
            //保存試題
            Yii::app()->db->createCommand()->insert('exa_take_title', array(
                'take_id'=>$this->id,
                'employee_id'=>$this->employee_id,
                'title_id'=>$row["id"],
                'choose_id'=>$row["choose"],//用戶選擇的選項(多選用逗號分割)
                'list_choose'=>$row["chooseStr"],//選項順序
                'is_correct'=>$row["success_bool"],//1：回答正確 0：回答錯誤
                'lcu'=>$uid
            ));
            if($row["success_bool"]!=1){ //回答错误，需要记录到错误集
                $row["menu_id"] = $this->menu_id;
                $paperWrongModel->saveWrongData($row,$row["choose"],$row["chooseStr"],$this->id);
            }
            //修改試題的出現次數
            Yii::app()->db->createCommand()->update('exa_chapter_title', array(
                'show_num'=>$row["show_num"],
                'success_num'=>$row["success_num"]
            ), "id={$row["id"]}");
        }
    }

    public static function validateEmployee($model){
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()->select("b.id,b.code,b.name")
            ->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.user_id ='$uid'")->queryRow();
        if($rs){
            $model->employee_id=$rs["id"];
            $model->employee_code=$rs["code"];
            $model->employee_name=$rs["name"];
            return true;
        }else{
            return false;
        }
    }
}
