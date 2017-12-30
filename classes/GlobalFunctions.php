<?php
/**
 * Created by PhpStorm.
 * User: vaibhav
 * Date: 12/30/17
 * Time: 12:42 AM
 */

class GlobalFunctions
{
    public function getFormatedDateTime($dateStr, $format = 'Y-m-d H:i:s')
    {
        $date = new DateTime($dateStr);
        return $date->format($format);
    }
}