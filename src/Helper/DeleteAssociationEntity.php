<?php

namespace App\Helper;

class DeleteAssociationEntity
{
    /**
     * @param $array
     */
    public static function deleteAssociation($array): void
    {
        foreach ($array as $item) {
            if (is_array($item)) {
                foreach ($item as $value) {
                    $value->delete();
                }
            }
            else {
                $item->delete();
            }
        }
    }

    /**
     * @param $array
     * @return void
     */
    public static function restoreAssociation($array): void
    {
        foreach ($array as $item) {
            if (is_array($item)) {
                foreach ($item as $value) {
                    $value->restore();
                }
            }
            else {
                $item->restore();
            }
        }
    }

}