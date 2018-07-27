<?php
class Examina{

    public $_quizId;//測驗單id
    //public $_typeId;//類別id (後期刪除)
    public $_bool;//是否隨機出試題 true:是

    public $_testNum;//測驗單題目數量

    public $_testList=array();//題庫列表
    public $_errorList=array();//員工錯誤列表
    public $_resultList=array();//輸出列表

    public $_quizList=array();//測驗單信息

    private $_command;
    private $_errorBool = false;//是否有異常 true：數據異常

    public function __construct($index,$bool=true,$join_id=""){
        $this->_quizId = $index;
        $this->_bool = $bool;
        $staff_id = Yii::app()->user->staff_id();//當前員工
        if(empty($staff_id)){
            $this->_errorBool = true;
            return true;
        }
        $command = Yii::app()->db->createCommand();
        //$this->_command->reset();
        $rows = $command->select()->from("exa_quiz")
            ->where("id=:id", array(':id'=>$index))->queryRow();
        if($rows){
            $this->_quizList = $rows;
            $this->_testNum = $rows["exa_num"];
            //$this->_typeId = $rows["type_id"];
            if($bool){
                $this->roundList();
            }else{
                //獲取已經存在的試題列表
                $command->reset();
                $rows = $command->select("a.*,b.title_code,b.name,b.remark")->from("exa_examina a")
                    ->leftJoin("exa_title b","a.title_id=b.id")
                    ->where("a.join_id=:join_id and a.employee_id=:employee_id", array(':join_id'=>$join_id,':employee_id'=>$staff_id))->queryAll();
                if($rows){
                    shuffle($rows);//打亂
                    foreach ($rows as $row){
                        $list = $row;
                        $chooseList = explode(",",$list["list_choose"]);
                        shuffle($chooseList);//打亂
                        foreach ($chooseList as $choose){
                            $list["list"][] = $this->getChooseList($command,$choose);
                        }
                        $this->_resultList[] =$list;
                    }
                }else{
                    $this->_errorBool = true;
                }
            }
        }else{
            $this->_errorBool = true;
        }
    }

    public function roundList(){
        $staff_id = Yii::app()->user->staff_id();//當前員工
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $rows = $command->select()->from("exa_title")->where("quiz_id=:id",array(":id"=>$this->_quizId))->queryAll();
        if($rows){
            $this->_testNum = $this->_testNum>count($rows)?count($rows):$this->_testNum; //測驗單題目數量不能大於試題數量
            foreach ($rows as $row){
                $this->_testList[$row['id']] = $row;
            }
            $command->reset();

            //由於測驗單只能進行一次，且試題為單個綁定測驗單，所以試題錯誤列表只能為模擬測驗提供
            $rows = $command->select("a.title_id")->from("exa_examina a")
                ->leftJoin("exa_title_choose b","a.choose_id=b.id")
                ->leftJoin("exa_title c","a.title_id=c.id")
                ->where("a.employee_id=:employee_id and b.judge=0 and c.quiz_id=:quiz_id",
                    array(':employee_id'=>$staff_id,':quiz_id'=>$this->_quizId))->queryAll();
            if($rows){
                $this->_errorList = array_column($rows,"title_id","title_id");
            }

            //$this->_resultList =  array_merge(array_column($this->_testList,"id"),$this->_errorList);
            $this->setResultList($command);
        }else{
            $this->_errorBool = true;
        }
    }

    private function setResultList(&$command){
        $testList = array_column($this->_testList,"id","id");
        $errorList = $this->_errorList;
        $resultList = array();
        $sessionList = array();
        $session = Yii::app()->session;

        for ($i=0;$i<$this->_testNum;$i++){
            $list = array_merge($testList,$errorList);
            $randNum = array_rand($list,1);
            $randNum = $list[$randNum];
            unset($testList[$randNum]);
            if(key_exists($randNum,$errorList)){
                unset($errorList[$randNum]);
            }
            //$randNum = intval($randNum);
            $result = $this->_testList[$randNum];
            $result["list"]=$this->getRandChooseList($command,$result["id"]);
            $sessionList[]=$result["list"];
            $resultList[]=$result;
        }
        $this->_resultList = $resultList;
        $session["examina_list"] = $sessionList;
    }

    private function getRandChooseList(&$command,$title_id){
        $command->reset();
        $rows = $command->select("*")->from("exa_title_choose")
            ->where("title_id=:title_id", array(':title_id'=>$title_id))->queryAll();
        if($rows){
            shuffle($rows);//打亂
            return $rows;
        }else{
            return array(
                array("id"=>"","choose_name"=>"數據丟失","judge"=>0),
                array("id"=>"","choose_name"=>"數據丟失","judge"=>0),
                array("id"=>"","choose_name"=>"數據丟失","judge"=>0),
                array("id"=>"","choose_name"=>"數據丟失","judge"=>0)
            );
        }
    }

    private function getChooseList(&$command,$id){
        $command->reset();
        $rows = $command->select("*")->from("exa_title_choose")
            ->where("id=:id", array(':id'=>$id))->queryRow();
        if($rows){
            return $rows;
        }else{
            return array("id"=>"","choose_name"=>"數據丟失","judge"=>0);
        }
    }

    //驗證測驗單的時間是否過期
    public function validateTime(){
        $date = date("Y-m-d");
        $quizList = $this->_quizList;
        $startDate = date("Y-m-d",strtotime($quizList["start_time"]));
        $endDate = date("Y-m-d",strtotime($quizList["end_time"]));
        if($date<=$startDate||$date>=$endDate){
            return false;
        }else{
            return true;
        }
    }

    public function getResultList(){
        return $this->_resultList;
    }

    public function getQuizList(){
        return $this->_quizList;
    }

    public function getErrorBool(){
        return $this->_errorBool;
    }
}
