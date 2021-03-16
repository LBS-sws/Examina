<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ExamTheoryController extends Controller
{
	public $function_id='TP05';

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
                'actions'=>array('index','view','edit','fileupload','fileRemove'),
                'expression'=>array('ExamTheoryController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('ExamTheoryController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('TP05');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('TP05');
    }

    public function actionIndex(){
        if(Yii::app()->user->validRWFunction('TP05')){
            $this->redirect("edit");
        }else{
            $this->redirect("view");
        }
    }

    public function actionView()
    {
        $flowTitleModel = new FlowTitleForm('view');
        $this->render('index',array('flowTitleModel'=>$flowTitleModel));
    }

    public function actionEdit()
    {
        $flowTitleModel = new FlowTitleForm('edit');
        $this->render('index',array('flowTitleModel'=>$flowTitleModel));
    }

    public function actionFileupload($doctype) {
        $model = new FlowTitleForm('edit');
        if (isset($_POST['FlowTitleForm'])) {
            $model->attributes = $_POST['FlowTitleForm'];
            //var_dump($_POST['FlowTitleForm']);die();
            $id = 1;
            $model->setScenario("edit");
            $model->id = 1;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileRemove($doctype) {
        $model = new FlowTitleForm();
        if (isset($_POST['FlowTitleForm'])) {
            $model->attributes = $_POST['FlowTitleForm'];
            $model->id = 1;
            $model->setScenario("edit");
            $docman = new DocMan($model->docType,$model->id,'FlowTitleForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $row = $docId == 1;
        if ($row!==false) {
            $docman = new DocMan($doctype,$docId,'FlowTitleForm');
            $docman->masterId = $mastId;
            $docman->fileDownload($fileId);
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }
}