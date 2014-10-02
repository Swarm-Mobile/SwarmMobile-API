<?php
use Aws\S3\S3Client;
App::uses('Component', 'Controller');

class S3FactoryComponent extends Component {
    public function loadS3(){
        $s3 = S3Client::factory(array(
            'key' => 'AKIAJK6CZ34NK3NLRJAQ',
            'secret' => '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'
        ));
        return $s3;
    }
}