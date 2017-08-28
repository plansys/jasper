<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 25 Agu 2017
 * Time: 14:03
 */

namespace jasper\Pages;

use Plansys\Jasper\Jasper;

class JasperManager extends \Yard\Page
{
    private $jasper;

    public function __construct($alias, $isRoot, $showDeps, $base)
    {
        parent::__construct($alias, $isRoot, $showDeps, $base);
        $this->jasper = new Jasper($this->app()->jasper['dir']);
    }

    public function query($app, $params)
    {
        $cmd = isset($params['cmd']) ? $params['cmd'] : null;
        switch ($cmd) {
            case 'params':
                $params = isset($params['id']) ? $this->jasper->getJasperParams($params['id']) : array();
                echo json_encode($params);
        }
    }

    public function render()
    {
        $options = json_encode($this->jasper->getJasperSelect());
        $format = json_encode($this->jasper->getSupprotedFormat());
        return '
<div>
    <ui:Form.Select name="jasper" label="Jasper List" options=\'' . $options . '\' onChangeCallback=""></ui:Form.Select>
    <div>Show parameters available in jasper file after choose a Jasper List</div>
    <ui:Form.Select name="format" label="Output Format" options=\'' . $format . '\' onChangeCallback=""></ui:Form.Select>
    <ui:Form.Button>Download</ui:Form.Button><br/>
    <ui:Form.Button>Preview</ui:Form.Button>
</div>';
    }

    public function js()
    {
        return <<<JS
this.state = {
    format: 'pdf',
    params: {},
    select_jasper: null
}
JS;
    }
}