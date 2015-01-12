<?php

CakePlugin::routes();

//SDK
Router::connect('/what_is_here'                 , ['controller' => 'SDK'          , 'action' => 'whatIsHere'            , '[method]' => 'POST']);


//Misc
Router::connect('/location/openHours'           , ['controller' => 'Location'     , 'action' => 'openHours'             , '[method]' => 'GET']);

//Metrics
Router::connect('/location/avgTicket'           , ['controller' => 'Metric'       , 'action' => 'avgTicket'             , '[method]' => 'GET']);
Router::connect('/location/conversionRate'      , ['controller' => 'Metric'       , 'action' => 'conversionRate'        , '[method]' => 'GET']);
Router::connect('/location/dwell'               , ['controller' => 'Metric'       , 'action' => 'dwell'                 , '[method]' => 'GET']);
Router::connect('/location/footTraffic'         , ['controller' => 'Metric'       , 'action' => 'footTraffic'           , '[method]' => 'GET']);
Router::connect('/location/itemsPerTransaction' , ['controller' => 'Metric'       , 'action' => 'itemsPerTransaction'   , '[method]' => 'GET']);
Router::connect('/location/totalItems'          , ['controller' => 'Metric'       , 'action' => 'totalItems'            , '[method]' => 'GET']);
Router::connect('/location/totals'              , ['controller' => 'Metric'       , 'action' => 'totals'                , '[method]' => 'GET']);
Router::connect('/location/transactions'        , ['controller' => 'Metric'       , 'action' => 'transactions'          , '[method]' => 'GET']);
Router::connect('/location/revenue'             , ['controller' => 'Metric'       , 'action' => 'revenue'               , '[method]' => 'GET']);
Router::connect('/location/returning'           , ['controller' => 'Metric'       , 'action' => 'returning'             , '[method]' => 'GET']);
Router::connect('/location/sensorTraffic'       , ['controller' => 'Metric'       , 'action' => 'portalTraffic'         , '[method]' => 'GET']);
Router::connect('/location/walkbys'             , ['controller' => 'Metric'       , 'action' => 'walkbys'               , '[method]' => 'GET']);
Router::connect('/location/windowConversion'    , ['controller' => 'Metric'       , 'action' => 'windowConversion'      , '[method]' => 'GET']);

//Network Analytics
Router::connect('/network/emails'               , ['controller' => 'Network'      , 'action' => 'emails'                , '[method]' => 'GET']);
Router::connect('/network/emailsCaptured'       , ['controller' => 'Network'      , 'action' => 'emailsCaptured'        , '[method]' => 'GET']);
Router::connect('/network/websites'             , ['controller' => 'Network'      , 'action' => 'websites'              , '[method]' => 'GET']);
Router::connect('/network/wifiConnections'      , ['controller' => 'Network'      , 'action' => 'wifiConnections'       , '[method]' => 'GET']);

//Maintenance
Router::connect('/server_health/ok'             , ['controller' => 'serverHealth' , 'action' => 'ok'                    , '[method]' => 'GET']);

//Customer Profiles
Router::connect('/brands'                       , ['controller' => 'Brand'        , 'action' => 'brands'                , '[method]' => 'GET']);
Router::connect('/categories'                   , ['controller' => 'Category'     , 'action' => 'categories'            , '[method]' => 'GET']);
Router::connect('/customer'                     , ['controller' => 'Customer'     , 'action' => 'customer'              , '[method]' => 'GET']);
Router::connect('/customers'                    , ['controller' => 'Customer'     , 'action' => 'customers'             , '[method]' => 'GET']);
Router::connect('/location/highlights'          , ['controller' => 'Location'     , 'action' => 'highlights'            , '[method]' => 'GET']);

//User
Router::connect('/user/getSettings'             , ['controller' => 'User'         , 'action' => 'getSettings'           , '[method]' => 'GET']);
Router::connect('/user/locations'               , ['controller' => 'User'         , 'action' => 'locations'             , '[method]' => 'GET']);
Router::connect('/user/login'                   , ['controller' => 'User'         , 'action' => 'login'                 , '[method]' => 'POST']);
Router::connect('/user/register'                , ['controller' => 'User'         , 'action' => 'register'              , '[method]' => 'POST']);
Router::connect('/user/updatePassword'          , ['controller' => 'User'         , 'action' => 'updatePassword'        , '[method]' => 'POST']);
Router::connect('/user/updateSettings'          , ['controller' => 'User'         , 'action' => 'updateSettings'        , '[method]' => 'POST']);

//iOS App
Router::connect('/location/availableSettings'   , ['controller' => 'Location'     , 'action' => 'availableSettings'     , '[method]' => 'GET']);
Router::connect('/location/getSettings'         , ['controller' => 'Location'     , 'action' => 'getSettings'           , '[method]' => 'GET']);
Router::connect('/location/create'              , ['controller' => 'Location'     , 'action' => 'create'                , '[method]' => 'POST']);
Router::connect('/location/updateSettings'      , ['controller' => 'Location'     , 'action' => 'updateSettings'        , '[method]' => 'POST']);
Router::connect('/portal/visitorEvent'          , ['controller' => 'Portal'       , 'action' => 'visitorEvent'          , '[method]' => 'POST']);

Router::connect('/location/monthlyTotals'       , ['controller' => 'Metric'       , 'action' => 'monthlyTotals'         , '[method]' => 'GET']);
Router::connect('/location/historicalTotals'    , ['controller' => 'Metric'       , 'action' => 'historicalTotals'      , '[method]' => 'GET']);
Router::connect('/device/checkForUpdates'       , ['controller' => 'Device'       , 'action' => 'checkForUpdates'       , '[method]' => 'GET']);
Router::connect('/device/status'                , ['controller' => 'Device'       , 'action' => 'getStatus'             , '[method]' => 'GET']);
Router::connect('/device/assign'                , ['controller' => 'Device'       , 'action' => 'assign'                , '[method]' => 'POST']);
Router::connect('/device/status'                , ['controller' => 'Device'       , 'action' => 'setStatus'             , '[method]' => 'POST']);

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
//require CAKE . 'Config' . DS . 'routes.php';
