<?php

App::uses('Model', 'Model');
App::uses('Invoice', 'Model');

class LocationController extends AppController
{

    public $uses = ['Invoice'];

    public function highlights ()
    {
        $this->autoRender = false;
        $this->layout = 'json';
        $this->set('result', [
            'Biggest Ticket' => $this->Invoice->biggestTicket(),
            'Best Hour' => $this->Invoice->bestHour(),
            'Best Day' => $this->Invoice->bestDay()
        ]);
    }

}
