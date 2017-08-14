<?php


use ArrayToString\ArrayToStringTool;
use Authenticate\SessionUser\SessionUser;
use Bat\SessionTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Services\XLog;
use Logger\Logger;
use Module\Ekom\Utils\OrderBuilder\OrderBuilder;
use Module\Ekom\Utils\OrderBuilder\OrderBuilderInterface;
use Module\Ekom\Utils\OrderBuilder\Step\MockOrderBuilderStep;
use PhpBeast\AuthorTestAggregator;
use PhpBeast\PrettyTestInterpreter;
use PhpBeast\Tool\ComparisonErrorTableTool;


ini_set("display_errors", "1");

require_once __DIR__ . "/../init-test.php";


$agg = AuthorTestAggregator::create();
if ('prepareKamille') {

    SessionUser::$key = 'frontUser';
    A::loggerInit();
//    $logger = Logger::create();
//    $logger->addListener(QuickDebugLoggerListener::create());
//    XLog::setLogger($logger);


    $logger = Logger::create();
    Hooks::call("Core_addLoggerListener", $logger);
    XLog::setLogger($logger);

    A::quickPdoInit();
    ApplicationParameters::set('request', \Kamille\Architecture\Request\Web\HttpRequest::create());
}


$trainingStep = MockOrderBuilderStep::create()->setIdentifier('training');

$ob = OrderBuilder::create()
    ->setSessionName("order-builder-1")
    ->registerStep('login', MockOrderBuilderStep::create()->setIdentifier('login'))
    ->registerStep('training', $trainingStep)
    ->registerStep('shipping', MockOrderBuilderStep::create()->setIdentifier('shipping'))
    ->registerStep('payment', MockOrderBuilderStep::create()->setIdentifier('payment'));
$ob->clean();


function compareStates(array $id2Expected, array $states, &$msg = null)
{
    // now comparing the states
    foreach ($id2Expected as $id => $state) {
        if (array_key_exists($id, $states)) {
            $_state = $states[$id];
            if ($_state === $state) {
                // ok
            } else {
                $msg = "state not matching: expected $state, got $_state for id=$id";
                a("showing expected states");
                a($id2Expected);
                a("showing actual states");
                a($states);
                return false;
            }

        } else {
            $msg = "Key not found: $id in " . ArrayToStringTool::toPhpArray($states);
            return false;
        }
    }
    return true;
}


function compareModel($id, array $stepInfo, &$msg = null)
{
    if (array_key_exists($id . 'ModelName', $stepInfo['model'])) {
        return true;
    } else {
        $msg = "expected model not found";
    }
    return false;
}


function myTestRig($expectedStates, $expectedModelId, $expectedCompleted = false, array $fixturePost = [], OrderBuilderInterface $ob, &$msg = null)
{
    $_POST = $fixturePost;
    $stepInfo = $ob->getStepsInfo();
    if ($expectedCompleted === $ob->isCompleted()) {
        $sessionStates = $_SESSION['ekom']['order-builder-1']['states'];
        $res = compareStates($expectedStates, $sessionStates, $msg);
        if (true === $res) {
            if (null !== $expectedModelId) {
                return compareModel($expectedModelId, $stepInfo, $msg);
            } elseif (true === $expectedCompleted) {
                return true;
            }
            $msg = "expectedModelId is null but the OrderBuilder is not completed!";
            return false;
        }
    } else {
        $sessionStates = $_SESSION['ekom']['order-builder-1']['states'];
        a('session states');
        a($sessionStates);
        $msg = "OrderBuilder.isCompleted mismatch";
    }
    return false;
}


//--------------------------------------------
// TEST 1
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'active',
        'training' => 'inactive',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'login', false, [], $ob, $msg);
});


//--------------------------------------------
// TEST 2: CHECKING THAT PAGE REFRESH DOESN'T CHANGE ANYTHING (same test)
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'active',
        'training' => 'inactive',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'login', false, [], $ob, $msg);
});


