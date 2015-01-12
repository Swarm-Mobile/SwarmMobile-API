<?php

App::uses('AppController', 'Controller');

class ServerHealthController extends AppController
{

    public function ok ()
    {
        return new CakeResponse(['body' => 'Server Health Success.']);
    }

}
