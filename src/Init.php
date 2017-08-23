<?php

namespace Plansys\Jasper;

class Init
{
    public static function getBase($host)
    {
        return [
            'dir'=> realpath(dirname(__FILE__) . '/..') . '/pages',
            'url' => '/' . trim($host, '/') . '/pages/'
        ];
    }
}
