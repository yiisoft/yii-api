<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Rest\Actions;

use yii\base\Response;
use yii\web\ServerErrorHttpException;
use Yiisoft\Yii\Rest\Action;

/**
 * DeleteAction implements the API endpoint for deleting a model.
 *
 * For more details and usage information on DeleteAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 1.0
 */
class DeleteAction extends Action
{
    /**
     * @var \yii\web\Response
     */
    protected $response;

    public function __construct($id, $controller, Response $response)
    {
        parent::__construct($id, $controller);
        $this->response = $response;
    }

    /**
     * Deletes a model.
     * @param mixed $id id of the model to be deleted.
     * @throws ServerErrorHttpException on failure.
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        $this->response->setStatusCode(204);
    }
}