//--------------------------------------------
// TEST 3: If the data is posted on a step that is not active, the form data is ignored,
// and things remain the same
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'active',
        'training' => 'inactive',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'login', false, [
        'training' => "abc",
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 4: Now posting on the active state, triggers change to the next step
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'active',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'training', false, [
        'login' => "abc",
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 5: Emulating the user coming back to the login (state=done) step
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'active',
        'training' => 'inactive',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'login', false, [
        'step' => 'login',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 6: Checking that the user can't manually jump to the training states (or any inactive state)
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'active',
        'training' => 'inactive',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'login', false, [
        'step' => 'training',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 7: Passing again the login test
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'active',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'training', false, [
        'login' => 'abc',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 8: Then passing the training test
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'done',
        'shipping' => 'active',
        'payment' => 'inactive',
    ], 'shipping', false, [
        'training' => 'abc',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 9: Trying to go back to the training test
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'active',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'training', false, [
        'step' => 'training',
    ], $ob, $msg);
});

//--------------------------------------------
// TEST 10: Passing the training step again by submitting the corresponding form
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'done',
        'shipping' => 'active',
        'payment' => 'inactive',
    ], 'shipping', false, [
        'training' => 'abc',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 11: Passing the shipping step by submitting the corresponding form
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'done',
        'shipping' => 'done',
        'payment' => 'active',
    ], 'payment', false, [
        'shipping' => 'abc',
    ], $ob, $msg);
});

//--------------------------------------------
// TEST 12: Passing the payment step by submitting the corresponding form
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'done',
        'shipping' => 'done',
        'payment' => 'done',
    ], null, true, [
        'payment' => 'abc',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 13: should be the same outcome as test 1
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob, $trainingStep) {
    //--------------------------------------------
    // THEN CLEANING, and disabling the training step
    //--------------------------------------------
    $ob->clean();
    $trainingStep->setIsRelevant(false);

    return myTestRig([
        'login' => 'active',
        'training' => 'irrelevant',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'login', false, [], $ob, $msg);
});


//--------------------------------------------
// REDOING STEPS AGAIN WITH THE TRAINING STEP DISABLED
//--------------------------------------------

//--------------------------------------------
// TEST 2-14: CHECKING THAT PAGE REFRESH DOESN'T CHANGE ANYTHING (same test)
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'active',
        'training' => 'irrelevant',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'login', false, [], $ob, $msg);
});


//--------------------------------------------
// TEST 3-15: If the data is posted on a step that is not active, the form data is ignored,
// and things remain the same
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'active',
        'training' => 'irrelevant',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'login', false, [
        'training' => "abc",
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 4-16: Now posting on the active state, triggers change to the next step
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'irrelevant',
        'shipping' => 'active',
        'payment' => 'inactive',
    ], 'shipping', false, [
        'login' => "abc",
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 5-17: Emulating the user coming back to the login (state=done) step
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'active',
        'training' => 'irrelevant',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'login', false, [
        'step' => 'login',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 6-18: Checking that the user can't manually jump to the training states (or any inactive state)
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'active',
        'training' => 'irrelevant',
        'shipping' => 'inactive',
        'payment' => 'inactive',
    ], 'login', false, [
        'step' => 'training',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 7-19: Passing again the login test
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'irrelevant',
        'shipping' => 'active',
        'payment' => 'inactive',
    ], 'shipping', false, [
        'login' => 'abc',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 8-20: Then passing the shipping test
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'irrelevant',
        'shipping' => 'done',
        'payment' => 'active',
    ], 'payment', false, [
        'shipping' => 'abc',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 9-21: Trying to go back to the login test
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'active',
        'training' => 'irrelevant',
        'shipping' => 'done',
        'payment' => 'inactive',
    ], 'login', false, [
        'step' => 'login',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 10-22: Passing the login step again by submitting the corresponding form
/**
 * Note: we go back to the payment step directly, since the shipping step was done,
 * but that's just the actual algorithm (we could have make the user redo the shipping step)
 */
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'irrelevant',
        'shipping' => 'done',
        'payment' => 'active',
    ], 'payment', false, [
        'login' => 'abc',
    ], $ob, $msg);
});


//--------------------------------------------
// TEST 12-23: Passing the payment step by submitting the corresponding form
//--------------------------------------------
$agg->addTest(function (&$msg = null, $testNumber) use ($ob) {
    return myTestRig([
        'login' => 'done',
        'training' => 'irrelevant',
        'shipping' => 'done',
        'payment' => 'done',
    ], null, true, [
        'payment' => 'abc',
    ], $ob, $msg);
});


PrettyTestInterpreter::create()->execute($agg);
ComparisonErrorTableTool::display();