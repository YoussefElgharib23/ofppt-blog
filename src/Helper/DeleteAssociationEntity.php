<?php

namespace App\Helper;

class DeleteAssociationEntity
{

    public static function deleteAssociation($array) {
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

    public static function restoreAssociation($array) {
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