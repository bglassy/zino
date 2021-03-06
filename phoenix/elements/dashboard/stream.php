<?php
    class ElementDashboardStream extends Element {
        public function Render( $stream ) {
            ?><div id="stream">
                <h2>Τι συμβαίνει;</h2>
                <ul><?php
                    foreach ( $stream as $fish ) {
                        $type = $fish[ 'type' ];
                        $item = $fish[ 'item' ];
                        $comments = $fish[ 'comments' ];
                        $numcomments = $fish[ 'numcomments' ];
                        if ( is_array( $item ) ) {
                            $single = $item[ 0 ];
                        }
                        else {
                            $single = $item;
                        }
                        ?><li<?php
                        if ( !is_array( $item ) ) {
                            ?> class="link" onclick="Dashboard.Navigate('<?php
                            ob_start();
                            Element( 'url', $item );
                            echo htmlspecialchars( ob_get_clean() );
                            ?>')"<?php
                        }
                        ?>>
                        <div class="avatar">
                            <div class="tl corner"></div>
                            <div class="tr corner"></div>
                            <div class="bl corner"></div>
                            <div class="br corner"></div>
                            <a href="<?php
                                ob_start();
                                Element( 'url', $single->User );
                                echo htmlspecialchars( ob_get_clean() );
                                ?>" title="<?php
                                echo $single->User->Name;
                                ?>"><?php
                                Element( 'image/view', $single->User->Avatarid, $single->Userid, 100, 100, IMAGE_CROPPED_100x100, '', $single->User->Name, '', true, 50, 50, 0 );
                                ?>

                            </a>
                        </div>
                        <div class="spotcontent">
                        <a href="" class="filter" title="Κι άλλα όπως αυτό"></a><?php
                        switch ( $type ) {
                            case 'Journal':
                                $journal = $item;
                                ?>
                                <h3><strong><?php
                                Element( 'user/name', $journal->Userid, $journal->User->Name, $journal->User->Subdomain );
                                ?></strong> 
                                <i class="journal icon"></i> <a href="<?php
                                ob_start();
                                Element( 'url', $journal );
                                echo htmlspecialchars( ob_get_clean() );
                                ?>"><?php
                                echo htmlspecialchars( $journal->Title );
                                ?></a></h3>
                                <?php
                                break;
                            case 'Image':
                                ?>
                                <h3>
                                    <i class="photo icon"></i> 
                                    <?php
                                    if ( !is_array( $item ) ) {
                                        $items = array( $item );
                                    }
                                    else {
                                        $items = $item;
                                    }
                                    $username = $single->User->Name;
                                    $gender = $single->User->Gender;
                                    ?>
                                    Νέες φωτογραφίες <?php
                                    switch ( $gender ) {
                                        case 'f':
                                            ?>της<?php
                                            break;
                                        case 'm':
                                        default:
                                            ?>του<?php
                                    }
                                    ?> <strong><a href="" class="inline"><?php
                                    echo $username;
                                    ?></a></strong></h3>
                                    <?php
                                    foreach ( $items as $photo ) {
                                        ?><a href="<?php
                                            ob_start();
                                            Element( 'url', $photo );
                                            echo htmlspecialchars( ob_get_clean() );
                                            ?>"><?php
                                            Element( 'image/view', $photo->Id, $photo->Userid, 100, 100, IMAGE_CROPPED_100x100, '', $photo->Name, '', false, 0, 0, 0 );
                                        ?></a><?php
                                    }
                                break;
                            case 'Poll':
                                ?>
                                <h3>
                                    <strong><?php
                                        Element( 'user/name', $item->Userid, $item->User->Name, $item->User->Subdomain );
                                        ?></strong> 
                                        <i class="poll icon"></i> <a href="<?php
                                        ob_start();
                                        Element( 'url', $item );
                                        echo htmlspecialchars( ob_get_clean() );
                                        ?>"><?php
                                        echo htmlspecialchars( $item->Title );
                                        ?></a>
                                </h3>
                                <?php
                                break;
                        }
                        ?></div>
                        <div class="comments"><?php
                        if ( $numcomments ) {
                                ?><h4><?php
                                echo $numcomments;
                                ?> σχόλι<?php
                                if ( $numcomments == 1 ) {
                                    ?>ο<?php
                                }
                                else {
                                    ?>α<?php
                                }
                                ?></h4>
                                <ul class="comments"><?php
                                    foreach ( $comments as $comment ) {
                                        ?><li><a href="<?php
                                        ob_start();
                                        echo Element( 'url', $single );
                                        $url = ob_get_clean();
                                        echo htmlspecialchars( $url );
                                        if ( strpos( $url, '&' ) !== false ) {
                                            ?>&amp;<?php
                                        }
                                        else {
                                            ?>?<?php
                                        }
                                        ?>commentid=<?php
                                        echo $comment[ 'id' ];
                                        ?>"><strong><?php
                                        echo $comment[ 'user_name' ];
                                        ?></strong> <?php
                                        echo $comment[ 'text' ];
                                        ?></a></li><?php
                                    }
                                    ?>
                                </ul><?php
                        }
                        ?></div><?php
                        ?></li><?php
                    }
                ?>
                </ul>
                </div>
                <?php
                return;
                ?>
                    <li class="link">
                        <div class="avatar">
                            <div class="tl corner"></div>
                            <div class="tr corner"></div>
                            <div class="bl corner"></div>
                            <div class="br corner"></div>
                            <a href="" title="beboula">

                                <img src="http://images2.zino.gr/media/4000/219356/219356_100.jpg" alt="beboula" style="width:50px;height:50px" />
                            </a>
                        </div>
                        <div class="spotcontent">
                            <a href="" class="filter" title="Κι άλλα όπως αυτό"></a>
                            <h3>
                                <strong><a href="">beboula</a></strong> 
                                    <i class="poll icon"></i> den einai uperoxo pou ta magazia evalan xristougenniatika?? :) :) exete mpei sto klima???
                            </h3>

                        </div>
                        <div class="comments">
                            <h4>19 σχόλια</h4>
                            <ul class="comments">
                                <li><a href=""><strong>kard0uLina</strong> tcu k dn 9elw :P</a></li>
                                <li><a href=""><strong>_daemon_</strong> w nai, iperoxo, as poulisoume kana xristougeniatiko giati pirame to poulo me ta ipoloipa! hell yeah goustarw katanalwtikes epidromes logo xmas</a></li>

                                <li><a href=""><strong>Seraphim</strong> giou ar xot</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <div class="avatar">
                            <div class="tl corner"></div>

                            <div class="tr corner"></div>
                            <div class="bl corner"></div>
                            <div class="br corner"></div>
                                <a href="">
                                    <img src="http://images2.zino.gr/media/1778/165339/165339_100.jpg" alt="" title="" />
                                </a>
                        </div>
                        <div class="comments">
                            <h4>13 σχόλια</h4>
                            <ul class="comments">

                                <li><a href=""><strong>B1anka</strong> gmth pic :-)</a></li>
                                <li class="lvl2"><a href=""><strong>uLee</strong> Thanks :D</a></li>
                                <li class="lvl3"><a href=""><strong>B1anka</strong> tpt :P</a></li>
                            </ul>

                        </div>
                    </li>
                </ul>
            </div><?php
        }
    }
?>
