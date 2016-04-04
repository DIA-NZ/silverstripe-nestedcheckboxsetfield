<?php

class NestedCheckboxSetFieldSourceGenerator {

    public static function generate(DataList $parentList, callable $childCallback) {
        $source = array();

        foreach ($parentList as $parentItem) {
            $childSource = array();
            $children = $childCallback($parentItem);

            foreach ($children as $childItem) {
                $childSource += array(
                    $childItem->ID => $childItem->Title
                );
            }

            $source[$parentItem->Title] = $childSource;
        }

        return $source;
    }

}