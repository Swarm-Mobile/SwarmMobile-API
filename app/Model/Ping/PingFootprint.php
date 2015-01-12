<?php

App::uses('Model', 'Model');
class PingFootprint extends Model {
    /**
     *
     * @var string
     */
    public $useTable = 'footprint';

    /**
     *
     * @var string
     */
    public $id = 'id';

    /**
     *
     * @var string
     */
    public $useDbConfig = 'pingAsPresence';
}