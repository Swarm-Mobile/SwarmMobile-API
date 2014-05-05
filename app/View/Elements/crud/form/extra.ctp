<?php if (!empty($tables)) { ?>
    <div class="row">
        <h3>Extra Information</h3>
        <?php foreach ($tables as $cModel => $cFields) { ?>
            <h4 style="margin:30px 0"><?= ucfirst($cModel) ?></h4>
            <table clasS='table table-striped'>
                <thead>
                    <tr>
                        <?php foreach ($cFields as $field) { ?>
                            <th> <?= ucwords(str_replace('_', ' ', $field)) ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables_data[$cModel] as $oRow) { ?>
                        <tr>
                            <?php foreach ($cFields as $field) { ?>
                                <td><?= $oRow[$field] ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>                                                                            
            </table>
        <?php } ?>
    </div>
<?php } ?>        