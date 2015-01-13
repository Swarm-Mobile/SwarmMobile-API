<?php 
    $vars = ['value','title','description','percentage', 'dataType', 'change'];
    foreach($vars as $var){       
        $tmp_var = $metric.'_'.$var;
        $$var = $$tmp_var;        
    }     
?>
<tr>
    <td width="70%">
        <h4>
            <strong><?= $this->MetricFormat->formatValue($value, $dataType, $oLocation) ?></strong> 
            <?=$title?>
        </h4>
        <p><?=$description?></p>
    </td>
    <td class="change <?= $this->MetricFormat->signColor($percentage) ?> text-center">
        <h4 style="<?= $this->MetricFormat->signColorInline($change) ?>">
            <?= $this->MetricFormat->signSymbol($change) ?> <?= abs($percentage) ?>%
        </h4>
        <p style="color:#000"><?= $this->MetricFormat->signVerbiage($change) ?> w/o/w</p>
    </td>
</tr>
