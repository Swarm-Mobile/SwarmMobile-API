<?php

/**
 * AppShell file
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Shell', 'Console');

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell
{

    private $console = true;
    public $output   = true;

    public function output ($text, $breakLine = true)
    {
        if ($this->output) {
            if (!$breakLine) {
                $this->out("\r", (int) $breakLine);
                $this->out(str_repeat(" ", 80), (int) $breakLine);
                $this->out("\r", (int) $breakLine);
            }
            $this->out($text, (int) $breakLine);
            if (!$this->console) {
                echo $text . ($breakLine) ? "\n" : "\r";
            }
        }
    }

    public function coalesce ()
    {
        return array_shift(array_filter(func_get_args()));
    }

    public function setEnvironment ($env = false)
    {
        $cenv = (isset($_SERVER['server_location']))?$_SERVER['server_location']:'';
        if ($cenv != 'phpunit') {
            if (!$env) {
                $htaccess = file_get_contents(__DIR__ . '/../../../.htaccess');
                $pattern  = '/.*SetEnv server_location "(.*)"/';
                if (preg_match_all($pattern, $htaccess, $matches)) {
                    putenv('server_location=' . $matches[1][0]);
                    $_SERVER['server_location'] = $matches[1][0];
                }
            }
            else {
                $_SERVER['server_location'] = $env;
            }
        }
    }

}
