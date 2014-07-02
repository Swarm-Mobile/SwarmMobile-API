<?php

    //POST /services/my/coupon/{couponId}
    Router::connect(
        '/my/coupon/:couponId',
        array(
            'controller' => 'IBeaconCouponsController',
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
            'controller' => 'IBeaconCustomersController',
            'action' => 'login',
            '[method]' => 'POST'
         )
    );