<?php

    //POST /my/coupon/{couponId}
    Router::connect(
        '/my/coupon/:couponId',
        array(
            'plugin' => 'ibeacon',
            'controller' => 'IBeaconCoupons',
            'action' => 'reactToCoupon',
            '[method]' => 'POST',
        ),
        array(
            'couponId' => '[0-9]+',
            'pass' => array('couponId')
        )
    );
    //GET /coupon/campaign/4?userid=1
    Router::connect(
        '/coupon/campaign/:campaignId',
        array(
            'plugin' => 'ibeacon',
            'controller' => 'IBeaconCoupons',
            'action' => 'couponForCampaign',
            '[method]' => 'GET'
        ),
        array(
            'campaignId' => '[0-9]+',
            'pass' => array('campaignId')
        )
    );

    /**
     * Customer Controller
     *
     */
    //POST /services/api/login
    Router::connect(
        '/api/login',
        array(
            'plugin' => 'ibeacon',
            'controller' => 'IBeaconCustomers',
            'action' => 'login',
            '[method]' => 'POST'
         )
    );

    //POST /what_is_here?userid=1
    Router::connect(
        '/what_is_here',
        array(
            'plugin' => 'ibeacon',
            'controller' => 'IBeaconCoupons',
            'action' => 'whatIsHere',
            '[method]' => 'POST'
         )
    );
    //POST /where_am_i
    Router::connect(
        '/where_am_i',
        array(
            'plugin' => 'ibeacon',
            'controller' => 'IBeaconeLocation',
            'action' => 'whereAmI',
            '[method]' => 'POST'
        )
    );
    Router::connect(
        '/test',
        array(
            'plugin' => 'ibeacon',
            'controller' => 'IBeacon',
            'action' => 'index',
            '[method]' => 'GET'
         )
    );