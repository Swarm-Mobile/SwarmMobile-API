<?php

require_once __DIR__ . '/Component/CompressedFunctions.php';

App::uses('Model', 'Model');
App::uses('Location', 'Model');
App::uses('Category', 'Model');

class CategoryController extends AppController
{

    public $uses = ['Location', 'Category'];

    public function categories ()
    {
        $this->Location->readFromParams($this->params->query, 1);
        $storeId = settVal('pos_store_id', $this->Location->data['Setting']);
        if (empty($storeId)) {
            throw new Exception("Incorrect location_id");
        }
        $categories = $this->Category->find('all', [
            'fields'     => ['DISTINCT Category.name'],
            'conditions' => [
                'Category.store_id' => $storeId,
                'Category.name !='  => ['']
            ]
        ]);
        $result     = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $result[] = ucwords(strtolower($category['Category']['name']));
            }
        }
        return new JsonResponse(['body' => json_encode($result)]);
    }

}
