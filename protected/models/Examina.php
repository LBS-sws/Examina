<?php
class Examina{

    public $_quizId;//測驗單id
    public $_joinId;
    //public $_typeId;//類別id (後期刪除)
    public $_bool;//是否隨機出試題 true:是

    public $_testNum;//測驗單題目數量

    public $_testList=array();//題庫列表
    public $_errorList=array();//員工錯誤列表
    public $_successList=array();//員工正確列表
    public $_resultList=array();//輸出列表

    public $_quizList=array();//測驗單信息

    private $_command;
    private $_errorBool = false;//是否有異常 true：數據異常

    public function __construct($index,$bool=true,$join_id=""){
        $this->_joinId = $join_id;
        $this->_quizId = $index;
        $this->_bool = $bool;
        $staff_id = Yii::app()->user->staff_id();//當前員工
        if(empty($staff_id)){
            $this->_errorBool = true;
            return true;
        }
        $command = Yii::app()->db->createCommand();
        if(empty($this->_joinId)){//最後一次參與測試的id
            $this->_joinId = $command->select("max(id)")->from("exa_join")
                ->where("employee_id=:id and quiz_id=:quiz_id", array(':id'=>$staff_id,':quiz_id'=>$index))->queryScalar();
            $this->_joinId = $this->_joinId?$this->_joinId:"";
            $command->reset();
        }
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
                //var_dump($rows);die();
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
        $rows = $command->select()->from("exa_title")->where("quiz_id=:id and show_int=1",array(":id"=>$this->_quizId))->queryAll();
        if($rows){
            $this->_testNum = $this->_testNum>count($rows)?count($rows):$this->_testNum; //測驗單題目數量不能大於試題數量
            foreach ($rows as $row){
                $this->_testList[$row['id']] = $row;
            }
            $command->reset();

            //最後一次測驗單的試題
            $exprSql = empty($this->_joinId)?"":" and a.join_id='{$this->_joinId}'";
            $rows = $command->select("a.title_id,b.judge")->from("exa_examina a")
                ->leftJoin("exa_title_choose b","a.choose_id=b.id")
                ->leftJoin("exa_title c","a.title_id=c.id")
                ->where("a.employee_id=:employee_id and c.quiz_id=:quiz_id $exprSql",
                    array(':employee_id'=>$staff_id,':quiz_id'=>$this->_quizId))->queryAll();
            if($rows){
                foreach ($rows as $row){
                    if($row["judge"]==1){
                        $this->_successList[$row["title_id"]] = $row["title_id"];
                    }else{
                        $this->_errorList[$row["title_id"]] = $row["title_id"];
                    }
                }
            }

            //$this->_resultList =  array_merge(array_column($this->_testList,"id"),$this->_errorList);
            $this->setResultList($command);
        }else{
            $this->_errorBool = true;
        }
    }

    //錯誤試題百分百出現，然後選擇沒參與過的試題，最後從成功試題中隨機
    private function getRandListForKey(){
        $testList = array_column($this->_testList,"id","id");
        $errorList = $this->_errorList;
        $successList = $this->_successList;
        if(count($errorList)>=$this->_testNum){
            $list = array_rand($errorList,$this->_testNum);
            $list = $this->_testNum==1?array($list):$list;
        }else{
            $list = empty($errorList)?array():array_values($errorList);
            $exprList = array();//上次測驗沒抽中的試題
            foreach ($testList as $item){
                if(!in_array($item,$errorList)&&!in_array($item,$successList)){
                    $exprList[$item] = $item;
                }
            }
            $num = $this->_testNum-count($errorList);
            if($num<=count($exprList)){
                $exprList = array_rand($exprList,$num);
                $exprList = $num==1?array($exprList):$exprList;
                $list = array_merge($list,$exprList);
            }else{
                $num-=count($exprList);
                $list = array_merge($list,$exprList);//補上沒抽中試題
                $exprList = array_rand($successList,$num);//從成功試題中隨機最後幾個試題
                $exprList = $num==1?array($exprList):$exprList;
                $list = array_merge($list,$exprList);
            }
        }
        shuffle($list);
        return $list;
    }

    private function setResultList(&$command){
        $resultList = array();
        $sessionList = array();
        $session = Yii::app()->session;
        $list = $this->getRandListForKey();

        foreach ($list as $randNum){
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
        $startDate = CGeneral::toMyDate($quizList["start_time"]);
        $endDate = CGeneral::toMyDate($quizList["end_time"]);
        if($date<$startDate||$date>$endDate){
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
