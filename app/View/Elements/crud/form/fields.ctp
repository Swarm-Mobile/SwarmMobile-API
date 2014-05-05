<?php foreach ($fields as $cModel => $cFields) { ?>
    <div class="row">
        <h3 style="margin-bottom:20px"><?= ucfirst($cModel) ?></h3>
        <?php foreach ($cFields as $field) { ?>
            <div class="form-group">
                <label for="<?= $field ?>" class="col-lg-2 control-label">
                    <?= ucwords(str_replace('_', ' ', $field)) ?>
                </label>
                <div class="col-lg-10">
                    <?= 
                        $this->element('crud/form/input', 
                            array(
                                'cModel' => $cModel,
                                'field'  => $field, 
                                'value'  => $data[$cModel][$field],                                
                            )
                        ); 
                    ?>                 
                </div>
            </div>
        <?php } ?>
        <?php
        $kId = (isset($data[$cModel]['id'])) ? 'id' : strtolower($cModel) . '_id';
        $id = $data[$cModel][$kId];
        ?>
        <input type="hidden" name="<?= $cModel . '.' . $kId ?>" value="<?= $data[$cModel][$kId] ?>">
    </div>
<?php } ?>