<?php

class MutualController extends Controller
{
	public $function_id='CCCC';

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
        $this->function_id = "{$code}03";
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
				'actions'=>array('edit','add','draft','audit','delete'),
				'expression'=>array('MutualController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','my','view','pageAjax'),
				'expression'=>array('MutualController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($index=0)
	{
		$model = new MutualModel();
        if (!$model->retrieveAll($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('index',array('model'=>$model));
        }
	}

	//瀑布流的异步请求
	public function actionPageAjax($index=0,$page=1)
	{
		$model = new MutualModel();
        if (!$model->retrieveAll($index)) {
            echo "";
        } else {
            echo $model->getMutualHtml($page);
        }
	}

    public function actionMy($index,$pageNum=0){
        $model = new MutualMyList();
        if (isset($_POST['MutualMyList'])) {
            $model->attributes = $_POST['MutualMyList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['mutualMy_'.$this->function_id]) && !empty($session['mutualMy_'.$this->function_id])) {
                $criteria = $session['mutualMy_'.$this->function_id];
                $model->setCriteria($criteria);
            }
        }
        if(MarkedlyTakeModel::validateEmployee($model)){
            $model->determinePageNum($pageNum);
            $model->retrieveAll($index,$model->pageNum);
            $this->render('my',array('model'=>$model));
        }else{
            throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
        }
    }

    public function actionDraft()
    { //草稿
        if (isset($_POST['MutualModel'])) {
            $model = new MutualModel($_POST['MutualModel']['scenario']);
            $model->attributes = $_POST['MutualModel'];
            if(MarkedlyTakeModel::validateEmployee($model)){
                if ($model->validate()&&$this->function_id==$model->menu_code) {
                    $model->mutual_state=0;
                    $model->saveData();
                    Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                    $this->redirect(Yii::app()->createUrl('mutual/edit',array('index'=>$model->id)));
                } else {
                    $message = CHtml::errorSummary($model);
                    Dialog::message(Yii::t('dialog','Validation Message'), $message);
                    $this->render('form',array('model'=>$model,));
                }
            }else{
                throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
            }
        }
    }

    public function actionAudit()
    { //审核
        if (isset($_POST['MutualModel'])) {
            $model = new MutualModel($_POST['MutualModel']['scenario']);
            $model->attributes = $_POST['MutualModel'];
            if(MarkedlyTakeModel::validateEmployee($model)){
                if ($model->validate()&&$this->function_id==$model->menu_code) {
                    $model->mutual_state=1;
                    $model->saveData();
                    Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                    $this->redirect(Yii::app()->createUrl('mutual/edit',array('index'=>$model->id)));
                } else {
                    $message = CHtml::errorSummary($model);
                    Dialog::message(Yii::t('dialog','Validation Message'), $message);
                    $this->render('form',array('model'=>$model,));
                }
            }else{
                throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
            }
        }
    }

    public function actionAdd($index)
    {
        $model = new MutualModel('new');
        $model->mutual_date=date("Y-m-d");
        if(MarkedlyTakeModel::validateEmployee($model)){
            if (!$model->retrieveMenuData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
        }
    }

    public function actionEdit($index)
    {
        $model = new MutualModel('edit');
        if(MarkedlyTakeModel::validateEmployee($model)){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
        }
    }

    public function actionView($index)
    {
        $model = new MutualModel('view');
        if(MarkedlyTakeModel::validateEmployee($model)){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
        }
    }

    public function actionDelete()
    {
        $model = new MutualModel('delete');
        if (isset($_POST['MutualModel'])) {
            $model->attributes = $_POST['MutualModel'];
            if(MarkedlyTakeModel::validateEmployee($model)){
                if ($model->validate()&&$this->function_id==$model->menu_code) {
                    $model->saveData();
                    Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                    $this->redirect(Yii::app()->createUrl('mutual/my',array("index"=>$model->menu_id)));
                } else {
                    $message = CHtml::errorSummary($model);
                    Dialog::message(Yii::t('dialog','Validation Message'), $message);
                    $this->render('form',array('model'=>$model));
                }
            }else{
                throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
            }
        }
    }

    public static function allowReadWrite() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validRWFunction("{$code}03");
    }

    public static function allowReadOnly() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validFunction("{$code}03");
    }
}
