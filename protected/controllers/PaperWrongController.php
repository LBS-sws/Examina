<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0004
 * Time: 上午 11:30
 */
class PaperWrongController extends Controller
{
    public $function_id='TE19';

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
        $this->function_id = "{$code}04";
        parent::init();
    }

    public function filters()
    {
        return array(
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
                'actions'=>array('edit'),
                'expression'=>array('PaperWrongController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','new','save'),
                'expression'=>array('PaperWrongController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validRWFunction("{$code}04");
    }

    public static function allowReadOnly() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validFunction("{$code}04");
    }

    public static function allowRead() {
        return true;
    }

    public function actionIndex($index,$pageNum=0){
        $model = new PaperWrongList;
        if (isset($_POST['PaperWrongList'])) {
            $model->attributes = $_POST['PaperWrongList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['paperWrong_'.$this->function_id]) && !empty($session['paperWrong_'.$this->function_id])) {
                $criteria = $session['paperWrong_'.$this->function_id];
                $model->setCriteria($criteria);
            }
        }

        if(MarkedlyTakeModel::validateEmployee($model)){
            $model->determinePageNum($pageNum);
            $model->retrieveAll($index,$model->pageNum);
            $this->render('index',array('model'=>$model));
        }else{
            throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
        }
    }

    public function actionEdit($index)
    {
        $model = new PaperWrongForm('edit');
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
        $model = new PaperWrongForm('view');
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

    public function actionNew($menu_id)
    {
        $model = new PaperWrongForm('view');
        if(MarkedlyTakeModel::validateEmployee($model)){
            if (!$model->retrieveErrorData($menu_id)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('test',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
        }
    }

    public function actionSave()
    {
        if (isset($_POST['PaperWrongForm'])) {
            $model = new PaperWrongForm($_POST['PaperWrongForm']['scenario']);
            $model->attributes = $_POST['PaperWrongForm'];
            if(MarkedlyTakeModel::validateEmployee($model)){
                if ($model->validate()&&$this->function_id==$model->menu_code) {
                    $model->saveData();
                    Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                } else {
                    $message = CHtml::errorSummary($model);
                    Dialog::message(Yii::t('dialog','Validation Message'), $message);
                }
                $this->redirect(Yii::app()->createUrl('PaperWrong/index',array('index'=>$model->menu_id)));
            }else{
                throw new CHttpException(404,'该账号未绑定员工，无法进行测试');
            }
        }
    }
}