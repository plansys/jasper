<?php

namespace jasper\Pages;
use Plansys\Jasper\Helper;
use Plansys\Jasper\Jasper;

class Download extends \Yard\Page
{
    private $jasper;
    public function __construct($alias, $isRoot, $showDeps, $base)
    {
        parent::__construct($alias, $isRoot, $showDeps, $base);
        $this->jasper = new Jasper($this->app()->jasper['dir']);
    }

    public function query($app, $params) {
        $file_path = $this->jasper->getExportFile($params['jasper'], $params['format'], $params['params']);
        echo $file_path;
    }

    public function render()
    {
        var_dump($this);
//        Helper::download();
    }
}