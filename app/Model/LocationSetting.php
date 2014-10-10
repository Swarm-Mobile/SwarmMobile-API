<?php
App::uses('AppModel', 'Model');
App::uses('Setting', 'Model');

class LocationSetting extends AppModel
{

    public $useDbConfig  = 'backstage';
    public $useTable     = 'location_setting';
    public $displayField = 'id';
    public $id           = 'id';
    public $locationId   = 0;
    public $settingId    = 0;

    public function getSettingValue ($setting, $locationId = null)
    {        
        if (empty($locationId)) {
            if (ValidatorComponent::isPositiveInt($locationId)) {
                $locationId = $this->locationId;
            } else {
                return false;
            }
        }
        
        if (ValidatorComponent::isPositiveInt($setting)) {
            $settingId = $setting;
        }
        else {
            $settingId = $this->getSettingIdByName($setting);
            if (!$settingId) {
                return false;
            }
        }

        $locationSetting = $this->find('first', ['conditions' => [
                'LocationSetting.location_id' => $locationId,
                'LocationSetting.setting_id'  => $settingId,
        ]]);

        return (!empty($locationSetting)) ? $locationSetting['LocationSetting']['value'] : false;
    }

    public function getSettingIdByName ($settingName)
    {
        $settingModel = new Setting();
        $setting      = $settingModel->find('first', ['conditions' => ['Setting.name' => $settingName]]);
        return (!empty($setting)) ? $setting['Setting']['id'] : false;
    }

}
