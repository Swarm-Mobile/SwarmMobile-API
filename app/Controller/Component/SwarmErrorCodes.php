<?php

class SwarmErrorCodes
{

    public static function getFirstError ($validationErrors)
    {
        $fields                                 = array_keys($validationErrors);
        $field                                  = array_shift($fields);
        $message                                = _($validationErrors[$field][0]);
        self::$messages[self::VALIDATION_ERROR] = $message;
        return self::VALIDATION_ERROR;
    }

    public static function setError ($message)
    {
        self::$messages[self::VALIDATION_ERROR] = $message;
        return self::VALIDATION_ERROR;
    }

    const VALIDATION_ERROR                               = 1000;
    const VALIDATOR_NOT_FOUND                            = 1001;
    const BRAND_BRANDS_LOCATION_NOTFOUND                 = 1002;
    const BRAND_BRANDS_STORE_NOTFOUND                    = 1003;
    const CATEGORY_CATEGORIES_LOCATION_NOTFOUND          = 1004;
    const CATEGORY_CATEGORIES_STORE_NOTFOUND             = 1005;
    const CUSTOMER_CUSTOMER_INVALID_CUSTOMER             = 1006;
    const CUSTOMER_CUSTOMERS_STORE_NOTFOUND              = 1007;
    const DEVICE_CHECKFORUPDATES_PORTAL_FIRMWARE_INVALID = 1008;
    const DEVICE_STATUS_DEVICE_NOTFOUND                  = 1009;
    const DEVICE_STATUS_DEVICE_LOCATION_MISMATCH         = 1010;
    const DEVICE_STATUS_DEVICE_TYPE_MISMATCH             = 1011;
    const LOCATION_HIGHLIGHTS_STORE_NOTFOUND             = 1012;
    const LOCATION_GETSETTINGS_LOCATION_NOTFOUND         = 1013;
    const LOCATION_UPDATESETTINGS_LOCATION_NOTFOUND      = 1014;
    const LOCATION_CREATE_USER_NOTFOUND                  = 1015;
    const USER_GETSETTINGS_USER_NOTFOUND                 = 1016;
    const USER_UPDATESETTINGS_USER_NOTFOUND              = 1017;
    const USER_UPDATEPASSWORD_USER_NOTFOUND              = 1018;
    const USER_UPDATEPASSWORD_PASSWORD_MISMATCH          = 1019;
    const USER_LOCATIONS_USER_NOTFOUND                   = 1020;
    const DEVICE_ASSIGN_DEVICE_CREATE                    = 1021;
    const DEVICE_ASSIGN_DEVICE_CREATE_ERROR              = 1022;
    const DUPLICATE_NAME_ADDRESS_COMBINATION             = 1023;
    const COUNTRY_NOT_FOUND            = 1024;
    const INVALID_CREDENTIALS          = 1025;
    const LOCATION_NOT_INITIALIZED     = 1026;
    const MODEL_NOT_INITIALIZED        = 1027;
    const METHOD_NOT_IMPLEMENTED       = 1028;
    const LOCATION_WITHOUT_NETWORK     = 1029;
    const INVALID_VALIDATOR_PARAM_TYPE = 1030;    

    public static $messages = [
        1000 => "",
        1001 => "",
        1002 => "Location does not exist in our system. Please contact your Account Manager",
        1003 => "Unable to find POS associated to the location. Please contact your Account Manager",
        1004 => "Location does not exist in our system. Please contact your Account Manager",
        1005 => "Unable to find POS store_id associated to the location. Please contact your Account Manager",
        1006 => "The customer does not exist in our system",
        1007 => "Unable to find POS store_id associated to the customer",
        1008 => "Invalid firmware version. Try again. If the problem persists please contact your Account Manager",
        1009 => "Our system cannot identify the device. If the problem persists please contact your Account Manager",
        1010 => "Possible device misconfiguration. Try again. If the problem persists contact your Account Manager",
        1011 => "Possible device misconfiguration. Try again. If the problem persists contact your Account Manager",
        1012 => "Unable to find POS associated to the location. Please contact your Account Manager",
        1013 => "Location does not exist in our system. Please contact your Account Manager",
        1014 => "Location does not exist in our system. Please contact your Account Manager",
        1015 => "Invalid user. Try singning in again. If the problem persists please contact your Account Manager",
        1016 => "Invalid user. Try singning in again. If the problem persists please contact your Account Manager",
        1017 => "Invalid user. Try singning in again. If the problem persists please contact your Account Manager",
        1018 => "Invalid user. Try singning in again. If the problem persists please contact your Account Manager",
        1019 => "Incorrect existing password. Please try again",
        1020 => "Invalid user. Try singning in again. If the problem persists please contact your Account Manager",
        1021 => "Missing device created via the API. This needs your urgent attention",
        1022 => "Unable to create missing device via the API. Please contact the engineering team for details",
        1023 => "Location name, address and city combination already exists in our records",
        1024 => "Country code does not exist in our database",
        1025 => "Invalid Credentials Supplied",
        1026 => "Location not initialized",
        1027 => "You must create() the model with correct data before execute this method",
        1028 => "Not Implemented",
        1029 => "No network presence on the selected location",        
    ];

}
