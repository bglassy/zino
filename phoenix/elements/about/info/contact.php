<?php
    class ElementAboutInfoContact extends Element {
        public function Render( $status ) {
            global $page;
            global $user;
            
            $page->SetTitle( 'Επικοινωνία' );
            
            if ( $status ) { // sent
                ?><h2>Ευχαριστούμε!</h2>

                <p>Ευχαριστούμε που αφιέρωσες το χρόνο να επικοινωνήσεις μαζί μας. Θα διαβάσουμε το μήνυμά σου προσεκτικά.</p><?php
                return;
            }

            ?><form id="aboutcontact" action="do/about/contact" method="post">
                <div><?php
                    if ( $user->Exists() ) {
                        ?><label>Το ψευδώνυμό σου:</label>
                        <strong><?php
                        echo $user->Name;
                        ?></strong><?php
                    }
                    else {
                       ?><label>Το e-mail σου:</label>
                       <input type="text" name="email" value="" /><?php
                    }
                    ?>
                </div>
                <p>Όλα τα μηνύματα που λαμβάνουμε διαβάζονται προσεκτικά και με επιμέλεια από κάποιον της ομάδας ανάπτυξης του Zino.
                   Θα προσπαθήσουμε να σου απαντήσουμε στο μήνυμά σου, αλλά αυτό δυστυχώς δεν είναι πάντα δυνατό λόγω του πλήθους των
                   μηνυμάτων που παίρνουμε.
               </p>
                <div>
                   <label>Επικοινωνώ επειδή:</label>
                   <select name="reason" id="reason">
                    <option></option>
                    <option value="support">Έχω τεχνικό πρόβλημα στο Zino</option>
                    <option value="feature">Έχω μία ιδέα για το Zino</option>
                    <option value="abuse">Αναφέρω παραβίαση των Όρων Χρήσης</option>
                    <option value="biz">Θα ήθελα να συνεργαστούμε</option>
                    <option value="press">Είμαι δημοσιογράφος</option>
                   </select>
                </div>
                <div id="contact_support" style="display:none">
                    <div>
                        <label>Σε ποια σελίδα συνέβη το πρόβλημα; (διεύθυνση)</label>
                        <input type="text" name="bugurl" style="width:100%" />
                    </div>
                    <div>
                        <label>Τι ακριβώς συνέβη; Περιέγραψε με όσες λεπτομέρειες μπορείς.</label>
                        <textarea cols="70" rows="10" name="bugdescription" style="width:100%"></textarea>
                    </div>
                    <div>
                        <label>Τι συσκευή χρησιμοποιείς;</label>
                        <select name="bugdevice" id="bugdevice">
                         <option value="computer" selected="selected">Υπολογιστή</option>
                         <option value="palmtop">Palmtop ή iPod</option>
                         <option value="mobile">Κινητό τηλέφωνο</option>
                         <option value="console">Παιχνιδομηχανή</option>
                         <option value="other">Άλλη συσκευή</option>
                        </select>
                    </div>
                    <div id="bug_deviceinfo_computer">
                        <label>Τι λειτουργικό σύστημα χρησιμοποιείς;</label>
                        <select name="bugcomputeros" id="bugcomputeros">
                         <option></option>
                         <option value="windows">Windows</option>
                         <option value="linux">Linux</option>
                         <option value="mac">Mac OS</option>
                         <option value="bsd">BSD</option>
                         <option value="other">Κάποιο άλλο</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div id="bug_deviceinfo_palmtop" style="display:none">
                        <label>Τι λειτουργικό σύστημα χρησιμοποιείς;</label>
                        <select name="bugpalmos">
                         <option></option>
                         <option value="symbian">SymbianOS</option>
                         <option value="iphone">iPhone/iPod OS</option>
                         <option value="blackberry">RIM Blackberry</option>
                         <option value="windows">Windows Mobile</option>
                         <option value="linux">Linux</option>
                         <option value="palmos">Palm OS</option>
                         <option value="android">Android</option>
                         <option value="other">Κάποιο άλλο</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div id="bug_deviceinfo_console" style="display:none">
                        <label>Ποια παιχνιδομηχανή χρησιμοποιείς;</label>
                        <select name="bugconsole" id="bugconsole">
                            <option></option>
                            <option value="ps3">Playstation 3</option>
                            <option value="psp">PSP</option>
                            <option value="xbox360">Xbox 360</option>
                            <option value="wii">Wii</option>
                            <option value="other">Κάποια άλλη</option>
                        </select>
                    </div>
                    <div id="bug_osinfo_windows" style="display:none">
                        <label>Ποια έκδοση των Windows χρησιμοποιείς;</label>
                        <select name="bugwinversion" id="bugwinversion">
                         <option></option>
                         <option value="98">Windows 98</option>
                         <option value="Me">Windows Millenium</option>
                         <option value="2000">Windows 2000</option>
                         <option value="XP">Windows XP</option>
                         <option value="Vista">Windows Vista</option>
                         <option value="7">Windows 7</option>
                         <option value="other">Κάποια άλλη</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div id="bug_osinfo_linux" style="display:none">
                        <label>Ποια διανομή του Linux χρησιμοποιείς;</label>
                        <select name="buglinuxdistro" id="buglinuxdistro">
                         <option></option>
                         <option value="Ubuntu">Ubuntu</option>
                         <option value="OpenSUSE">OpenSUSE</option>
                         <option value="Fedora">Fedora</option>
                         <option value="Debian">Debian</option>
                         <option value="Mandriva">Mandriva</option>
                         <option value="LinuxMint">LinuxMint</option>
                         <option value="PCLinuxOS">PCLinuxOS</option>
                         <option value="Slackware">Slackware</option>
                         <option value="Gentoo">Gentoo</option>
                         <option value="CentOS">CentOS</option>
                         <option value="other">Κάποια άλλη</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div id="bug_osinfo_bsd" style="display:none">
                        <label>Ποια διανομή του BSD χρησιμοποιείς;</label>
                        <select name="bugbsddistro">
                         <option></option>
                         <option value="freebsd">FreeBSD</option>
                         <option value="openbsd">OpenBSD</option>
                         <option value="netbsd">NetBSD</option>
                         <option value="other">Κάποια άλλη</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div>
                        <label>Ποιο browser χρησιμοποιείς;</label>
                        <select name="bugbrowser" id="bugbrowser">
                         <option></option>
                         <option class="ie" value="ie">Internet Explorer</option>
                         <option class="ff" value="ff" style="">Mozilla Firefox</option>
                         <option class="chrome" value="chrome">Google Chrome</option>
                         <option class="opera" value="opera">Opera</option>
                         <option class="safari" value="safari">Safari</option>
                         <option value="other">Κάποιο άλλο</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div id="bug_browserinfo_ie" style="display:none">
                        <label>Ποια έκδοση του Internet Explorer χρησιμοποιείς;</label>
                        <select name="bugieversion" id="bugieversion">
                         <option></option>
                         <option value="6">Internet Explorer 6</option>
                         <option value="7">Internet Explorer 7</option>
                         <option value="8">Internet Explorer 8</option>
                         <option value="other">Κάποια άλλη</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div id="bug_browserinfo_ff" style="display:none">
                        <label>Ποια έκδοση του Mozilla Firefox χρησιμοποιείς;</label>
                        <select name="bugffversion" id="bugffversion">
                         <option></option>
                         <option value="1">Firefox 1</option>
                         <option value="1.5">Firefox 1.5</option>
                         <option value="2">Firefox 2</option>
                         <option value="3">Firefox 3</option>
                         <option value="3.5">Firefox 3.5</option>
                         <option value="other">Κάποια άλλη</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div id="bug_browserinfo_opera" style="display:none">
                        <label>Ποια έκδοση του Opera χρησιμοποιείς;</label>
                        <select name="bugoperaversion" id="bugoperaversion">
                         <option></option>
                         <option value="8.5">Opera 8.5</option>
                         <option value="9">Opera 9.0</option>
                         <option value="9.1">Opera 9.1</option>
                         <option value="9.2">Opera 9.2</option>
                         <option value="9.5">Opera 9.5</option>
                         <option value="9.6">Opera 9.6</option>
                         <option value="10">Opera 10</option>
                         <option value="other">Κάποια άλλη</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div id="bug_browserinfo_chrome" style="display:none">
                        <label>Ποια έκδοση του Google Chrome χρησιμοποιείς;</label>
                        <select name="bugchromeversion" id="bugchromeversion">
                         <option></option>
                         <option value="1">Chrome 1.0</option>
                         <option value="2">Chrome 2.0</option>
                         <option value="3">Chrome 3.0</option>
                         <option value="other">Κάποια άλλη</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div id="bug_browserinfo_safari" style="display:none">
                        <label>Ποια έκδοση του Safari χρησιμοποιείς;</label>
                        <select name="bugsafariversion" id="bugsafariversion">
                         <option></option>
                         <option value="2">Safari 2.0</option>
                         <option value="3">Safari 3.0</option>
                         <option value="3.1">Safari 3.1</option>
                         <option value="3.2">Safari 3.2</option>
                         <option value="4">Safari 4.0</option>
                         <option value="other">Κάποια άλλη</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                </div>
                <div id="contact_feature" style="display:none">
                    <p>Ευχαριστούμε που θέλεις να μοιραστείς την ιδέα σου μαζί μας!</p>
                    <div>
                        <label>Τι είναι αυτό που θα σου άρεσε να γίνει στο Zino?</label>
                        <select name="featurechoice" id="featurechoice">
                            <option></option>
                            <option value="customization">Χρωματικοί συνδιασμοί στο προφίλ μου</option>
                            <option value="sms">Ενημέρωση μέσω SMS</option>
                            <option value="rename">Δυνατότητα αλλαγής ονόματος</option>
                            <option value="newidea">Κάποια άλλη ιδέα (προσδιόρισε)</option>
                        </select>
                    </div>
                    <div id="feature_simple" style="display:none">
                        <p>Ευχαριστούμε! Θα λάβουμε υπ' όψιν μας την ψήφο σου.</p>
                    </div>
                    <div id="feature_extensive" style="display:none">
                        <label>Γράψε μας την ιδέα σου που θα ήθελες να δεις στο Zino:</label>
                        <textarea cols="70" rows="10" name="featuredescription" style="width:100%"></textarea>
                    </div>
                </div>
                <div id="contact_abuse" style="display:none">
                    <p>
                        Σ' ευχαριστούμε για το ενδιαφέρον σου να αναφέρεις αυτό το πρόβλημα.<br />
                        Τα στοιχεία του ατόμου που αναφέρει την παραβίαση των όρων χρήσης παραμένουν εμπιστευτικά.<br />
                        Θα εξετάσουμε κάθε αναφορά παραβίασης όρων. Η αναφορά παραβίασης όρων χρήσης δεν σημαίνει ότι θα
                        υπάρξει αυτόματα και δράση από πλευράς μας, αν δεν κρίνουμε ότι είναι απαραίτητη.
                    </p>
                    <div>
                        <label>Τι είδους παραβίαση των όρων χρήσης έγινε;</label>
                        <select name="abusetype">
                            <option></option>
                            <option value="porn">Πορνογραφικό υλικό</option>
                            <option value="imitation">Χρήση φωτογραφίας μου χωρίς να το θέλω</option>
                            <option value="fake">Fake λογαριασμός</option>
                            <option value="spam">Spam</option>
                            <option value="racism">Ρατσιστικό περιεχόμενο</option>
                            <option value="copyright">Παραβίαση πνευματικών δικαιωμάτων</option>
                            <option value="drugs">Απαγορευμένες ουσίες</option>
                        </select>
                    </div>
                    <div>
                        <label>Ποιο είναι το ψευδώνυμο του χρήστη που το έκανε;</label>
                        <input type="text" name="abuseusername" />
                    </div>
                    <div>
                        <label>Τι ακριβώς συνέβη;</label>
                        <textarea cols="70" rows="10" name="abusedescription" style="width:100%"></textarea>
                    </div>
                </div>
                <div id="contact_press" style="display:none">
                    <p>
                        Ευχαριστούμε για το ενδιαφέρον σας για την δημοσιογραφική κάλυψη του Zino.
                        Συμπλήρωσε τα παρακάτω στοιχεία, και θα έρθουμε σε επαφή μαζί σου.
                    </p>
                    <div>
                        <label>Όνομα και επώνυμο:</label>
                        <input type="text" name="pressfullname" />
                    </div>
                    <div>
                        <label>Είδος μέσου:</label>
                        <select name="presstype">
                            <option></option>
                            <option>Τηλεόραση</option>
                            <option>Ραδιόφωνο</option>
                            <option>Τύπος</option>
                            <option>Blog / Ιστοσελίδα</option>
                            <option>Άλλο</option>
                        </select>
                    </div>
                    <div>
                        <label>Επωνυμία:</label>
                        <input type="text" name="presscompany" />
                    </div>
                    <div>
                        <label>Τηλέφωνο:</label>
                        <input type="text" name="pressphone" />
                    </div>
                    <div>
                        <label>Λίγα λόγια για το ποιοι είστε και τι ενδιαφέρεστε να κάνουμε μαζί:</label>
                        <textarea cols="70" rows="10" name="pressdescription" style="width:100%"></textarea>
                    </div>
                </div>
                <div id="contact_biz" style="display:none">
                    <div>
                        <label>Όνομα και επώνυμο:</label>
                        <input type="text" name="bizfullname" />
                    </div>
                    <div>
                        <label>Εταιρία:</label>
                        <input type="text" name="bizcompany" />
                    </div>
                    <div>
                        <label>Θέση στην εταιρία:</label>
                        <input type="text" name="bizposition" />
                    </div>
                    <div>
                        <label>Τηλέφωνο:</label>
                        <input type="text" name="bizphone" />
                    </div>
                    <div>
                        <label>Πώς πιστεύετε ότι θα μπορούσαμε να συνεργαστούμε;</label>
                        <textarea cols="70" rows="10" name="bizdescription" style="width:100%"></textarea>
                    </div>
                </div>
                <input type="submit" value="Αποστολή" class="submit" id="submit" style="display:none" />
            </form><?php
        }
    }
?>
