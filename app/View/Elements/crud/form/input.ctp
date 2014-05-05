<?php        
    switch ($field){
        case 'password':
            $value = '';
            $type = 'password';
            break;
        case 'description':                       
            $type = 'textarea';
            break;
        default:            
            $type = 'text';
            break;
    }
    
    switch ($type){            
        case 'password':
            ?>
            <input name="<?= $cModel . '.' . $field ?>" 
                   class="form-control" 
                   type="password" 
                   value="<?= $value ?>">
            <?php
            break;
        case 'textarea':
            ?>
            <textarea name="<?= $cModel . '.' . $field ?>" 
                      class="form-control"><?=$value?></textarea> 
            <?php
            break;
        default:
            ?>
            <input name="<?= $cModel . '.' . $field ?>" 
                   class="form-control" 
                   type="text" 
                   value="<?= $value ?>">
            <?php
            break;
    }
?>         