<?php

App::uses('AppModel', 'Model');
App::uses('Setting', 'Model');
App::uses('VisitorEvent', 'Model/Portal');

class LocationSetting extends AppModel
{

    public function __construct ($id = false, $table = null, $ds = null)
    {
        $this->setValidationFields(['location_id']);
        parent::__construct($id, $table, $ds);
    }

    public $useDbConfig  = 'backstage';
    public $useTable     = 'location_setting';
    public $displayField = 'id';
    public $id           = 'id';
    public $locationId   = null;

    const ADDRESS1                      = 1;
    const ADDRESS2                      = 2;
    const CITY                          = 3;
    const STATE                         = 4;
    const ZIPCODE                       = 5;
    const NETWORK_ID                    = 6;
    const TIMEZONE                      = 7;
    const EMAIL_GATE_WIFI               = 8;
    const TERMS_DISAGREE                = 9;
    const STRIPE_CUSTOMER_ID            = 10;
    const POS_STORE_ID                  = 11;
    const MONDAY_OPEN                   = 12;
    const MONDAY_CLOSE                  = 13;
    const TUESDAY_OPEN                  = 14;
    const TUESDAY_CLOSE                 = 15;
    const WEDNESDAY_OPEN                = 16;
    const WEDNESDAY_CLOSE               = 17;
    const THURSDAY_OPEN                 = 18;
    const THURSDAY_CLOSE                = 19;
    const FRIDAY_OPEN                   = 20;
    const FRIDAY_CLOSE                  = 21;
    const SATURDAY_OPEN                 = 22;
    const SATURDAY_CLOSE                = 23;
    const SUNDAY_OPEN                   = 24;
    const SUNDAY_CLOSE                  = 25;
    const INDUSTRY                      = 26;
    const CURRENCY                      = 27;
    const CUSTOMER_TERMS                = 28;
    const NETWORK_PROVIDER              = 29;
    const REGISTER_FILTER               = 30;
    const OUTLET_FILTER                 = 31;
    const COUNTRY                       = 32;
    const NIGHTCLUB_HOURS               = 33;
    const CORRECTION_PERCENTAGE         = 34;
    const EMAIL_GATE_TEXT               = 35;
    const UNIT_OF_MEASUREMENT           = 36;
    const SIZE_OF_STORE                 = 37;
    const DISTANCE_TO_FRONT_DOOR        = 38;
    const DISTANCE_TO_LEFT_WALL         = 39;
    const DISTANCE_TO_RIGHT_WALL        = 40;
    const DISTANCE_TO_BACK_WALL         = 41;
    const SQUARE                        = 42;
    const ESTIMATED_DAILY_FOOT_TRAFFIC  = 43;
    const POS_PROVIDER                  = 44;
    const POS_API_KEY                   = 45;
    const GUEST_WIFI                    = 46;
    const WIFI_NAME                     = 47;
    const WIFI_PASSWORD                 = 48;
    const ACTUAL_WIFI_PASSWORD          = 49;
    const USE_INSTANT_APP               = 50;
    const INSTANT_APP_REDIRECT_URL      = 51;
    const MERAKI_ORG_VALIDATOR          = 52;
    const MERAKI_WIFI_NETWORK_NAME      = 53;
    const SETUP_ACCOUNT_COMPLETE        = 54;
    const SETUP_HOURS_COMPLETE          = 55;
    const SETUP_CALIBRATION_COMPLETE    = 56;
    const SETUP_POS_COMPLETE            = 57;
    const SETUP_WIRELESS_COMPLETE       = 58;
    const PHONE                         = 59;
    const POS_UPLINK_INSTALLED          = 60;
    const POS_LOGIN                     = 61;
    const POS_PASSWORD                  = 62;
    const POS_URL                       = 63;
    const CONNECT_SCREEN_VARIANT        = 64;
    const EMAIL_GATE_SUBJECT            = 65;
    const EMAIL_GATE_CONTENT            = 66;
    const NO_ROLLUPS                    = 67;
    const NO_CACHE                      = 68;
    const NIGHTCLUB_HOURS_LOCATION      = 69;
    const NAICS_CODE                    = 70;
    const IAB_TIER_1_CATEGORY           = 71;
    const IAB_TIER_2_CATEGORY           = 72;
    const BRAND_1                       = 73;
    const BRAND_2                       = 74;
    const BRAND_3                       = 75;
    const BRAND_4                       = 76;
    const BRAND_5                       = 77;
    const BRAND_6                       = 78;
    const BRAND_7                       = 79;
    const BRAND_8                       = 80;
    const BRAND_9                       = 81;
    const BRAND_10                      = 82;
    const POS_VERSION                   = 83;
    const OS_VERSION                    = 84;
    const TRANSACTIONS_WHILE_CLOSED     = 85;
    const WHITE_LABEL_DASHBOARD         = 86;
    const SLUG                          = 87;
    const AVATAR_FILENAME               = 88;
    const LOGO_FILENAME                 = 89;
    const CTCT_ACCESS_TOKEN             = 90;
    const CTCT_EXPIRES_IN               = 91;
    const CTCT_TOKEN_TYPE               = 92;
    const CTCT_USERNAME                 = 93;
    const FOOTTRAFFIC_DEFAULT_DEVICE    = 94;
    const CONVERSIONRATE_DEFAULT_DEVICE = 95;
    const BUSINESS_NAME                 = 96;
    const NUMBER_OF_LOCATIONS           = 97;
    const TYPE_OF_RETAIL                = 98;
    const ANNUAL_REVENUE                = 99;
    const NUMBER_OF_EMPLOYEES           = 100;
    const SUBSCRIPTION_START_DATE       = 101;
    const PACKAGE                       = 102;
    const BILLING_STATUS                = 103;
    const LAT                           = 104;
    const LNG                           = 105;

