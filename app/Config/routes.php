<?php

CakePlugin::routes();

//Misc
Router::connect('/location/openHours'           , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);

//Metrics
Router::connect('/location/avgTicket'           , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/conversionRate'      , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/dwell'               , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/footTraffic'         , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/itemsPerTransaction' , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/totalItems'          , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/totals'              , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/transactions'        , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/revenue'             , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/returning'           , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/sensorTraffic'       , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/walkbys'             , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/windowConversion'    , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);

//Network Analytics
Router::connect('/network/emails'               , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/network/emailsCaptured'       , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/network/websites'             , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/network/wifiConnections'      , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);

//Maintenance
Router::connect('/server_health/ok'             , ['controller' => 'serverHealth' , 'action' => 'ok'               , '[method]' => 'GET']);

//Customer Profiles
Router::connect('/brands'                       , ['controller' => 'Brand'        , 'action' => 'brands'           , '[method]' => 'GET']);
Router::connect('/categories'                   , ['controller' => 'Category'     , 'action' => 'categories'       , '[method]' => 'GET']);
Router::connect('/customer'                     , ['controller' => 'Customer'     , 'action' => 'customer'         , '[method]' => 'GET']);
Router::connect('/customers'                    , ['controller' => 'Customer'     , 'action' => 'customers'        , '[method]' => 'GET']);
Router::connect('/location/highlights'          , ['controller' => 'Location'     , 'action' => 'highlights'       , '[method]' => 'GET']);

//User
Router::connect('/user/getSettings'             , ['controller' => 'User'         , 'action' => 'getSettings'      , '[method]' => 'GET']);
Router::connect('/user/locations'               , ['controller' => 'User'         , 'action' => 'locations'        , '[method]' => 'GET']);
Router::connect('/user/login'                   , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'POST']);
Router::connect('/user/register'                , ['controller' => 'User'         , 'action' => 'register'         , '[method]' => 'POST']);
Router::connect('/user/updatePassword'          , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'POST']);
Router::connect('/user/updateSettings'          , ['controller' => 'User'         , 'action' => 'updateSettings'   , '[method]' => 'POST']);
Router::connect('/user/getLocationManagerId'    , ['controller' => 'User'          , 'action' => 'getLocationManagerId'            , '[method]' => 'GET']);

//iOS App
Router::connect('/location/availableSettings'   , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/getSettings'         , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'GET']);
Router::connect('/location/create'              , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'POST']);
Router::connect('/location/updateSettings'      , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'POST']);
Router::connect('/portal/visitorEvent'          , ['controller' => 'Api'          , 'action' => 'index'            , '[method]' => 'POST']);

Router::connect('/location/monthlyTotals'       , ['controller' => 'Api'          , 'action' => 'monthlyTotals'    , '[method]' => 'GET']);
Router::connect('/location/historicalTotals'    , ['controller' => 'Api'          , 'action' => 'historicalTotals' , '[method]' => 'GET']);
Router::connect('/device/checkForUpdates'       , ['controller' => 'Device'       , 'action' => 'checkForUpdates'  , '[method]' => 'GET']);
Router::connect('/device/status'                , ['controller' => 'Device'       , 'action' => 'getStatus'        , '[method]' => 'GET']);
Router::connect('/device/assign'                , ['controller' => 'Device'       , 'action' => 'assign'           , '[method]' => 'POST']);
Router::connect('/device/status'                , ['controller' => 'Device'       , 'action' => 'setStatus'        , '[method]' => 'POST']);

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
//require CAKE . 'Config' . DS . 'routes.php';
