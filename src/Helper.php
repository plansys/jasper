<?php

namespace Plansys\Jasper;


class Helper
{
    public static function download($file_path)
    {
        header("Content-type:" . mime_content_type($file_path));
        header('Content-Disposition: attachment; filename=' . basename($file_path));
        readfile($file_path);
    }

    public static function preview($file_path)
    {
        header("Content-type:" . mime_content_type($file_path));
        header('Content-Disposition: attachment; filename=' . basename($file_path));
        readfile($file_path);
    }
}