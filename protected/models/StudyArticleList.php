<?php

class StudyArticleList extends CListPageModel
{
    public $menu_id;
    public $menu_code;
    public $menu_name;
    public $class_id;
    public $class_name;
    public $default_img;

    protected $code_pre="01";

    public function getCriteria() {
        return array(
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,

            'menu_id'=>$this->menu_id,
            'class_id'=>$this->class_id
        );
    }
    public function rules()
    {
        return array(
            array('menu_id,class_id,attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter','safe',),
        );
    }
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('examina','ID'),
			'title_code'=>Yii::t('examina','question code'),
			'name'=>Yii::t('examina','question name'),
            'city'=>Yii::t('examina','City'),
            'city_name'=>Yii::t('examina','City'),
            'type_name'=>Yii::t('examina','category name'),
            'show_int'=>Yii::t('examina','show bool'),
		);
	}

	public function retrieveDataByPage($class_id,$pageNum=1){
        $menu = Yii::app()->db->createCommand()->select("a.*,b.menu_name,b.menu_code")
            ->from("exa_study_class a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where("a.id =:id",array(":id"=>$class_id))->queryRow();
        if($menu){
            $this->menu_id = $menu["menu_id"];
            $this->menu_name = $menu["menu_name"];
            $this->menu_code = $menu["menu_code"].$this->code_pre;
            $this->class_id = $class_id;
            $this->class_name = $menu["class_name"];
            $this->default_img = $menu["default_img"];
            $this->getAttrForSearch();
            return true;
        }else{
            return false;
        }
	}

	private function getAttrForSearch($display=1){
	    $this->noOfItem=24;//每页显示24个
        $sql1 = "select * from exa_study 
                where class_id = {$this->class_id} and display={$display} 
			";
        $sql2 = "select count(*) from exa_study 
                where class_id = {$this->class_id} and display={$display}  
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'study_title':
                    $clause .= General::getSqlConditionClause('study_title',$svalue);
                    break;
            }
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        }else{
            $order .= " order by z_index asc,study_date desc,id desc ";
        }

        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'study_title'=>$record['study_title'],
                    'study_img'=>$record['study_img'],
                    'study_subtitle'=>$record['study_subtitle'],
                    'study_date'=>$record['study_date'],
                );
            }
        }
        $session = Yii::app()->session;
        $session['studyArticle_'.$this->menu_code] = $this->getCriteria();
    }

    public function echoMedia(){
        $html = "";
        if(!empty($this->attr)){
            $i=0;
            foreach ($this->attr as $row){
                $linkView = Yii::app()->createUrl('StudyArticle/view',array("index"=>$row["id"]));
                $linkEdit = Yii::app()->createUrl('StudyArticle/edit',array("index"=>$row["id"]));
                $html.="<div class=\"col-lg-6 article-div\" data-href=\"{$linkView}\">";
                $html.='<div class="media">';
                $html.='<div class="media-left">';
                $html.='<div class="img-article">';
                if(!empty($row["study_img"])){
                    $html.="<div><img src='".Yii::app()->createUrl('studyArticle/printImage',array("id"=>$row["id"]))."'/></div>";
                }
                $html.='</div>';
                $html.='</div>';
                $html.='<div class="media-body">';
                $html.='<div class="article-body">';
                $html.='<h4>'.$row["study_title"].'</h4>';
                $html.='<p>'.$row["study_subtitle"].'</p>';
                $html.='</div>';
                $html.='<div class="article-footer">';
                $html.='<div class="footer-date">'.CGeneral::toMyDate($row["study_date"]).'</div>';
                $html.=TbHtml::link("",$linkEdit,array("class"=>"footer-link glyphicon glyphicon-pencil"));
                $html.='</div>';
                $html.='</div>';
                $html.='</div>';
                $html.='</div>';
            }
        }else{
            $html="暂时没有文章，请与管理员联系";
        }
        return $html;
    }

    public function echoNoneDiv(){
        $html = "";
        $rows = Yii::app()->db->createCommand()->select("id,study_title")
            ->from("exa_study")
            ->where("class_id =:id and display=0",array(":id"=>$this->class_id))
            ->order("z_index asc,id desc")->queryAll();
        if($rows){
            $html = "<div class='box'><div class='box-body'>";
            $html.= "<h4>被隐藏的文章</h4>";
            $i=0;
            foreach ($rows as $row){
                $i++;
                $linkEdit = Yii::app()->createUrl('StudyArticle/edit',array("index"=>$row["id"]));
                $linkText = Yii::app()->createUrl('StudyArticle/view',array("index"=>$row["id"]));
                $html.='<div class="media">';
                $html.='<div class="media-left media-middle"><span class="num_i">'.$i.'</span></div>';
                $html.=TbHtml::link("<h4>{$row["study_title"]}</h4>",$linkText,array("class"=>"media-body media-middle"));
                $html.='<div class="media-right media-middle">';
                $html.=TbHtml::link("",$linkEdit,array("class"=>"glyphicon glyphicon-pencil"));
                $html.='</div>';
                $html.='</div>';
            }
            $html.= "</div></div>";
        }
        return $html;
    }

    public function navBar()
    {
        $html="<span>共".$this->totalRow."条记录</span>";
        $totalrow = $this->totalRow;
        $pageno = $this->pageNum;
        $pagerow = ($this->noOfItem == 0) ? $totalrow : $this->noOfItem;
        $remain = ($pagerow==0) ? 0 : $totalrow % $pagerow;
        $totalpage = ($pagerow==0) ? 1 : (($totalrow - $remain) / $pagerow) + (($remain==0) ? 0 : 1);
        $window = 10;
        $tab = 3;
        $width=80/$window;

        $items = array();

        $param = array('pageNum'=>1,'class_id'=>$this->class_id);
        $url = Yii::app()->createUrl("StudyArticle/index",$param);
        $items[] = array('label'=>'1','url'=>$url,'active'=>($pageno == 1));
        $cnt = 1;

        if ($pageno > $tab && $totalpage > $window) {
            $items[] = array('label'=>'...','url'=>'#',);
            $cnt++;
        }

        $hadj = ($pageno > $tab && $totalpage > $window) ? 2 : 1;
        $tadj = ($totalpage > $window) ? (($pageno < $totalpage-($window-$hadj)+1) ? 2 : 1) : 0;
        $adj = $hadj + $tadj;

        $pos = ($pageno > $tab && $totalpage > $window)
            ? (($pageno > $totalpage-($window-$hadj)+1) ? $totalpage-($window-$hadj)+1 : $pageno-($tab-1))
            : 2;
        while ($pos <= $totalpage && $cnt < $window-$tadj)
        {
            $param = array('pageNum'=>$pos,'class_id'=>$this->class_id);
            $url = Yii::app()->createUrl("StudyArticle/index",$param);
            $items[] = array('label'=>$pos,'url'=>$url,'active'=>($pageno == $pos));
            $pos++;
            $cnt++;
        }

        if ($totalpage > $window) {
            if ($pageno < $totalpage-($window-$adj-$tab)-1 && $totalpage > $window) {
                $items[] = array('label'=>'...','url'=>'#',);
                $cnt++;
            }

            $param = array('pageNum'=>$totalpage,'class_id'=>$this->class_id);
            $url = Yii::app()->createUrl("StudyArticle/index",$param);
            $items[] = array('label'=>$totalpage,'url'=>$url,'active'=>($pageno == $totalpage));

            $cnt++;
        }

        return $html.TbHtml::pagination($items, array('class'=>'pagination pagination-sm no-margin pull-right'));
    }
}
