<?php
$this->assign('title', 'Swarm Mobile - View' . ucfirst($model));
?>
<div class="row">
    <div class="col-md-12">
        <h2>
            View <?= $model ?>
            <a href="<?= Router::url('/' . $ctrl . '/add') ?>" 
                type="button" 
                class="btn btn-primary btn-xl pull-right">
                 <span class="glyphicon glyphicon-plus-sign"></span> Add
             </a>
        </h2>
        <table clasS='table table-striped'>
            <thead>
                <tr>
                    <?php foreach ($fields as $cModel => $cFields) { ?>
                        <?php foreach ($cFields as $field) { ?>
                            <th><?= ucwords(str_replace('_', ' ', $field)) ?></th>
                        <?php } ?>
                    <?php } ?>
                    <th></th>
                </tr>
            </thead>        
            <tbody>
                <?php foreach ($data as $oRow) { ?>
                    <tr>
                        <?php foreach ($fields as $cModel => $cFields) { ?>
                            <?php foreach ($cFields as $field) { ?>
                                <td><?= $oRow[$cModel][$field] ?></td>
                            <?php } ?>
                        <?php } ?>
                        <td>
                            <a href="<?= Router::url('/' . $ctrl . '/edit?id=' . $oRow[$model]['id']) ?>" 
                               type="button" 
                               class="btn btn-success btn-sm">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </a>
                            <a href="<?= Router::url('/' . $ctrl . '/delete?id=' . $oRow[$model]['id']) ?>" 
                               type="button" 
                               class="btn btn-danger btn-sm">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>