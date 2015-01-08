<?php

App::uses('Model', 'Model');
class PingUser extends Model {

    /**
     *
     * @var string
     */
    public $useTable = 'user';

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