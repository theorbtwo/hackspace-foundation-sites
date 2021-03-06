<?
$title = 'Membership Management';
$page = 'main';
require('./header.php');
$cardid = strtoupper($_GET['cardid']);
$cards = fRecordSet::build('Card', array('uid=' => $cardid));
if($cards->count() == 0) {
    fURL::redirect("/kiosk/addcard.php?cardid=" . $cardid);
}
$card = $cards->getRecord(0);
$user = new User($card->getUserId());
$user->load();

if ($user->isMember()) {
    $result = fRecordSet::build(
        'Transaction',
        array('user_id=' => $user->getId(),
            'timestamp>' => new fDate('2009-01-01'),
            'timestamp<' => new fDate('now')
        ),
        array('timestamp' => 'desc')
        );

        $expires = strtotime($result[0]->getTimestamp());
        # 30 days ~= a month
        # we don't include the 14 days grace period here.
        $expires += 30 * 24 * 60 * 60;
        $expires = date('d F Y', $expires);
}

?>
<table class="table">
<tr><th>Member Name</th><td><?=$user->prepareFullName()?></td></tr>
<tr><th>Member ID</th><td> <?=$user->getMemberNumber()?></td></tr>
<tr><th>Card ID</th><td> <?=$card->prepareUid()?></td></tr>
<tr><th>Subscribed</th><td> <?=$user->isMember() ? "Yes":"No"?></td></tr>
<? if ($user->isMember()) { ?>
<tr><th>Subscription Expirey</th><td> <?=$expires ?></td></tr>
<? } ?>
</table>

<h2>Print Membership Stickers</h2>
<div class="btn-group">
<a href="/kiosk/storage.php?cardid=<?=$cardid?>" class="btn btn-default">Storage Request</a>
<a href="/kiosk/box.php?cardid=<?=$cardid?>" class="btn btn-default">Member Box</a>
</div>

<h2>Other Stickers</h2>

<div class="btn-group">
<a href="/kiosk/fixme.php?cardid=<?=$cardid?>" class="btn btn-default">Fix Me</a>
<a href="/kiosk/hackme.php?cardid=<?=$cardid?>" class="btn btn-default">Hack Me</a>
<a href="/kiosk/nod.php?cardid=<?=$cardid?>" class="btn btn-default">Notice of Disposal</a>
</div>


<?require('./footer.php')?>
</body>
</html>
