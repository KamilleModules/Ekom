<div class="widget widget-exception">
    <h1>An exception occurred</h1>
    <?php


    /**
     * @var \Exception $exception
     */
    use QuickPdo\QuickPdo;

    $exception = $v['exception'];
    $showMessage = $v['showMessage'];
    $showTrace = $v['showTrace'];
    $showFile = $v['showFile'];
    $showCode = $v['showCode'];
    $showLine = $v['showLine'];


    ?>
    <table>
        <?php if ($showMessage): ?>
            <tr>
                <td>Message</td>
                <td><?php echo $exception->getMessage(); ?></td>
            </tr>
        <?php endif; ?>
        <!-- sorry for putting logic into the template, it's just for me, quick'n'dirty.... -->
        <?php if ($exception instanceof \PDOException): ?>
            <tr>
                <td>QuickPdo query</td>
                <td><?php echo QuickPdo::getQuery(); ?></td>
            </tr>
        <?php endif; ?>
        <?php if ($showFile): ?>
            <tr>
                <td>File</td>
                <td><?php echo $exception->getFile(); ?></td>
            </tr>
        <?php endif; ?>
        <?php if ($showCode): ?>
            <tr>
                <td>Code</td>
                <td><?php echo $exception->getCode(); ?></td>
            </tr>
        <?php endif; ?>
        <?php if ($showLine): ?>
            <tr>
                <td>Line</td>
                <td><?php echo $exception->getLine(); ?></td>
            </tr>
        <?php endif; ?>
        <?php if ($showTrace): ?>
            <tr>
                <td>Trace</td>
                <td><?php echo nl2br($exception->getTraceAsString()); ?></td>
            </tr>
        <?php endif; ?>
    </table>
</div>
