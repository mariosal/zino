<?php
    class ElementAboutInfoContact extends Element {
        public function Render() {
            global $page;
            global $user;
            
            $page->SetTitle( 'Επικοινωνία' );
            
            ?><form>
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
                   <select name="reason">
                    <option></option>
                    <option value="support">Έχω τεχνικό πρόβλημα στο Zino</option>
                    <option value="feature">Έχω μία ιδέα για το Zino</option>
                    <option value="abuse">Αναφέρω παραβίαση των Όρων Χρήσης</option>
                    <option value="biz">Θα ήθελα να συνεργαστούμε</option>
                    <option value="press">Είμαι δημοσιογράφος</option>
                    <option value="purge">Θέλω να διαγράψω το λογαριασμό μου</option>
                   </select>
                </div>
                <div id="contact_support">
                    <div>
                        <label>Σε ποια σελίδα συνέβη το πρόβλημα; (διεύθυνση)</label>
                        <input type="text" name="url" style="width:100%" />
                    </div>
                    <div>
                        <label>Τι ακριβώς συνέβη; Περιέγραψε με όσες λεπτομέρειες μπορείς.</label>
                        <textarea cols="70" rows="10" name="description" style="width:100%"></textarea>
                    </div>
                    <div>
                        <label>Τι λειτουργικό σύστημα χρησιμοποιείς;</label>
                        <select name="os">
                         <option></option>
                         <option value="windows">Windows</option>
                         <option value="linux">Linux</option>
                         <option value="mac">Mac OS</option>
                         <option value="other">Κάποιο άλλο</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div>
                        <label>Ποια έκδοση των Windows χρησιμοποιείς;</label>
                        <select name="winversion">
                         <option></option>
                         <option value="98">Windows 98</option>
                         <option value="me">Windows Millenium</option>
                         <option value="2000">Windows 2000</option>
                         <option value="xp">Windows XP</option>
                         <option value="vista">Windows Vista</option>
                         <option value="7">Windows 7</option>
                         <option value="other">Κάποια άλλη</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                    <div>
                        <label>Ποια διανομή του Linux χρησιμοποιείς;</label>
                        <select name="linuxdistro">
                         <option></option>
                         <option>Ubuntu</option>
                         <option>OpenSUSE</option>
                         <option>Fedora</option>
                         <option>Debian</option>
                         <option>Mandriva</option>
                         <option>LinuxMint</option>
                         <option>PCLinuxOS</option>
                         <option>Slackware</option>
                         <option>Gentoo</option>
                         <option>CentOS</option>
                         <option>Κάποια άλλη</option>
                         <option>Δεν ξέρω</option>
                        </select>
                    </div>
                    <div>
                        <label>Ποιο browser χρησιμοποιείς;</label>
                        <select name="browser">
                         <option></option>
                         <option value="ie">Internet Explorer</option>
                         <option value="ff">Mozilla Firefox</option>
                         <option value="chrome">Google Chrome</option>
                         <option value="opera">Opera</option>
                         <option value="safari">Safari</option>
                         <option value="other">Κάποιο άλλο</option>
                         <option value="dontknow">Δεν ξέρω</option>
                        </select>
                    </div>
                </div>
                <div id="contact_feature">
                    <p>Ευχαριστούμε που θέλεις να μοιραστείς την ιδέα σου μαζί μας!</p>
                    <div>
                        <label>Τι είναι αυτό που θα σου άρεσε να γίνει στο Zino?</label>
                        <select name="featurechoice">
                            <option></option>
                            <option value="customization">Χρωματικοί συνδιασμοί στο προφίλ μου</option>
                            <option value="sms">Ενημέρωση μέσω SMS</option>
                            <option value="music">Μουσική στο προφίλ μου</option>
                            <option value="purge">Δυνατότητα διαγραφής προφίλ</option>
                            <option value="rename">Δυνατότητα αλλαγής ονόματος</option>
                            <option value="newidea">Κάποια άλλη ιδέα (προσδιόρισε)</option>
                        </select>
                    </div>
                    <div>
                        <label>Γράψε μας την ιδέα σου που θα ήθελες να δεις στο Zino:</label>
                        <textarea cols="70" rows="10" name="featuredescription" style="width:100%"></textarea>
                    </div>
                </div>
            </form><?php
        }
    }
?>
