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

class Jasper extends JasperPHP
{
    public $jaspers = [];
    private $jasper_url;
    private $jasper_dir;
    protected $windows = false;

    public function __construct($jasper_dir)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            $this->windows = true;

        $this->jasper_url = DIRECTORY_SEPARATOR . 'repo' . DIRECTORY_SEPARATOR .'jasper';
        $this->jasper_dir = $jasper_dir;
        $this->initJasper();
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
        $params = $this->list_parameters($path);
        $this->jaspers[$name . '.' . $ext] = [
            'label' => $name . '.' . $ext,
            'path' => $path,
            'url' => $url,
            'params' => $params
        ];
    }

    public function getJasperItem() {
        return $this->jaspers;
    }

    public function getSupprotedFormat() {
        return $this->formats;
    }

    public function getJasperSelect() {
        $options = [];
        foreach ($this->jaspers as $k => $v) {
            $options[$k] = $v['label'];
        }

        return $options;
    }

    public function getJasperParams($id) {
        return $this->list_parameters($this->jaspers[$id]['path']);
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
                    $this->compile($jasper_file)->execute(true);
                } catch (\Exception $e) {
                    return null;
                }
            }
            $jasper_file = $name . '.jasper';
        }
        return $jasper_file;
    }

    private function processJasper($id, $file, $output_file, $format, $params, $url) {
        $export_file = $file;
        $exp = explode('.', $export_file);
        $ext = $exp[count($exp) - 1];
//        $name = $exp[count($exp) - 2];
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
                $export_format = ['html'];
                if($format != 'html') {
                    $export_format[] = $format;
                }
                $this->process($export_file, $output_file, $export_format, $params)->execute(true);
            } catch (\Exception $e) {
                return null;
            }
            $export_file = $output_file . '.' . $format;
            if($url) {
                $exp = explode(DIRECTORY_SEPARATOR, $export_file);
                $export_file = $exp[count($exp) - 1];
            }
        }
        return $export_file;
    }

    public function getExportFile($id, $format = 'pdf', $params = [], $url = false) {
        $export_file = null;
        $jasper_file = $this->compileJasper($id);
        if(!is_null($jasper_file)) {
            $hash = $this->hashingParams($params);
            $output_file = $this->jasper_dir . DIRECTORY_SEPARATOR . $hash;
            $export_file = $this->processJasper($id, $jasper_file, $output_file, $format, $params, $url);
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

    public function execute($run_as_user = false)
    {
        if( $this->redirect_output && !$this->windows)
            $this->the_command .= " 2>&1";

        if( $this->background && !$this->windows )
            $this->the_command .= " &";

        if( $run_as_user !== false && strlen($run_as_user) > 0 && !$this->windows )
            $this->the_command = "su -c \"{$this->the_command}\" {$run_as_user}";

        $output     = array();
        $return_var = 0;

        $command = str_replace('^\\', '\\', $this->the_command);
        exec($command, $output, $return_var);

        if( $return_var != 0 && isset($output[0]) )
            throw new \Exception($output[0], 1);

        elseif( $return_var != 0 )
            throw new \Exception("Your report has an error and couldn't be processed! Try to output the command using the function `output();` and run it manually in the console.", 1);

        return $output;
    }
}