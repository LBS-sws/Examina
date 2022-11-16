<?php

class ChapterArticleController extends Controller
{
	public $function_id='TE09';

	public function init(){
        $session = Yii::app()->session;
        if(key_exists("menu_code",$_GET)){
            $code = $_GET["menu_code"];
            $session["menu_code"]=$code;
        }elseif (isset($session['menu_code']) && !empty($session['menu_code'])) {
            $code = $session['menu_code'];
        }else{
            $code = "无";
        }
        $this->function_id = "{$code}02";
        parent::init();
    }

    public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('test','testAll','ajaxAddWrong'),
				'expression'=>array('ChapterArticleController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionTest($chapter_id)
	{
		$model = new ChapterArticleModel('test');
		if (!$model->retrieveChapterData($chapter_id)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,"title_name"=>Yii::t("study","mock chapter")));
		}
	}

	public function actionTestAll($menu_id)
	{
		$model = new ChapterArticleModel('test');
		if (!$model->retrieveMenuData($menu_id)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,"title_name"=>Yii::t("study","all mock chapter")));
		}
	}


    //添加错误试题
    public function actionAjaxAddWrong(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $menu_id = key_exists("menu_id",$_POST)?$_POST['menu_id']:0;
            $title_id = key_exists("title_id",$_POST)?$_POST['title_id']:0;
            $choose_id = key_exists("choose_id",$_POST)?$_POST['choose_id']:array();
            $list_choose = key_exists("list_choose",$_POST)?$_POST['list_choose']:array();
            $model = new PaperWrongForm();
            if(MarkedlyTakeModel::validateEmployee($model)){
                $arr = $model->addWrongTitle($menu_id,$title_id,$choose_id,$list_choose);
                echo CJSON::encode($arr);
            }else{
                echo CJSON::encode(array("status"=>0,"message"=>"权限异常"));
            }
        }else{
            echo "Error:404";
        }
        Yii::app()->end();
    }

	public static function allowReadWrite() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
		return Yii::app()->user->validRWFunction("{$code}02");
	}
	
	public static function allowReadOnly() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
		return Yii::app()->user->validFunction("{$code}02");
	}

    public static function allowAll() {
        return true;
    }
}
