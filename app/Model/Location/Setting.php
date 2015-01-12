<?php

App::uses('AppModel', 'Model');

class Setting extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'setting';
    public $displayField = 'name';
    public $id           = 'id';

    public function getDefaults ()
    {
        $displayField       = $this->displayField;
        $this->displayField = 'default';
        $defaults           = $this->find('all', [
            'conditions' => [
                'Setting.default IS NOT NULL',
                'Setting.default !=' => ''
            ]
        ]);
        $this->displayField = $displayField;
        return $defaults;
    }

    public function getSettingsByLocation ($locationId)
    {
        $db       = $this->getDataSource();
        $query    = [
            'fields' => [
                'Setting.name',
                'Setting.label',
                'LocationSetting.setting_id',
                'LocationSetting.value',
                'Setting.desc'
            ],
            'table'  => 'setting',
            'alias'  => 'Setting',
            'joins'  => [
                [
                    'table'      => 'location_setting',
                    'alias'      => 'LocationSetting',
                    'type'       => 'INNER',
                    'conditions' => [
                        'LocationSetting.setting_id = Setting.id',
                        'LocationSetting.location_id' => $locationId,
                    ]
                ]
            ]
        ];
        $querySQL = $db->buildStatement($query, $this);
        return $db->fetchAll($querySQL);
    }

}
