<?php

namespace app\modules\yiipass\services;

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
                $password_groups = Password::find()
                                                ->select('group')
                                                ->where(['is not', 'group', null])
                                                ->asArray()->all();
                $arr_dropdown = ArrayHelper::map($password_groups, 'group', 'group');
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

    // checkbox column
    protected function renderDataCellContent($model, $key, $index)
    {
        $pagination = $this->grid->dataProvider->getPagination();
        if ($pagination !== false) {
            return $pagination->getOffset() + $index + 1;
        } else {
            return $index + 1;
        }
    }

}