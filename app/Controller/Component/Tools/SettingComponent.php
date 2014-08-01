<?php

class SettingComponent {

    protected static $searches;

    public static function get($setting_name, $setting_array) {
        if (!empty($setting_array[0]['LocationSetting']['location_id'])) {
            $location_id = $setting_array[0]['LocationSetting']['location_id'];
            if (empty(self::$searches[$location_id][$setting_name])) {
                self::$searches[$location_id][$setting_name] = array(null, null);
                foreach ($setting_array as $k => $val) {
                    if ($val['name'] == $setting_name) {
                        self::$searches[$location_id][$setting_name] = array(
                            $val['LocationSetting']['setting_id'],
                            $val['LocationSetting']['value']                                                        
                        );
                    }
                }
            }
            return self::$searches[$location_id][$setting_name];
        }
        return '';
    }

    public static function id($setting_name) {
        $oSetting = new Setting();
        $oSetting = $oSetting->find('first', array('conditions' => array('Setting.name' => $setting_name)));
        return (empty($oSetting)) ? false : $oSetting['Setting']['id'];
    }

    public static function value($setting_name, $setting_array) {
        $aResult = self::get($setting_name, $setting_array);
        return (isset($aResult[1]))?$aResult[1]:'';
    }

    public static function defaults($setting_name, $setting_array) {
        foreach ($setting_array as $k => $val) {
            if ($val['Setting']['name'] == $setting_name) {
                return $val['Setting']['default'];
            }
        }
    }

}