    public function create ($data = [], $filterKey = false)
    {
        parent::create($data, $filterKey);
        if ($this->validates()) {
            $this->locationId = $this->data[__CLASS__]['location_id'];
        }
    }

    public function getLocationId ()
    {
        return $this->locationId;
    }

    public function setLocationId ($locationId)
    {
        $this->locationId = $locationId;
        return $this;
    }

    public function getSettingValue ($settingId)
    {
        $locationSetting = $this->find('first', ['conditions' => [
                'LocationSetting.location_id' => $this->locationId,
                'LocationSetting.setting_id'  => $settingId,
        ]]);
        return (!empty($locationSetting)) ? $locationSetting['LocationSetting']['value'] : false;
    }

    public function getTimezone ()
    {
        $timezone = $this->getSettingValue(LocationSetting::TIMEZONE);
        $timezones = DateTimeZone::listIdentifiers();
        return in_array($timezone, $timezones)?$timezone:'America/Los_Angeles';        
    }

    public function getOpenHours ()
    {
        $days   = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $return = [];
        $openCloseByDay = [];
        $weekSettings = [];
        foreach ($days as $day) {            
            $weekSettings[] = constant('LocationSetting::' . strtoupper($day . '_open'));
            $weekSettings[] = constant('LocationSetting::' . strtoupper($day . '_close'));
        }
        $db    = $this->getDataSource();
        $query = [
            'table'      => 'location_setting',
            'alias'      => 'LocationSetting',
            'conditions' => [],
            'fields'     => [
                "LocationSetting.value",
                "Setting.name",                    
            ],                
            'joins'=>[
                [
                    'table'      => 'setting',
                    'alias'      => 'Setting',
                    'type'       => 'INNER',
                    'conditions' => [
                        'Setting.id = LocationSetting.setting_id',
                        'Setting.id' => $weekSettings,
                        'LocationSetting.location_id' => $this->getLocationId()
                    ]
                ]
            ]
        ];
        $querySQL = $db->buildStatement($query, $this);        
        $result   = $db->fetchAll($querySQL);        
        foreach ($result as $setting){
            list($day, $openClose) = explode('_', $setting['Setting']['name']);
            $openCloseByDay[$day][$openClose] = $setting['LocationSetting']['value'];
        }
        foreach ($days as $day) {            
            $open         = isset($openCloseByDay[$day])?$openCloseByDay[$day]['open']:'';
            $close        = isset($openCloseByDay[$day])?$openCloseByDay[$day]['close']:'';
            $return[$day] = [
                'isOpen' => $open !== '0' && $close !== '0',
                'open'   => $open === '0' || !empty($open) ? $open : '09:00',
                'close'  => $close === '0' || !empty($close) ? $close : '21:00'
            ];
        }
        return $return;
    }

