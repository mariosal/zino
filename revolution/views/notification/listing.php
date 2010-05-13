<stream type="notify" count="<?= count( $notifications ) ?>">
    <? foreach ( $notifications as $notification ): ?>
    <? switch ( $notification[ 'eventtype' ] ):
    case 'EVENT_COMMENT_CREATED': 
    ?>

    <entry type="<?
    switch ( $notification[ 'comment' ][ 'typeid' ] ):
        case TYPE_IMAGE:
            ?>photo<?
            break;
        case TYPE_POLL:
            ?>poll<?
            break;
        case TYPE_USERPROFILE:
            ?>user<?
            break;
        case TYPE_JOURNAL:
            ?>journal<?
            break;
    endswitch;
    ?>" id="<?= $notification[ 'comment' ][ 'itemid' ] ?>">
        <discussion>
            <? if ( $notification[ 'comment' ][ 'parentid' ] != 0 ): ?>
            <comment id="<?= $notification[ 'comment' ][ 'parentid' ] ?>">
            <? endif; ?>
                <?
                    $comment = $notification[ 'comment' ];
                    $user = $notification[ 'user' ];
                    include 'views/comment/view.php';
                ?>
            <? if ( $notification[ 'comment' ][ 'parentid' ] != 0 ): ?>
            </comment>
            <? endif; ?>
        </discussion>
    </entry>
    <? 
    break;
    case 'EVENT_FAVOURITE_CREATED': 
    ?>

    <entry type="<?
    switch ( $notification[ 'favourite' ][ 'typeid' ] ):
        case TYPE_IMAGE:
            ?>photo<?
            break;
        case TYPE_POLL:
            ?>poll<?
            break;
        case TYPE_USERPROFILE:
            ?>user<?
            break;
        case TYPE_JOURNAL:
            ?>journal<?
            break;
    endswitch;
    ?>" id="<?= $notification[ 'favourite' ][ 'itemid' ] ?>">
        <favourites>
            <user id="<?= $notification[ 'favourite' ][ 'user' ][ 'id' ] ?>">
                <?
                    $user = $notification[ 'favourite' ][ 'user' ];
                ?>
                <name><?= $user[ 'name' ] ?></name>
                <subdomain><?= $user[ 'subdomain' ] ?></subdomain>
                <gender><?= $user[ 'gender' ] ?></gender>
                <age><?= $user[ 'age' ] ?></age>
                <location><?= $user[ 'place' ][ 'name' ] ?></location>
                <avatar id="<?= $user[ 'avatarid' ] ?>">
                    <media url="http://images2.zino.gr/media/<?= $user[ 'id' ] ?>/<?= $user[ 'avatarid' ] ?>/<?= $user[ 'avatarid' ] ?>_100.jpg" />
                </avatar>
            </user>
        </favourites>
    </entry>
    <?
    break;
    endswitch; ?>
    <? endforeach; ?>

</stream>