<?php
$this->assign('title', 'Swarm Mobile - Edit' . ucfirst($model));
?>
<div class="row" style="margin-bottom: 50px">
    <div class="col-md-12">
        <h2>Edit <?= $model ?> #<?= $data[$model]['id'] ?></h2>
        <form action="<?= Router::url('/' . $ctrl . '/save') ?>" 
              class="form-horizontal" role="form" method="post" >            
            <?= $this->element('crud/form/fields'); ?>        
            <button type="submit" class="btn btn-default pull-right caps">Submit</button>
            <a href="<?= Router::url('/' . $ctrl . '/view') ?>" 
               class="btn btn-primary pull-right caps" 
               style="margin-right:10px">Return</a>
        </form>
        <?= $this->element('crud/form/extra'); ?>        
    </div>
</div>