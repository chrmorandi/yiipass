<?php

namespace app\modules\yiipass\services;

use app\modules\yiipass\controllers\PasswordController;
use app\modules\yiipass\controllers\UserController;
use yii\grid\GridView;
use yii\helpers\Html;
use app\modules\yiipass\models\Password;
use app\modules\yiipass\models\PasswordSearch;
use yii\helpers\ArrayHelper;

/**
 * Class CustomGridViewService
 * @package app\components
 */
class CustomGridViewService extends GridView {

    /**
     * Renders the filter.
     * @return string the rendering result.
     */
    public function renderFilters()
    {
        if ($this->filterModel !== null) {
            $cells = [];
            foreach ($this->columns as $column) {
                /* @var $column Column */
                $cells[] = $column->renderFilterCell();
            }

            $new_cells = $this->modifyFilters($cells);

            return Html::tag('tr', implode('', $new_cells), $this->filterRowOptions);
        } else {
            return '';
        }
    }

    /**
     * Passes the cells to the specific method to each filter and allows
     * to extend this for more filters.
     *
     * @param $cells
     * @return array
     */
    private function modifyFilters($cells){
        $cells = $this->modifyGroupInput($cells);
        return $cells;
    }

    /**
     * Modifies the group input to let the user choose all existing groups.
     *
     * @param $cells
     * @return array
     */
    private function modifyGroupInput($cells){

        foreach($cells as $cell){
            if(is_numeric(strpos($cell, '[group]'))){
                $searchModel = new PasswordSearch();
                $acc_groups = Password::find()
                                                ->select(['id', 'group'])
                                                ->where(['is not', 'group', null])
                                                ->asArray()->all();

                // Filter unique group items from all account credentials.
                $acc_groups = self::getUniqueArrItems($acc_groups, 'group');

                // Groups for which the user has access.
                $allowed_acc_groups = array();

                if (intval(\Yii::$app->user->identity->is_admin) !== 1){
                    foreach ($acc_groups as $a_group) {
                        // Iterate all groups and check if user is allowed.
                        if (PasswordController::checkAccessByAccId($a_group['id'])){
                            $allowed_acc_groups[] = $a_group;
                        }
                    }
                } else {
                    // Admin can access everything.
                    $allowed_acc_groups = $acc_groups;
                }

                $arr_dropdown = ArrayHelper::map($allowed_acc_groups, 'group', 'group');
                $cell = Html::activeDropDownList($searchModel, 'group', $arr_dropdown,
                                                ['class'=>'form-control','prompt' => 'Select Group']);
                $cell = $this->render('@app/modules/yiipass/views/elements/dropdown', array('group_input' => $cell));
            }

            // Remove "lastaccess" input. Working sorting is enough here. Input for date works not good.
            if(is_numeric(strpos($cell, '[lastaccess]'))){
                $cell = '';
            }

            $new_cells[] = $cell;
        }
        return $new_cells;
    }

    /**
     * Displays the index for the pagination.
     *
     * @param int $index The index id.
     *
     * @return mixed
     */
    protected function renderDataCellContent($index)
    {
        $pagination = $this->grid->dataProvider->getPagination();
        if ($pagination !== false) {
            return $pagination->getOffset() + $index + 1;
        } else {
            return $index + 1;
        }
    }

    /**
     * Filters duplicate items in multi-dimensional array.
     *
     * @param array $array The array with duplicated items.
     * @param string $key The key on which should be filtered.
     *
     * @return array
     */
    protected static function getUniqueArrItems($array, $key)
    {
        $temp_array = array();

        foreach ($array as &$v) {

            if (!isset($temp_array[$v[$key]]))

                $temp_array[$v[$key]] =& $v;

        }

        $array = array_values($temp_array);

        return $array;
    }

}
