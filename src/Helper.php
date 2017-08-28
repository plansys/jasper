<?php

namespace Plansys\Jasper;


class Helper
{
    public static function download($file_path, $delete = false)
    {
        header("Content-type:" . mime_content_type($file_path));
        header('Content-Disposition: attachment; filename=' . basename($file_path));
        readfile($file_path);
        if($delete) {
            unlink($file_path);
        }
    }

    public static function preview($file_path)
    {
//        header("Content-type:" . mime_content_type($file_path));
//        header('Content-Disposition: attachment; filename=' . basename($file_path));
//        echo readfile($file_path);
        include_once $file_path;
    }
}