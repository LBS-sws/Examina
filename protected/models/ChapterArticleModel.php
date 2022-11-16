<?php

class ChapterArticleModel extends CFormModel
{
    public $id;
    public $menu_id;
    public $menu_name;
    public $menu_code;

    public $chapter_id;
    public $chapter_name;
    public $item_sum;
    public $random_num;

    public $chapter_list=array();//所有試題
    public $paper_list=array();//試卷試題


    public $title_id_list=array();//試卷試題的所有试题id 例如：array(3,2,5,1);
    public $choose_id=array();//试题选项的顺序 例如：array(title_id=>'2,1,3,4');
    public $choose=array();//試卷試題的客户选择 例如：array(title_id=>1);

    protected $code_pre="02";

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('study','ID'),
            'chapter_name'=>Yii::t('study','chapter name'),

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
            array('id, menu_id, chapter_id','safe'),
            array('chapter_id','required'),
            array('chapter_id','validateChapter'),
        );
    }

    public function validateChapter($attribute, $params){
        $row = Yii::app()->db->createCommand()
            ->select("a.chapter_name,a.item_sum,a.menu_id,b.menu_code,b.menu_name")
            ->from("exa_chapter_class a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where('a.id=:id',array(':id'=>$this->chapter_id))->queryRow();
        if ($row){
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->chapter_name = $row["chapter_name"];
            $this->item_sum = $row["item_sum"];
        }else{
            $message = "数据异常请刷新重试";
            $this->addError($attribute,$message);
        }
    }

    public function retrieveChapterData($chapter_id){
        $row = Yii::app()->db->createCommand()
            ->select("a.chapter_name,a.item_sum,a.random_num,a.menu_id,b.menu_code,b.menu_name")
            ->from("exa_chapter_class a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where('a.id=:id',array(':id'=>$chapter_id))->queryRow();
        if ($row){
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->chapter_id = $chapter_id;
            $this->chapter_name = $row["chapter_name"];
            $this->item_sum = $row["item_sum"];
            $this->random_num = $row["random_num"];

            $this->chapter_list=self::getAllChapterList($this->chapter_id);
            $this->paper_list = self::resetRandomList($this->chapter_list,$this->random_num);

            return true;
        }
        return false;
    }

    public function retrieveMenuData($menu_id){
        $row = Yii::app()->db->createCommand()
            ->select("id,menu_code,menu_name")->from("exa_setting")
            ->where('id=:id',array(':id'=>$menu_id))->queryRow();
        if ($row){
            $this->menu_id = $row["id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->random_num = 20;
            $this->chapter_name = Yii::t("study","all mock chapter");

            $this->chapter_list=self::getAllMenuList($this->menu_id);
            $this->paper_list = self::resetRandomList($this->chapter_list,$this->random_num);

            return true;
        }
        return false;
    }

    public static function showPaperTitle($model,$list,$resetBool=true,$remarkBool=false){
        $className = get_class($model);
        $html = "";
        if(!empty($list)){
            $i = 0;
            $count = count($list);
            foreach ($list as $row){
                $i++;
                $active=$i==1?"active":"";
                $chooseList = self::selectExListForArr($row["chooseList"],$resetBool,$remarkBool);
                $html.="<div class='resultDiv {$active}' data-id='{$row["id"]}'>";
                //试题id
                $html.=TbHtml::hiddenField("{$className}[title_id_list][]",$row["id"]);
                //保存选项的固定顺序（choose_id）
                $html.=TbHtml::hiddenField("{$className}[choose_id][{$row["id"]}]",implode(",",array_keys($chooseList)));
                $html.="<div class='resultBody'>";
                $html.="<h4 class='resultBody_t'><b>{$i}/{$count}、{$row['name']}</b></h4>";
                $html.="<div class='resultBody_b'>";
                if($row["title_type"]==1){
                    $html.=TbHtmlEx::checkBoxListEx("{$className}[choose][{$row['id']}][]","",$chooseList,array(
                        'class' => 'btn-checkbox'
                    ));
                }else{
                    $html.=TbHtmlEx::radioButtonListEx("{$className}[choose][{$row['id']}][]","",$chooseList,array(
                        'class' => 'btn-checkbox'
                    ));
                }
                $html.="</div>";
                $html.="</div>";
                $html.="<div class='resultFooter'>";
                $html.=TbHtml::button(Yii::t("study","ok"),array("class"=>"btn btn-primary btn-res","data-id"=>$row["id"]));
                $html.="</div>";
                if($remarkBool){
                    $html.="<div class='resultRemark'>";
                    $html.="<b>".Yii::t("study","Interpretation")."：</b>";
                    $html.="<span>".$row['remark']."</span>";
                    $html.="</div>";
                }
                $html.="</div>";
            }

        }
        return $html;
    }

    //答題卡
    public static function showAnswerSheet($list,$remarkBool=false){
        $html = "<p class='text-center'>".Yii::t("study","answer sheet")."</p>";
        $html.= "<ul class='list-inline answer-sheet-ul' id='answerSheet'>";
        if(!empty($list)){
            $i=0;
            foreach ($list as $row){
                $i++;
                $active=$i==1?"current":"";
                $html.="<li data-id='{$row['id']}' class='{$active}'>{$i}<span></span>";
            }
        }
        $html.= "</ul>";
        if($remarkBool){
            $html.= "<div>错误：<span id='span_error'>0</span>题</div>";
            $html.= "<div>正确：<span id='span_success'>0</span>题</div>";
            $html.= "<div>正确率：<span id='span_ratio'></span></div>";
        }else{
            $html.= "<div>共".count($list)."题，已做<span id='span_ok'>0</span>题</div>";
        }
        return $html;
    }

    //獲取章節內的所有試題
    public static function getAllChapterList($chapter_id){
        $list = array();
        $rows = Yii::app()->db->createCommand()
            ->select("id,title_type,title_code,name,remark,chapter_id")
            ->from("exa_chapter_title")
            ->where('chapter_id=:id and display=1',array(':id'=>$chapter_id))->queryAll();
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

    //獲取部分章節內的所有試題()
    public static function getBumenChapterList($menu_id,$bumen=""){
        $list = array();
        $sql = "";
        if(!empty($bumen)){
            $sql = " and b.id in (-1{$bumen}-2)";//$bumen=",1,2,3,"
        }
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.title_type,a.title_code,a.name,a.remark,a.chapter_id")
            ->from("exa_chapter_title a")
            ->leftJoin("exa_chapter_class b","b.id=a.chapter_id")
            ->where("b.menu_id=:id and a.display=1 and b.display=1 {$sql}",array(':id'=>$menu_id))
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

    //獲取部门內的所有試題
    public static function getAllMenuList($menu_id){
        $list = array();
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.title_type,a.title_code,a.name,a.remark,a.chapter_id")
            ->from("exa_chapter_title a")
            ->leftJoin("exa_chapter_class b","b.id=a.chapter_id")
            ->where('b.menu_id=:id and a.display=1 and b.display=1',array(':id'=>$menu_id))
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

    //从數組中随机获取几个元素
    public static function resetRandomList($list,$num){
        $arr = array();
        if(!empty($list)){
            $num = count($list)<$num?count($list):$num;//随机元素不能超过数组长度
            $idList = array_keys($list);
            shuffle($idList);//打乱key值
            foreach ($idList as $id){
                if(count($arr)<$num){
                    $arr[]=$list[$id];
                }else{
                    break;//跳出循环
                }
            }
        }
        return $arr;
    }

    public static function selectExListForArr($arr,$resetBool=false,$judgeShow=false){
        $chooseStr = array("A","B","C","D");
        $list = array();
        if(!empty($arr)){
            if($resetBool){
                shuffle($arr);
            }
            $i = 0;
            foreach ($arr as $row){
                $item = array(
                    "id"=>$row["id"],
                    "name"=>$chooseStr[$i]."、".$row["choose_name"]
                );
                if($judgeShow){
                    $item["judge"]=$row["judge"];
                }
                $list["{$row["id"]}"] = $item;
                $i++;
            }
        }
        return $list;
    }
}
