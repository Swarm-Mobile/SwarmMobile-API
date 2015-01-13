<?php 
    $vars = ['title','range', 'value', 'suffix'];
    foreach($vars as $var){       
        $tmp_var = $metric.'_'.$var;        
        $$var = $$tmp_var;
    }   
?>
<tr>
    <td width="70%"><h4><?=$title?></h4></td>
    <td>
        <h4><?=$range?></h4>
        <h4><?=$value?> <?=$suffix?></h4>
    </td>
</tr>