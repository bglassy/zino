<photos>
    <? global $settings;
    foreach ( $photos as $photo ): ?>
    <photo id="<?= $photo[ 'id' ] ?>">
        <media url="http://images2.zino.gr/media/<?= $photo[ 'userid' ] ?>/<?= $settings[ 'beta' ]? '_': '' ?><?= $photo[ 'id' ] ?>/<?= $photo[ 'id' ] ?>_150.jpg" />
        <discussion count="<?= $photo[ 'numcomments' ] ?>" />
    </photo>
    <? endforeach; ?>
</photos>
