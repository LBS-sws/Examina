<?php

class Counter {
    public static function countAuditMutual() {
        $arr = array();
        $menuRows = Yii::app()->db->createCommand()->select("id,menu_code")
            ->from("exa_setting")->where("display=1")->queryAll();
        if($menuRows){
            foreach ($menuRows as $menu){
                $count = Yii::app()->db->createCommand()->select("count(a.id)")->from("exa_mutual a")
                    ->where("a.menu_id=:menu_id and a.mutual_state=1",array(":menu_id"=>$menu["id"]))
                    ->queryScalar();
                $arr[]=array('code'=>$menu["menu_code"]."10",'count'=>$count,'color'=>"bg-yellow");
            }
        }
        return $arr;
    }

	public static function countSign() {
		$rtn = 0;

		$wf = new WorkflowPayment;
		$wf->connection = Yii::app()->db;
		$list = $wf->getPendingRequestIdList('PAYMENT', 'PS', Yii::app()->user->id);
		$items = empty($list) ? array() : explode(',',$list);
		$rtn = count($items);

		return $rtn;
	}
}

?>