<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 24 Agu 2017
 * Time: 13:23
 */

namespace Plansys\Jasper;
use JasperPHP\JasperPHP;
use Plansys\Jasper\Helper;

class Jasper
{
    public $jaspers = [];
    private $jasper_php;
    private $root_dir;
    private $jasper_url;
    private $jasper_dir;

    public function __construct($jasper_dir)
    {
//        $this->root_dir = $root_dir;
        $this->jasper_url = '/repo/jasper';
        $this->jasper_dir = $jasper_dir;
        $this->initJasper();
        $this->jasper_php = new JasperPHP();
    }

    private function initJasper() {
        $files = scandir($this->jasper_dir);
        foreach ($files as $file) {
            $exp = explode('.', $file);
            $ext = $exp[count($exp) - 1];
            $name = $exp[count($exp) - 2];
            $path = $this->jasper_dir . DIRECTORY_SEPARATOR . $file;
            $url = $this->jasper_url . DIRECTORY_SEPARATOR . $name;
            if ($ext == 'jrxml' || $ext == 'jasper') {
                $this->setJasperItem($name, $path, $url, $ext);
            }
        }
    }

    private function setJasperItem($name, $path, $url, $ext) {
        $this->jaspers[$name . '.' . $ext] = [
            'label' => $name . '.' . $ext,
            'path' => $path,
            'url' => $url
        ];
    }

    public function getJasperSelect() {
        $options = [];
        foreach ($this->jaspers as $k => $v) {
            $options[$k] = $v['label'];
        }

        return $options;
    }

    public function getJasperParams($id) {
        return $this->jasper_php->list_parameters($this->jaspers[$id]['path']);
    }

    private function compileJasper($id) {
        $jasper_file = $this->jaspers[$id]['path'];
        $exp = explode('.', $jasper_file);
        $ext = $exp[count($exp) - 1];
        $name = $exp[count($exp) - 2];
        if($ext == 'jrxml') {
            $jasper_time = file_exists($name . '.jasper') ? filemtime($name . '.jasper') : 0;
            $jrxml_time = filemtime($jasper_file);
            if($jasper_time <= $jrxml_time) {
                try {
                    $this->jasper_php->compile($jasper_file)->execute(true);
                } catch (\Exception $e) {
                    return null;
                }
            }
            $jasper_file = $name . '.jasper';
        }
        return $jasper_file;
    }

    private function processJasper($id, $file, $output_file, $format, $params) {
        $export_file = $file;
        $exp = explode('.', $export_file);
        $ext = $exp[count($exp) - 1];
        $name = $exp[count($exp) - 2];
        if($ext == 'jrxml') {
            $export_file = $this->compileJasper($id);
            if(!is_null($export_file)) {
                $ext = 'jasper';
            } else {
                return null;
            }
        }
        if($ext == 'jasper') {
            try {
                $this->jasper_php->process($export_file, $output_file, $format, $params)->execute(true);
            } catch (\Exception $e) {
                return null;
            }
            $export_file = $name . '.' . $format;
        }
        return $export_file;
    }

    public function getExportFile($id, $format = 'pdf', $params = []) {
        $export_file = null;
        $jasper_file = $this->compileJasper($id);
        if(!is_null($jasper_file)) {
            $hash = $this->hashingParams($params);
            $output_file = $this->jasper_dir . DIRECTORY_SEPARATOR . $hash;
            $export_file = $this->processJasper($id, $jasper_file, $output_file, $format, $params);
            if(!is_null($export_file)) {
//                Helper::download($export_file);
                $this->saveJsonParams($output_file, $params);
                return $export_file;
            }
        }
    }

    private function hashingParams($params) {
        return hash('sha256', json_encode($params));
    }

    private function saveJsonParams($output_file, $params) {
        $json_file = fopen($output_file . '.json', 'w');
        fwrite($json_file, json_encode($params));
        fclose($json_file);
    }
}