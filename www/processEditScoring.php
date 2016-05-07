<?
require('init.php');
require('session.php');

$clanId = $_POST['clanId'];
try{
    $clan = new clan($clanId);
    $clanId = $clan->get('id');
}catch(Exception $e){
    $_SESSION['curError'] = $e->getMessage();
    header('Location: /clans.php');
    exit;
}

if(!userHasAccessToUpdateClan($clan)){
    $_SESSION['curError'] = NO_ACCESS;
    header('Location: /warStats.php?clanId=' . $clanId);
    exit;
}

$weights = array(
    'firstAttackWeight',
    'secondAttackWeight',
    'totalStarsWeight',
    'newStarsWeight',
    'defenceWeight',
    'numberOfDefencesWeight',
    'attacksUsedWeight',
    'rankAttackedWeight',
    'rankDefendedWeight'
);

try{
    foreach ($weights as $weight) {
        $val = $_POST[$weight];
        if (isset($val)) {
            if ($clan->get($weight) != $val) {
                $clan->set($weight, $val);
            }
        }
    }
    $_SESSION['curMessage'] = 'Successfully updated War Statistic Weights for ' . $clan->get('name') . '.';
}catch(Exception $e){
    $_SESSION['curError'] = $e->getMessage();
}
header('Location: /warStats.php?edit=edit&clanId=' . $clanId);
exit;