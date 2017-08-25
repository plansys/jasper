<?php

namespace jasper\Pages;

class Preview extends \Yard\Page
{
    private $jasper;
    public function __construct($alias, $isRoot, $showDeps, $base)
    {
        parent::__construct($alias, $isRoot, $showDeps, $base);
        $this->jasper = new Jasper($this->app()->jasper['dir']);
    }

    public function render()
    {

    }
}