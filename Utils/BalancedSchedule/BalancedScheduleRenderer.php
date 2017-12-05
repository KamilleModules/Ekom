<?php


namespace Module\Ekom\Utils\BalancedSchedule;


class BalancedScheduleRenderer
{

    public static function renderDebugTable(array $model, $useOwnStyle = true)
    {
        $participants = $model['participants'];
        $items = $model['schedule'];
        $nbCols = (3 * count($participants)) + 1;
        ?>

        <?php if ($useOwnStyle): ?>
        <style>
            table {
                border-collapse: collapse;
                border: 1px solid black;
            }

            tr, td, th {
                border: 1px solid black;
            }

            td, th {
                padding: 5px;
            }
        </style>
    <?php endif; ?>

        <table>
            <?php foreach ($items as $date => $item): ?>
                <tr>
                    <th>Date</th>
                    <?php foreach ($participants as $participant): ?>
                        <th><?php echo $participant; ?>.expected</th>
                        <th><?php echo $participant; ?>.real</th>
                        <th><?php echo $participant; ?>.balance</th>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td><?php echo $date; ?></td>
                    <?php foreach ($participants as $participant):
                        $info = $item[$participant];
                        ?>
                        <td><?php echo $info['expected']; ?> (<?php echo $info['expected_details']; ?>)</td>
                        <td><?php echo $info['real']; ?></td>
                        <td><?php echo $info['balance']; ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="<?php echo $nbCols - 1; ?>">Equilibrium</td>
                </tr>
                <?php if (true === $item['hasEquilibrium']): ?>
                    <?php foreach ($item['equilibriumDetails'] as $participantName => $details): ?>
                        <tr>
                            <td></td>
                            <td colspan="<?php echo $nbCols - 1; ?>">
                                <?php echo $participantName; ?>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="<?php echo $nbCols - 1; ?>">
                                <table>
                                    <tr>
                                        <td>start balance</td>
                                        <td>providers</td>
                                        <td>end balance</td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $details['startBalance']; ?></td>
                                        <td>
                                            <table>
                                                <?php foreach ($details['providers'] as $provider => $amount): ?>
                                                    <tr>
                                                        <td><?php echo $provider; ?>: <?php echo $amount; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </table>
                                        </td>
                                        <td><?php echo $details['endBalance']; ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
        <?php
    }
}
