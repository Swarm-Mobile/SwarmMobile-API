<?php

require_once __DIR__ . '/Component/CompressedFunctions.php';

App::uses('Model', 'Model');
App::uses('Location', 'Model');
App::uses('Category', 'Model');

class CategoryController extends AppController
{

    public $uses = ['Location','Category'];

    public function categories ()
    {        
        $this->layout = 'blank';
        try {           
            $this->Location->readFromParams($this->params->query, 1);
            $storeId = settVal('pos_store_id', $this->Location->data['Setting']);
            if (empty($storeId)) {
                throw new InvalidArgumentException("Incorrect location_id");
            }
            $categories = $this->Category->find('all', [
                'fields'     => ['DISTINCT Category.name'],
                'conditions' => [
                    'Category.store_id' => $storeId,
                    'Category.name !=' => ['']
                ]                
            ]);
            $result = [];
            if (!empty($categories)) {
                foreach ($categories as $category) {                    
                    $result[] = ucwords(strtolower($category['Category']['name']));
                }
            }
        }
        catch (InvalidArgumentException $e) {
            $result = ['error' => $e->getMessage()];
        }
        $this->set('result',$result);
        $this->render('/API/json');
    }
    
    public function beforeFilter ()
    {
        $this->Auth->allow('categories');
    }

}
