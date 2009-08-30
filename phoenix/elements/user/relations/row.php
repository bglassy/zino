<?php
    class ElementUserRelationsRow extends Element {
        public function Render( $relation, $isfriend ) {
            ?><li><a href="<?php
                    Element( 'url', $relation->Friend );
                ?>">
                <?php Element( "image/view", $relation->Friend->Avatarid, $relation->Friend->Id, 50, 50, IMAGE_CROPPED_100x100, '', $relation->Friend->Name );
                echo htmlspecialchars( $relation->Friend->Name );
                ?>
                </a>
                    <?php
                    if ( $isfriend ) {
                        ?><a class="add" href="">+
                         <span>Γίνε φίλος<?php
                    }
                    else {
                        ?><a class="remove" href="">-
                        <span>Διαγραφή φίλου<?php
                    }
                    ?>
                        <i class="tr corner"></i>
                        <i class="tl corner"></i>
                        <i class="br corner"></i>
                        <i class="bl corner"></i>
                    </span>
                </a>
                <span><?php
                    if ( $relation->Friend->Gender == 'f' ) {
                        $datalist[] = "Κορίτσι";
                    }
                    else {
                        $datalist[] = "Αγόρι";
                    }
                    if ( $relation->Friend->Profile->Age ) {
                        $datalist[] = $relation->Friend->Profile->Age;
                    }
                    if ( $theuser->Profile->Placeid > 0 ) {
                        $datalist[] = htmlspecialchars( $theuser->Profile->Location->Name );
                    }
                    while ( $data = array_shift( $datalist ) ) {
                        ?><span><?php
                        echo $data;
                        ?></span><?php
                        if ( !empty( $datalist ) ) {
                            ?>·<?php
                        }
                    }
                ?><div class="barfade"><div class="leftbar"></div><div class="rightbar"></div></div>
            </li><?php
        }
    }
?>