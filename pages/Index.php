<?php

namespace jasper\Pages;
use Plansys\Jasper\Helper;
use Plansys\Jasper\Jasper;

class Index extends \Yard\Page
{
    private $jasper;
    public function __construct($alias, $isRoot, $showDeps, $base)
    {
        parent::__construct($alias, $isRoot, $showDeps, $base);
        $this->jasper = new Jasper($this->app()->jasper['dir']);
    }

    public function query($app, $params) {
        $do = isset($_GET['do']) ? $_GET['do'] : '';
        switch ($do) {
            case "d":
                if(isset($_GET['f'])) {
                    $file = $this->app()->jasper['dir'] . DIRECTORY_SEPARATOR . $_GET['f'];
                    $exp = explode('.', $file);
                    $delete = false;
                    if(count($exp) > 0) {
                        if ($exp[1] != 'html') {
                            $delete = true;
                        }
                    }
                    Helper::download($file, $delete);
                }
                break;
            case "p":
                if(isset($_GET['f'])) {
                    $file = $this->app()->jasper['dir'] . DIRECTORY_SEPARATOR . $_GET['f'];
                    $exp = explode('.', $file);
                    $html = $exp[0] . '.html';
                    Helper::preview($html);
                }
                break;
            default:
                if(isset($params['jasper']) && isset($params['format']) && isset($params['params'])) {
                    $file_path = $this->jasper->getExportFile($params['jasper'], $params['format'], $params['params'], true);
                    echo $file_path;
                } else {
                    echo 'Invalid Request';
                }
                break;
        }
    }

    public function render()
    {
        var_dump($this);
//        Helper::download();
    }
}