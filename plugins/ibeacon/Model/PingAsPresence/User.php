<?php

App::uses('Model', 'Model');
class User extends Model {

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