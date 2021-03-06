<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\space\controllers;

use Yii;
use humhub\components\Controller;
use yii\helpers\Html;

/**
 * BrowseController
 *
 * @author Luke
 * @package humhub.modules_core.space.controllers
 * @since 0.5
 */
class BrowseController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'acl' => [
                'class' => \humhub\components\behaviors\AccessControl::className(),
                'guestAllowedActions' => ['search-json']
            ]
        ];
    }

    /**
     * Returns a workspace list by json
     *
     * It can be filtered by by keyword.
     */
    public function actionSearchJson()
    {
        \Yii::$app->response->format = 'json';

        $keyword = Yii::$app->request->get('keyword', "");
        $page = (int) Yii::$app->request->get('page', 1);
        $limit = (int) Yii::$app->request->get('limit', \humhub\models\Setting::Get('paginationSize'));

        $searchResultSet = Yii::$app->search->find($keyword, [
            'model' => \humhub\modules\space\models\Space::className(),
            'page' => $page,
            'pageSize' => $limit
        ]);

        $json = array();
        foreach ($searchResultSet->getResultInstances() as $space) {
            $spaceInfo = array();
            $spaceInfo['guid'] = $space->guid;
            $spaceInfo['title'] = Html::encode($space->name);
            $spaceInfo['tags'] = Html::encode($space->tags);
            $spaceInfo['image'] = $space->getProfileImage()->getUrl();
            $spaceInfo['link'] = $space->getUrl();

            $json[] = $spaceInfo;
        }

        return $json;
    }

}

?>