<?php

CakePlugin::routes();

//Misc
Router::connect('/location/openHours'           , ['controller' => 'location'       , 'action' => 'openHours'           , '[method]' => 'GET']);

//Metrics
Router::connect('/location/avgTicket'           , ['controller' => 'location'       , 'action' => 'avgTicket'           , '[method]' => 'GET']);
Router::connect('/location/conversionRate'      , ['controller' => 'location'       , 'action' => 'conversionRate'      , '[method]' => 'GET']);
Router::connect('/location/dwell'               , ['controller' => 'location'       , 'action' => 'dwell'               , '[method]' => 'GET']);
Router::connect('/location/footTraffic'         , ['controller' => 'location'       , 'action' => 'footTraffic'         , '[method]' => 'GET']);
Router::connect('/location/itemsPerTransaction' , ['controller' => 'location'       , 'action' => 'itemsPerTransaction' , '[method]' => 'GET']);
Router::connect('/location/totalItems'          , ['controller' => 'location'       , 'action' => 'totalItems'          , '[method]' => 'GET']);
Router::connect('/location/totals'              , ['controller' => 'location'       , 'action' => 'totals'              , '[method]' => 'GET']);
Router::connect('/location/transactions'        , ['controller' => 'location'       , 'action' => 'transactions'        , '[method]' => 'GET']);
Router::connect('/location/revenue'             , ['controller' => 'location'       , 'action' => 'revenue'             , '[method]' => 'GET']);
Router::connect('/location/returning'           , ['controller' => 'location'       , 'action' => 'returning'           , '[method]' => 'GET']);
Router::connect('/location/sensorTraffic'       , ['controller' => 'location'       , 'action' => 'sensorTraffic'       , '[method]' => 'GET']);
Router::connect('/location/walkbys'             , ['controller' => 'location'       , 'action' => 'walkbys'             , '[method]' => 'GET']);
Router::connect('/location/windowConversion'    , ['controller' => 'location'       , 'action' => 'windowConversion'    , '[method]' => 'GET']);

//Network Analytics
Router::connect('/network/emails'               , ['controller' => 'network'        , 'action' => 'emails'              , '[method]' => 'GET']);
Router::connect('/network/emailsCaptured'       , ['controller' => 'network'        , 'action' => 'emailsCaptured'      , '[method]' => 'GET']);
Router::connect('/network/websites'             , ['controller' => 'network'        , 'action' => 'websites'            , '[method]' => 'GET']);
Router::connect('/network/wifiConnections'      , ['controller' => 'network'        , 'action' => 'wifiConnections'     , '[method]' => 'GET']);

//Maintenance
Router::connect('/server_health/ok'             , ['controller' => 'serverHealth'   , 'action' => 'ok'                  , '[method]' => 'GET']);

//Customer Profiles
Router::connect('/brands'                       , ['controller' => 'Brand'          , 'action' => 'brands'              , '[method]' => 'GET']);
Router::connect('/categories'                   , ['controller' => 'Category'       , 'action' => 'categories'          , '[method]' => 'GET']);
Router::connect('/customer'                     , ['controller' => 'Customer'       , 'action' => 'customer'            , '[method]' => 'GET']);
Router::connect('/customers'                    , ['controller' => 'Customer'       , 'action' => 'customers'           , '[method]' => 'GET']);
Router::connect('/location/highlights'          , ['controller' => 'Location'       , 'action' => 'highlights'          , '[method]' => 'GET']);

//User
Router::connect('/user/getSettings'             , ['controller' => 'user'           , 'action' => 'getSettings'         , '[method]' => 'GET']);
Router::connect('/user/locations'               , ['controller' => 'user'           , 'action' => 'locations'           , '[method]' => 'GET']);
Router::connect('/user/login'                   , ['controller' => 'user'           , 'action' => 'login'               , '[method]' => 'POST']);
Router::connect('/user/register'                , ['controller' => 'user'           , 'action' => 'register'            , '[method]' => 'POST']);
Router::connect('/user/updatePassword'          , ['controller' => 'user'           , 'action' => 'updatePassword'      , '[method]' => 'POST']);
Router::connect('/user/updateSettings'          , ['controller' => 'user'           , 'action' => 'updateSettings'      , '[method]' => 'POST']);

//iOS App
Router::connect('/location/availableSettings'   , ['controller' => 'location'       , 'action' => 'availableSettings'   , '[method]' => 'GET']);
Router::connect('/location/getSettings'         , ['controller' => 'location'       , 'action' => 'getSettings'         , '[method]' => 'GET']);
Router::connect('/location/create'              , ['controller' => 'location'       , 'action' => 'create'              , '[method]' => 'POST']);
Router::connect('/location/updateSettings'      , ['controller' => 'location'       , 'action' => 'updateSettings'      , '[method]' => 'POST']);
Router::connect('/portal/visitorEvent'          , ['controller' => 'portal'         , 'action' => 'visitorEvent'        , '[method]' => 'POST']);

Router::connect('/location/monthlyTotals'       , ['controller' => 'location'       , 'action' => 'monthlyTotals'       , '[method]' => 'GET']);
Router::connect('/location/historicalTotals'    , ['controller' => 'location'       , 'action' => 'historicalTotals'    , '[method]' => 'GET']);
Router::connect('/device/checkForUpdates'       , ['controller' => 'Device'         , 'action' => 'checkForUpdates'     , '[method]' => 'GET']);
Router::connect('/device/status'                , ['controller' => 'Device'         , 'action' => 'getStatus'           , '[method]' => 'GET']);
Router::connect('/device/assign'                , ['controller' => 'Device'         , 'action' => 'assign'              , '[method]' => 'POST']);
Router::connect('/device/status'                , ['controller' => 'Device'         , 'action' => 'setStatus'           , '[method]' => 'POST']);

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
//require CAKE . 'Config' . DS . 'routes.php';
