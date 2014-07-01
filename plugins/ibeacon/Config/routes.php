<?php

    //POST /services/my/coupon/{couponId}
    Router::connect(
        '/services/my/coupon/:couponId',
        array(
            'controller' => 'CouponsRest',
            'action' => 'confirmation',
            '[method]' => 'POST',
        ),
        array(
            'couponId' => '[0-9]+',
            'pass' => array('couponId')
        )
    );
    //GET /services/coupon/campaign/4?userid=1
    Router::connect(
        '/services/coupon/campaign/:campaignId',
        array(
            'controller' => 'CouponsRest',
            'action' => 'createByCampaigningId',
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
        '/services/api/login',
        array(
            'controller' => 'CustomersRest',
            'action' => 'login',
            '[method]' => 'POST'
         )
    );