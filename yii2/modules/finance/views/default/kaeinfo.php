<?php 
use app\modules\finance\Module;
use app\modules\finance\components\Money;

$balance = Money::toCurrency($kaeCredit->kaecredit_amount)*Money::toPercentage($kaeCreditSumPercentage, false);
$balance_formatted = Money::toCurrency(Money::toCurrency($kaeCredit->kaecredit_amount)*Money::toPercentage($kaeCreditSumPercentage, false), true);
$withdrawalsSum = 0;
?>
<?php $collpased = ($options['collapsed'] == 1)? 'in': ''; ?>
<?php if($options['showbutton']) :?>
<p>
    <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#kaeInfo">
    	<?php echo Module::t('modules/finance/app', 'RCN {kae_id} - Quick Info', ['kae_id' => sprintf('%04d', $kae->kae_id)]);?>
    </button>
</p>
<?php endif;?>

<div id="kaeInfo" class="collapse <?= $collpased ?>">
    <div class="container-fluid well">
  		<div class="row">
        <table class="table table-hover">
            <thead><tr><th class="text-center" colspan="2"><?php echo "ΚΑΕ " . sprintf('%04d', $kae->kae_id) . " - " . $kae->kae_title  ?></th></tr></thead>
            <tr class="info"><td><?= Module::t('modules/finance/app', 'RCN Initial Credit') ?>:</td><td class="text-right"><?= Money::toCurrency($kaeCredit->kaecredit_amount, true) ?></td></tr>
            <tr class="info"><td><?= Module::t('modules/finance/app', 'Συνολικό Ποσοστό Διάθεσης') ?>:</td><td class="text-right"><?= Money::toPercentage($kaeCreditSumPercentage) ?></td></tr>
            <tr class="info"><td><?= Module::t('modules/finance/app', 'Διαθέσιμο ποσό') ?>:</td><td class="text-right"><?= $balance_formatted ?></td></tr>
            <?php foreach ($kaeWithdrwals as $withdrawal) :
            		$withdrawalsSum += $withdrawal->kaewithdr_amount; ?>
            		<tr class="danger"><td>Ανάληψη (<?= $withdrawal->kaewithdr_date ?>):</td><td class="text-right"><?= Money::toCurrency($withdrawal->kaewithdr_amount, true) ?></td></tr>
            <?php endforeach;?>
            <tr class="success"><td><strong>Διαθέσιμο Υπόλοιπο για Ανάληψη:</strong></td><td class="text-right"><strong><?= Money::toCurrency($balance - $withdrawalsSum, true) ?></strong></td></tr>	
        </table>
		</div>
	</div>
</div>