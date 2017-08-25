<?php

namespace jasper\Pages;

use Plansys\Jasper\Jasper;

class JasperList extends \Yard\Page
{
    private $jasper;
    public function __construct($alias, $isRoot, $showDeps, $base)
    {
        parent::__construct($alias, $isRoot, $showDeps, $base);
        $this->jasper = new Jasper($this->base->dir['root']);
    }

    public function render() {
        $options = json_encode($this->jasper->getJasperSelect());
        return '
<div>
   <ui:Form.Select name="jasper" label="Jasper List" options=\''.$options.'\' onChangeCallback=""></ui:Form.Select>
</div>
';
    }
}