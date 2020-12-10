<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class SimTestController extends Controller
{
	public $function_id='EM02';

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
                'actions'=>array('index','save','audit'),
                'expression'=>array('SimTestController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('EM02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('EM02');
    }

    public static function allowRead() {
        return true;
    }

    public function actionIndex(){
        $model = new SimTestForm('new');
        $this->render('index',array('model'=>$model));
    }

    public function actionSave()
    {
        if (isset($_POST['SimTestForm'])) {
            $index = $_POST['SimTestForm']["quiz_id"];
            $quizModel = new Examina($index);
            if(!$quizModel->getErrorBool()){
                if($quizModel->validateTime()){
                    $this->render('new',array('model'=>$quizModel,));
                }else{
                    $message = Yii::t("examina","The test list has expired");
                    Dialog::message(Yii::t('dialog','Validation Message'), $message);
                    $this->redirect(Yii::app()->createUrl('simTest/index'));
                }
                //var_dump($quizModel->getResultList());
            }else{
                throw new CHttpException(403,'該測驗單沒有試題無法開始測驗，請聯繫管理員.');
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['examina'])) {
            $model = new MyTestForm('new');
            $model->attributes = $_POST['examina'];
            if ($model->validate()) {
                $model->saveData();
                if($model->title_num/$model->title_sum<0.85){
                    Dialog::message(Yii::t('dialog','Warning'), Yii::t('block','validateExamination'));
                }else{
                    Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                }
                $this->redirect(Yii::app()->createUrl('myTest/view',array('index'=>$model->join_id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('simTest/index'));
            }
        }else{
            throw new CHttpException(404,'The requested page does not exist.');
        }
    }
}