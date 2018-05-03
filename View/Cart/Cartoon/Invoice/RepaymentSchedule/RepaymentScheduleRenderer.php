<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\RepaymentSchedule;


use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

class RepaymentScheduleRenderer extends BaseRenderer
{

    /**
     * https://github.com/lingtalfi/Models/blob/master/RepaymentSchedule/repayment-schedule.md
     */
    private $repaymentSchedule;


    public function __construct()
    {
        parent::__construct();

        $this->columns = [
            'repayment_schedule_label',
            'repayment_schedule_total',
        ];
    }

    public function setRepaymentSchedule(array $repaymentSchedule)
    {
        $this->repaymentSchedule = $repaymentSchedule;
        return $this;
    }


    public function render()
    {
        $debug = array_key_exists("debug", $_GET);
        $nbCols = 3;
        if ($debug) {
            $nbCols++;
        }
        if (false === $this->has("repayment_schedule_label")) {
            $nbCols--;
        }

        $model = $this->repaymentSchedule;
        ?>
        <div class="repayment-schedule">
            <div class="echeancier">
                <h4>Échéancier</h4>
                <table class="cartoon-columns">
                    <tr>
                        <?php if ($this->has("repayment_schedule_label")): ?>
                            <td>Libellé</td>
                        <?php endif; ?>
                        <td>Date de prélèvement</td>
                        <td>Montant</td>
                        <?php if (true === $debug): ?>
                            <td>Détails</td>
                        <?php endif; ?>
                    </tr>
                    <?php foreach ($model['items'] as $info):

                        if (false === $debug) {
                            $info['label'] = trim(explode('--', $info['label'])[0]);
                        }

                        ?>
                        <tr>
                            <?php if ($this->has("repayment_schedule_label")): ?>
                                <td><?php echo $info['label']; ?></td>
                            <?php endif; ?>
                            <td><?php echo date("Y-m-d", $info['time']); ?></td>
                            <td><?php echo $info['price']; ?></td>
                            <?php if (true === $debug): ?>
                                <td>
                                    <?php if (array_key_exists("details", $info)): ?>
                                        <pre>
                                        <?php a($info['details']); ?>
                                    </pre>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($this->has("repayment_schedule_total")): ?>
                        <tr>
                            <td colspan="<?php echo $nbCols; ?>" style="text-align: right">
                                <b>Total:</b> <?php echo $model['total']; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <?php
    }
}