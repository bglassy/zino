<entry>
    <media url="http://images2.zino.gr/media/<?= $photo[ 'userid' ] ?>/<?= $photo[ 'id' ] ?>/<?= $photo[ 'id' ] ?>_full.jpg" /><?
    include 'views/comment/listing.php';
    if ( !empty( $favourites ) ): ?>
    <favourites>
        <? foreach ( $favourites as $favourite ): ?>
        <user><name><?= $favourite[ 'username' ] ?></name></user>
        <? endforeach; ?>
    </favourites>
    <? endif; ?>
</entry>