    public function getFirstPurchaseDate ()
    {
        $model    = new Model(null, 'invoices', 'pos');
        $db       = $model->getDataSource();
        $query    = [
            'fields'     => ["DATE(CONVERT_TZ(ts,'GMT', '{$this->getTimezone()}')) as first_date"],
            'table'      => 'invoices',
            'alias'      => 'Invoices',
            'conditions' => [
                'store_id' => $this->getSettingValue(LocationSetting::POS_STORE_ID),
                'ts > '    => '2013-01-01 00:00:00',
                'ts IS NOT NULL'
            ],
            'order'      => ['ts ASC'],
            'limit'      => 1
        ];
        $querySQL = $db->buildStatement($query, $model);
        $result   = $db->fetchAll($querySQL);
        return (isset($result[0])) ? $result[0][0]['first_date'] : null;
    }

    public function getFirstSessionDate ()
    {
        $model    = new Model(null, 'sessions', 'swarmdata');
        $db       = $model->getDataSource();
        $query    = [
            'fields'     => ["DATE(CONVERT_TZ(time_login,'GMT', '{$this->getTimezone()}')) as first_date"],
            'table'      => 'sessions',
            'alias'      => 'Sessions',
            'conditions' => [
                'network_id'    => $this->getSettingValue(LocationSetting::NETWORK_ID),
                'time_login > ' => '2013-01-01 00:00:00',
                'time_login IS NOT NULL'
            ],
            'order'      => ['time_login ASC'],
            'limit'      => 1
        ];
        $querySQL = $db->buildStatement($query, $model);
        $result   = $db->fetchAll($querySQL);
        return (isset($result[0])) ? $result[0][0]['first_date'] : null;
    }

    public function getFirstSensorDate ()
    {
        $model    = new Model(null, 'visitorEvent', 'portal');
        $db       = $model->getDataSource();
        $query    = [
            'fields'     => ["DATE(CONVERT_TZ(ts,'GMT', '{$this->getTimezone()}')) as first_date"],
            'table'      => 'visitorEvent',
            'alias'      => 'VisitorEvent',
            'conditions' => [
                'location_id' => $this->getLocationId(),
                'ts > '       => '2014-07-24 00:00:00',
                'ts IS NOT NULL'
            ],
            'order'      => ['ts ASC'],
            'limit'      => 1
        ];
        $querySQL = $db->buildStatement($query, $model);
        $result   = $db->fetchAll($querySQL);
        return (isset($result[0])) ? $result[0][0]['first_date'] : null;
    }

    public function getDeviceTypesAssociated ()
    {
        $return = [];

        //Check if have portal
        $visitorEvent = new VisitorEvent();
        $count        = $visitorEvent->find('count', 
            [
                'conditions' => [
                    'location_id' => $this->getLocationId()
                ]   
            ]
        );
        if ($count > 0) {
            $return[] = 'portal';
        }

        //Check if have presence        
        if ($this->getSettingValue(LocationSetting::NETWORK_ID)) {
            $return[] = 'presence';
        }
        return $return;
    }

}
