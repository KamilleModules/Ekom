<?php


namespace Module\Ekom\Utils\BalancedSchedule;


use Module\Ekom\Utils\E;


/**
 * Meant to solve 3x 4x payments problems for my company.
 */
class BalancedSchedule
{

    /**
     * @var $buckets Bucket[]
     */
    private $buckets;
    private $dates;
    private $indent;

    public function __construct()
    {
        $this->buckets = [];
        $this->dates = [];
        $this->indent = 2;
    }

    public static function create()
    {
        return new static();
    }

    public function addBucket(Bucket $bucket)
    {
        $this->buckets[] = $bucket;
        return $this;
    }

    public function setDates(array $dates)
    {
        $this->dates = $dates;
        return $this;
    }

    /**
     *
     *
     * Self Balanced Algorithm:
     * -------------------------------
     *
     * We first compute the ideal amount per lap/date per participant.
     *
     * Then for each date:
     *      - we ask for this amount for each the participants
     *      - if the participant cannot fulfill this amount, we keep track of this deficit in a balance
     *          for this participant. The participant is called failing participant.
     *
     *      - equilibrium phase:
     *          if there is at least one failing participant at the end of this lap,
     *          we ask the other (non-failing) participants to balance out the debt of the failing participants.
     *          For that, we ask each non-failing participant in order if they can fulfill the full debt.
     *
     *      - the equilibrium phase might have solved the failing participants balance's problem, or not.
     *      - If not, the balance deficit of a failing participant is reported to the next lap on its own balance.
     *
     *      For instance, if at the end of the lap, the failing participant has a negative balance (btw, the balance
     *      can only be 0 or negative, never positive) of -50, then on the next lap, we will ask this participant
     *      its ideal amount +50.
     *
     */
    public function getPaymentSchedule()
    {

        $BUCKET_BALANCES = [];
        $BUCKETS = [];
        $IDEAL_AMOUNTS = []; // per bucket


        //--------------------------------------------
        // PREPARE IDEAL NUMBERS
        //--------------------------------------------
        $this->boot($BUCKET_BALANCES, $IDEAL_AMOUNTS, $BUCKETS);


        //--------------------------------------------
        // START SCHEDULE
        //--------------------------------------------
        $schedule = [];
        foreach ($this->dates as $id => $date) {


            //trying to balance out to 0 every step
            $hasBalanceProblem = false;
            $failingBalances = [];
            $nonFailingParticipants = [];
            $bucketSchedules = [];
            foreach ($IDEAL_AMOUNTS as $name => $amountToCapture) {
                $bucketSchedule = [];

                /**
                 * @var $bucket Bucket
                 */
                $bucket = $BUCKETS[$name];
                $balance = $BUCKET_BALANCES[$name];
                $newBalance = 0;
                $bucketSchedule['start_balance'] = $balance;
                $balanceAmount = $balance * -1;


                $expected = $amountToCapture + ($balanceAmount); // reporting balance

                $bucketSchedule['expected'] = $expected;
                $bucketSchedule['expected_details'] = "$amountToCapture + $balanceAmount";


                $realAmountCaptured = $bucket->capture($expected, $id, $date);
                $bucketSchedule['real'] = $realAmountCaptured;


                $newBalance += ($realAmountCaptured - $expected);
                $bucketSchedule['balance'] = $newBalance;

                if ($newBalance < 0) {
                    $hasBalanceProblem = true;
                    $failingBalances[$name] = $newBalance;
                } else {
                    $nonFailingParticipants[] = $name;
                }

                $BUCKET_BALANCES[$name] = $newBalance;
                $bucketSchedules[$name] = $bucketSchedule;
            }


            //--------------------------------------------
            // EQUILIBRIUM PHASE
            //--------------------------------------------
            $equilibriumDetails = [];
            if (true === $hasBalanceProblem) {
                foreach ($failingBalances as $_name => $failingBalance) {
                    $equilibriumDetails[$_name]['startBalance'] = $failingBalance;
                    $equilibriumDetails[$_name]['providers'] = [];
                    foreach ($nonFailingParticipants as $name) {

                        /**
                         * @var $bucket Bucket
                         */
                        $bucket = $BUCKETS[$name];

                        $realAmount = $bucket->capture(-1 * $failingBalance, null, null);
                        $newBalance = $failingBalance + $realAmount;
                        $BUCKET_BALANCES[$_name] = $newBalance;
                        if ($realAmount > 0) {
                            $equilibriumDetails[$_name]['providers'][$name] = $realAmount;
                        }
                    }
                }


                foreach ($equilibriumDetails as $name => $info) {
                    $info['endBalance'] = $BUCKET_BALANCES[$name];
                    $equilibriumDetails[$name] = $info;
                }

            }
            $bucketSchedules['equilibriumDetails'] = $equilibriumDetails;
            $bucketSchedules['hasEquilibrium'] = $hasBalanceProblem;


            $schedule[$date] = $bucketSchedules;

        }

        $participants = array_keys($BUCKET_BALANCES);
        return [
            'participants' => $participants,
            'schedule' => $schedule,
        ];
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    private function boot(array &$BUCKET_BALANCES, array &$IDEAL_AMOUNTS, array &$BUCKETS)
    {
        $bucket2Amount = [];
        foreach ($this->buckets as $bucket) {
            $name = $bucket->getName();
            $amount = $bucket->getAmount();
            $bucket2Amount[$name] = $amount;
            $BUCKETS[$name] = $bucket;

        }

        $nbPayments = count($this->dates);
        foreach ($bucket2Amount as $name => $bucketTotal) {
            $ideal = $bucketTotal / $nbPayments;
            $IDEAL_AMOUNTS[$name] = $ideal;
            $BUCKET_BALANCES[$name] = 0;
        }

    }
}
