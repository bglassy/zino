<?php
    include 'usersections.php';
?>
<div id="pollview">
    <div style="float:right;width:180px;height:150px;">
            <img src="images/ads/ad180.jpg" alt="Διαφήμιση" />
    </div>
    <h2>Πόσες φορές τη μέρα βαράς μαλακία;</h2>
    <div class="results">    
        <ul>
            <li>
                <dl>
                    <dt style="float:right;">
                        Μία
                    </dt>
                    <dd><?php //max width will be 220px and min width will be 24px ?>
                        <div class="percentagebar" style="width:120px;">
                            <div class="leftrounded"></div>
                            <div class="rightrounded"></div>
                            <div class="middlerounded"></div>
                        </div>
                        <span>30%</span>
                    </dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt style="float:right;">
                        Μεταξύ 2 και 5
                    </dt>
                    <dd>
                        <div class="percentagebar" style="width:150px;">
                            <div class="leftrounded"></div>
                            <div class="rightrounded"></div>
                            <div class="middlerounded"></div>
                        </div>
                        <span>64%</span>
                    </dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt style="float:right;">
                        Από 5 μέχρι 10
                    </dt>
                    <dd>
                        <div class="percentagebar" style="width:34px;">
                            <div class="leftrounded"></div>
                            <div class="rightrounded"></div>
                            <div class="middlerounded"></div>
                        </div>
                        <span>5,3%</span>
                    </dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt style="float:right;">
                        Από 10 και πάνω
                    </dt>
                    <dd>
                        <div class="percentagebar" style="width:24px;">
                            <div class="leftrounded"></div>
                            <div class="rightrounded"></div>
                            <div class="middlerounded"></div>
                        </div>
                        <span>0,7%</span>
                    </dd>
                </dl>
            </li>                
        </ul>
    </div>
    <div class="ads" style="margin: 10px 0;text-align:center;overflow:hidden;height: 60px;">
        <img src="images/ads/ad234.jpg" style="width:234px;height:60px;margin: 0 5px;" alt="Διαφήμιση" />
        <img src="images/ads/ad234.jpg" style="width:234px;height:60px;margin: 0 5px;" alt="Διαφήμιση" />
        <img src="images/ads/ad234.jpg" style="width:234px;height:60px;margin: 0 5px;" alt="Διαφήμιση" />
        <img src="images/ads/ad234.jpg" style="width:234px;height:60px;margin: 0 5px;" alt="Διαφήμιση" />
    </div>
    <div class="comments">
        <div class="comment newcomment">
            <div class="toolbox">
                <span class="time">τα σχόλια είναι επεξεργάσημα για ένα τέταρτο</span>
            </div>
            <div class="who">
                <a href="user/dionyziz">
                    <img src="images/avatars/dionyziz.jpg" class="avatar" alt="Dionyziz" />
                    dionyziz
                </a>πρόσθεσε ένα σχόλιο στο προφίλ σου
            </div>
            <div class="text">
                <textarea rows="2" cols="50"></textarea>
            </div>
            <div class="bottom">
                <input type="submit" value="Σχολίασε!" />
            </div>
        </div>
        <div class="comment" style="border-color: #dee;">
            <div class="toolbox">
                <span class="time">πριν 12 λεπτά</span>
            </div>
            <div class="who">
                <a href="user/smilemagic">
                    <img src="images/avatars/smilemagic.jpg" class="avatar" alt="SmilEMagiC" />
                    SmilEMagiC
                </a> είπε:
            </div>
            <div class="text">
                ρε μλκ τι είναι αυτά που γράφεις στο προφίλ μου? μωρή μαλακία...
                <img src="images/emoticons/tongue.png" alt=":P" title=":P" /><br />
                άμα σε πιάσω...<br />
                χαχα!! <img src="images/emoticons/teeth.png" alt=":D" title=":D" /><br />
                θα βρεθούμε το ΣΚ!??
            </div>
            <div class="bottom">
                <a href="" onclick="return false;">Απάντα</a> σε αυτό το σχόλιο
            </div>
        </div>
        <div class="comment" style="margin-left: 20px; border-color: #eed;">
            <div class="toolbox" style="margin-right: 20px">
                <span class="time">πριν 10 λεπτά</span>
            </div>
            <div class="who">
                <a href="user/kostis90gr">
                    <img src="images/avatars/kostis90gr.jpg" class="avatar" alt="kostis90gr" />
                    kostis90gr
                </a> είπε:
            </div>
            <div class="text">
                αχαχαχαχ έλεος ρε νίκο!!...
            </div>
            <div class="bottom">
                <a href="" onclick="return false;">Απάντα</a> σε αυτό το σχόλιο
            </div>
        </div>
        <div class="comment" style="margin-left: 20px; border-color: #ded">
            <div class="toolbox" style="margin-right: 20px">
                <span class="time">πριν 9 λεπτά</span>
            </div>
            <div class="who">
                <a href="user/izual">
                    <img src="images/avatars/izual.jpg" class="avatar" alt="izual" />
                    izual
                </a> είπε:
            </div>
            <div class="text">
                αφού τον ξέρεις μωρέ πώς κάνει..
            </div>
            <div class="bottom">
                <a href="" onclick="return false;">Απάντα</a> σε αυτό το σχόλιο
            </div>
        </div>
        <div class="comment" style="margin-left: 40px; border-color: #dee">
            <div class="toolbox" style="margin-right: 40px">
                <span class="time">πριν 9 λεπτά</span>
            </div>
            <div class="who">
                <a href="user/smilemagic">
                    <img src="images/avatars/smilemagic.jpg" class="avatar" alt="SmilEMagiC" />
                    SmilEMagiC
                </a> είπε:
            </div>
            <div class="text">
                για πλάκα τα λέω ρε!!
            </div>
            <div class="bottom">
                <a href="" onclick="return false;">Απάντα</a> σε αυτό το σχόλιο
            </div>
        </div>
        <div class="comment">
            <div class="toolbox">
                <span class="time">πριν 12 λεπτά</span>
            </div>
            <div class="who">
                <a href="user/titi">
                    <img src="images/avatars/titi.jpg" class="avatar" alt="Titi" />
                    Titi
                </a> είπε:
            </div>
            <div class="text">
                αδερφούλη το πάρτυ θα είναι γαμάτο, έχω ήδη μαγειρέψει αίμα!!!
            </div>
            <div class="bottom">
                <a href="" onclick="return false;">Απάντα</a> σε αυτό το σχόλιο
            </div>
        </div>
        <div class="comment" style="margin-left: 20px">
            <div class="toolbox" style="margin-right: 20px">
                <span class="time">πριν 12 λεπτά</span>
                <a href="" onclick="return false"><img src="images/delete.png" alt="Διαγραφή" title="Διαγραφή" /></a>
            </div>
            <div class="who">
                <a href="user/dionyziz">
                    <img src="images/avatars/dionyziz.jpg" class="avatar" alt="Dionyziz" />
                    dionyziz
                </a> είπε:
            </div>
            <div class="text">
                Τέλεια! Πήρες black light?
            </div>
            <div class="bottom">
                <a href="" onclick="return false;">Απάντα</a> σε αυτό το σχόλιο
            </div>
        </div>
        <div class="comment oldcomment">
            <div class="toolbox">
                <a href="" onclick="return false" class="rss">
                    <img src="images/feed.png" alt="rss" title="RSS Feed" class="rss" />
                </a>
            </div>
            <div class="who">
                <a href="user/dionyziz">
                    412 παλιότερα σχόλια
                </a>
            </div>
            <div class="text">
            </div>
            <div class="bottom">
            </div>
        </div>
    </div>
    <div class="eof"></div>
    <div class="ads" style="margin: 10px 0;text-align:center;overflow:hidden;height: 60px;">
        <img src="images/ads/ad234.jpg" style="width:234px;height:60px;margin: 0 5px;" alt="Διαφήμιση" />
        <img src="images/ads/ad234.jpg" style="width:234px;height:60px;margin: 0 5px;" alt="Διαφήμιση" />
        <img src="images/ads/ad234.jpg" style="width:234px;height:60px;margin: 0 5px;" alt="Διαφήμιση" />
        <img src="images/ads/ad234.jpg" style="width:234px;height:60px;margin: 0 5px;" alt="Διαφήμιση" />
    </div>
    <div class="ads" style="margin: 10px 0;text-align:center;overflow:hidden;height: 90px;">
        <img src="images/ads/ad728.jpg" style="width:728px;height:90px;margin: 0 5px;" alt="Διαφήμιση" />
    </div>
</div>
