<?php

App::uses('AppShell', 'Console/Command');

class CleanCacheShell extends AppShell {

    private function clean($path, $seconds) {        
        $files = scandir($path);
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..'))) {
                if (is_dir($path . $file)) {
                    $this->clean($path . $file.DS, $seconds);
                } else {
                    if (time() - filemtime($path . $file) >= $seconds) {
                        unlink($path . $file);
                    }
                }
            }
        }
    }

    public function main() {
        $minutes = (!isset($this->params['grace_time'])) ? 5 : $this->params['grace_time'];
        $seconds = $minutes * 60;
        $cache_folder = ROOT . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS . 'api_calls' . DS;
        if (file_exists($cache_folder)) {
            $this->clean($cache_folder, $seconds);
        }

        $this->out("\nDone!");
    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->addOption('grace_time', array(
            'short' => 't',
            'default' => '5',
            'help' => "Number of minutes of grace (0 to remove all)."
        ));
        return $parser;
    }

}
