<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Rest\Actions;

use Yiisoft\Yii\Rest\Action;

/**
 * ViewAction implements the API endpoint for returning the detailed information about a model.
 *
 * For more details and usage information on ViewAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 1.0
 */
class ViewAction extends Action
{
    /**
     * Displays a model.
     * @param string $id the primary key of the model.
     * @return \Yiisoft\ActiveRecord\ActiveRecordInterface the model being displayed
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id): \Yiisoft\ActiveRecord\ActiveRecordInterface
    {
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            \call_user_func($this->checkAccess, $this->id, $model);
        }

        return $model;
    }
}
