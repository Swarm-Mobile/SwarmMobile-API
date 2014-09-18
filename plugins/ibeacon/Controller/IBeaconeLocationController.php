<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IBeaconeLocationController
 *
 * @author Zotov Maxim <zotov_mv@groupbwt.com>
 */

App::uses('IBeaconController', 'ibeacon.Controller');

App::uses('IBeaconDeviceCoordinate', 'ibeacon.Model');

class IBeaconeLocationController extends  IBeaconController {

    /**
     *
     */
    public function whereAmI (){
        $locationModel = new IBeaconLocation();
        $deviceCoordinateModel = new IBeaconDeviceCoordinate();
        $LocationIdentifierList = $this->request->data['locations'];
        $response = array();
        foreach ($LocationIdentifierList as $LocationIdentifier){
            $LocationIdentifierList['customerSwarmId'] = $_GET['userid'];
            $this->IBeacon->logging('whereAmI',  array_merge($LocationIdentifier,$_GET));
            $locations = $locationModel->findByUUID(
                    $LocationIdentifier['uuid'],
                    $LocationIdentifier['major'],
                    $LocationIdentifier['minor']
            );
            foreach ($locations as $location){
                if(isset($location['IBeaconLocation']) && !empty($location['IBeaconLocation'])){
                    $deviceCoordinateModel->addNew(
                            $LocationIdentifier['latitude'],
                            $LocationIdentifier['longitude'],
                            $location['d']['id']
                    );
                    unset($location['d']['id']);
                    $location = array_merge($LocationIdentifier,$location['IBeaconLocation']);
                    $brands = $locationModel->findBrandById($location['id']);

                    $categorys = $locationModel->findCategoryById($location['id']);
                    $location['brands'] = array(
                        'list' => array_values($brands)
                    );
                    $location['categorization'] = $categorys;
                    $response['locations'][] = $location;
                }
            }
        }
        echo json_encode($response);exit;
    }

}