<?php
//虛擬一些假數據
class VideoHitsModel
{
    public $employee_list;
    public $study_list;
    public $menu_id = 0;

    public function __construct($menu_id=0){
        $this->menu_id = $menu_id;
        $this->employee_list=array();
        $this->addEmployeeForHr();
        $this->addEmployeeForHit();
        $this->study_list=array();
        $this->addStudyList();
    }

    private function getRoundListForDay($date){
        $w = date("w",strtotime($date));
        if($w==0||$w==6){//週末
            $numCount = mt_rand(0,6);
        }else{
            $numCount = mt_rand(8,25);
        }
        $dateTimer = array();
        $staffList = array();
        $studyList = array();
        for ($i=0;$i<$numCount;$i++){
            $dateTimer[] = $this->getRoundHIS();
            $staffKey = mt_rand(0,count($this->employee_list)-1);
            $studyKey = mt_rand(0,count($this->study_list)-1);
            $staffList[] = $this->employee_list[$staffKey];
            $studyList[] = $this->study_list[$studyKey];
        }
        array_shift($dateTimer);
        $list = array();
        foreach ($dateTimer as $key =>$value){
            $staff = $staffList[$key];
            $list[]=array(
                "id"=>$staff["id"],
                "hit_date"=>$date." ".$value,
                "entry_time"=>$staff["entry_time"],
                "study_title"=>$studyList[$key],
                "code"=>$staff["code"],
                "employee_name"=>$staff["employee_name"],
                "city_name"=>$staff["city_name"],
            );
        }
        return $list;
    }

    public function getRoundList(){
        $endData = "2023/07/11";
        $day = 3*30;
        $list = array();
        for ($i=1;$i<=$day;$i++){
            $hit_date = date("Y-m-d",strtotime($endData." - {$i} day"));
            $temp = $this->getRoundListForDay($hit_date);
            $list = array_merge($list,$temp);
        }
        return $list;
    }

    private function getRoundHIS(){
        return $this->getRoundH().":".$this->getRoundI().":".$this->getRoundS();
    }

    private function getRoundH(){
        $number = mt_rand(10,17);
        return $number;
    }

    private function getRoundI(){
        $number = mt_rand(0,59);
        return $number>9?$number:"0{$number}";
    }

    private function getRoundS(){
        $number = mt_rand(0,59);
        return $number>9?$number:"0{$number}";
    }

    private function addStudyList(){
        $rs = Yii::app()->db->createCommand()->select("study_title")
            ->from("exa_study")->where("menu_id={$this->menu_id}")->queryAll();
        if($rs){
            foreach ($rs as $row){
                $this->study_list[] = $row["study_title"];
            }
        }
        if(empty($this->study_list)){
            $this->study_list[]="灭虫、消毒";
        }
    }

    private function addEmployeeForHr(){
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()
            ->select("b.id,b.code,b.name as employee_name,b.entry_time,f.name as city_name")
            ->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("security$suffix.sec_city f","b.city = f.code")
            ->queryAll();
        if($rs){
            $rsCount = count($rs);
            $maxCount = mt_rand(7,20);
            $maxCount = $rsCount<$maxCount?$rsCount:$maxCount;
            for($i=0;$i<$maxCount;$i++){
                $id = mt_rand(0,$rsCount-1);
                $this->employee_list[] = $rs[$id];
            }
        }
    }

    private function addEmployeeForHit(){
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()
            ->select("b.id,b.code,b.name as employee_name,b.entry_time,f.name as city_name")
            ->from("exa_link_hits a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("security$suffix.sec_city f","b.city = f.code")
            ->group("b.id,b.code,b.name,b.entry_time,f.name")
            ->queryAll();
        if($rs){
            foreach ($rs as $row){
                $this->employee_list[] = $row;
            }
        }
    }
}
