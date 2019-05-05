<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Rest\Actions;

use yii\base\Model;
use yii\base\Request;
use yii\web\ServerErrorHttpException;
use Yiisoft\ActiveRecord\ActiveRecord;
use Yiisoft\Yii\Rest\Action;

/**
 * UpdateAction implements the API endpoint for updating a model.
 *
 * For more details and usage information on UpdateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 1.0
 */
class UpdateAction extends Action
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;
    /**
     * @var \yii\web\Request
     */
    protected $request;

    public function __construct($id, $controller, Request $request)
    {
        parent::__construct($id, $controller);
        $this->request = $request;
    }

    /**
     * Updates an existing model.
     * @param string $id the primary key of the model.
     * @return \Yiisoft\ActiveRecord\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     * @throws \yii\exceptions\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\UnsupportedMediaTypeHttpException
     */
    public function run($id): \Yiisoft\ActiveRecord\ActiveRecordInterface
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $model->scenario = $this->scenario;
        $model->load($this->request->getParsedBody(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }
}
