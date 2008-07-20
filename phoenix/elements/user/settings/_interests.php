<?php
	class ElementUserSettingsInterests extends Element {
        public function Render() {
            global $rabbit_settings;
            global $user;
            global $libs;
            //----------------
            global $page;
            
            $page->AttachScript( 'js/user/suggest.js' );
            $page->AttachStyleSheet( 'css/user/settings.css' );
            //----------------
            
            $libs->Load( 'tag' );
            $finder = New TagFinder();
            $tags = $finder->FindByUser( $user );
            $bytype = array(
                TAG_HOBBIE => array(),
                TAG_MOVIE => array(),
                TAG_BOOK => array(),
                TAG_SONG => array(),
                TAG_ARTIST => array(),
                TAG_GAME => array(),
                TAG_SHOW => array()
            );
            foreach ( $tags as $tag ) {
                $bytype[ $tag->Typeid ][] = $tag;
            }
            
            $hobbies = $bytype[ TAG_HOBBIE ];
            $movies = $bytype[ TAG_MOVIE ];
            $books = $bytype[ TAG_BOOK ];
            $songs = $bytype[ TAG_SONG ];
            $artists = $bytype[ TAG_ARTIST ];
            $games = $bytype[ TAG_GAME ];
            $shows = $bytype[ TAG_SHOW ];
            ?><div class="option">
                <label>Hobbies:</label>
                <div class="setting">
                    <ul class="interesttags hobbies"><?php
                        foreach ( $hobbies as $hobbie ) {
                            ?><li>
                                <div class="aplbubble">
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_left.png" style="" />
                                    <span style="margin-left:0.5px"><?php
                                    echo htmlspecialchars( $hobbie->Text );
                                    ?></span>
                                    <a href="" onclick="Settings.RemoveInterest( '<?php
                                    echo $hobbie->Id;
                                    ?>' , this );return false;" class="delete"><img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>delete.png" /></a>
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_right.png" />
                                </div>
                            </li><?php
                        }
                    ?></ul>
                    <div class="add hobbies">
                        <input type="text" onkeypress="Suggest.inputMove( event, 'hobbies' );"  />
                        <a href="" onclick="return false;"><img src="<?php
                        echo $rabbit_settings[ 'imagesurl' ];
                        ?>add.png" alt="Προσθήκη" title="Προσθήκη" /></a>
                        <form style="display: inline">
                        	<?php // This thing will be dynamic a little later ?>
                        	<select size="5" onkeypress="Suggest.selectMove( event, 'hobbies' );">
                        		<option value="test">test</option>
                        		<option value="best">best</option>
                        		<option value="guest">guest</option>
                        		<option value="fest">fest</option>
                        		<option value="nest">nest</option>
                        		<option vallue="budapest">budapest</option>
                        	</select>
                        </form>
                    </div>
                </div>
            </div>
            <div class="barfade">
                <div class="leftbar"></div>
                <div class="rightbar"></div>
            </div>
            <div class="option">
                <label>Αγαπημένα τραγούδια:</label>
                <div class="setting">
                    <ul class="interesttags songs"><?php
                        foreach ( $songs as $song ) {
                            ?><li>
                                <div class="aplbubble">
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_left.png" />
                                    <span style="margin-left:0.5px"><?php
                                    echo htmlspecialchars( $song->Text );
                                    ?></span>
                                    <a href="" onclick="Settings.RemoveInterest( '<?php
                                    echo $song->Id;
                                    ?>' , this );return false;" class="delete"><img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>delete.png" /></a>
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_right.png" />
                                </div>
                            </li><?php
                        }
                    ?></ul>
                    <div class="add songs">
                        <input type="text" onkeypress="Suggest.inputMove( event, 'songs' );"/>
                        <a href="" onclick="return false;"><img src="<?php
                        echo $rabbit_settings[ 'imagesurl' ];
                        ?>add.png" alt="Προσθήκη" title="Προσθήκη" /></a>
                        <form>
                        	<select size="5" onkeypress="Suggest.selectMove( event, 'songs' );"/>
                        </form>
                    </div>
                </div>
            </div>
            <div class="barfade">
                <div class="leftbar"></div>
                <div class="rightbar"></div>
            </div>
            <div class="option">
                <label>Αγαπημένες ταινίες:</label>
                <div class="setting">
                    <ul class="interesttags movies"><?php
                        foreach ( $movies as $movie ) {
                            ?><li>
                                <div class="aplbubble">
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_left.png" />
                                    <span style="margin-left:0.5px"><?php
                                    echo htmlspecialchars( $movie->Text );
                                    ?></span>
                                    <a href="" onclick="Settings.RemoveInterest( '<?php
                                    echo $movie->Id;
                                    ?>' , this );return false;" class="delete"><img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>delete.png" /></a>
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_right.png" />
                                </div>
                            </li><?php
                        }
                    ?></ul>
                    <div class="add movies">
                        <input type="text" onkeypress="Suggest.inputMove( event, 'movies' );"/>
                        <a href="" onclick="return false;"><img src="<?php
                        echo $rabbit_settings[ 'imagesurl' ];
                        ?>add.png" alt="Προσθήκη" title="Προσθήκη" /></a>
                        <form>
                        	<select size="5" onkeypress="Suggest.selectMove( event, 'movies' );"/>
                        </form>
                    </div>
                </div>
            </div>
            <div class="barfade">
                <div class="leftbar"></div>
                <div class="rightbar"></div>
            </div>
            <div class="option">
                <label>Αγαπημένες σειρές:</label>
                <div class="setting">
                    <ul class="interesttags shows"><?php
                        foreach ( $shows as $show) {
                            ?><li>
                                <div class="aplbubble">
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_left.png" />
                                    <span style="margin-left:0.5px"><?php
                                    echo htmlspecialchars( $show->Text );
                                    ?></span>
                                    <a href="" onclick="Settings.RemoveInterest( '<?php
                                    echo $show->Id;
                                    ?>' , this );return false;" class="delete"><img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>delete.png" /></a>
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_right.png" />
                                </div>
                            </li><?php
                        }
                    ?></ul>
                    <div class="add shows">
                        <input type="text" onkeypress="Suggest.inputMove( event, 'shows' );"/>
                        <a href="" onclick="return false;"><img src="<?php
                        echo $rabbit_settings[ 'imagesurl' ];
                        ?>add.png" alt="Προσθήκη" title="Προσθήκη" /></a>
                        <form>
                        	<select size="5" onkeypress="Suggest.selectMove( event, 'shows' );"/>
                        </form>
                    </div>
                </div>
            </div>
            <div class="barfade">
                <div class="leftbar"></div>
                <div class="rightbar"></div>
            </div>
            <div class="option">
                <label>Αγαπημένα βιβλία:</label>
                <div class="setting">
                    <ul class="interesttags books"><?php
                        foreach ( $books as $book ) {
                            ?><li>
                                <div class="aplbubble">
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_left.png" />
                                    <span style="margin-left:0.5px"><?php
                                    echo htmlspecialchars( $book->Text );
                                    ?></span>
                                    <a href="" onclick="Settings.RemoveInterest( '<?php
                                    echo $book->Id;
                                    ?>' , this );return false;" class="delete"><img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>delete.png" /></a>
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_right.png" />
                                </div>
                            </li><?php
                        }
                    ?></ul>
                    <div class="add books">
                        <input type="text" onkeypress="Suggest.inputMove( event, 'books' );"/>
                        <a href="" onclick="return false;"><img src="<?php
                        echo $rabbit_settings[ 'imagesurl' ];
                        ?>add.png" alt="Προσθήκη" title="Προσθήκη" /></a>
                        <form>
                        	<select size="5" onkeypress="Suggest.selectMove( event, 'books' );"/>
                        </form>
                    </div>
                </div>
            </div>
            <div class="barfade">
                <div class="leftbar"></div>
                <div class="rightbar"></div>
            </div>
            <div class="option">
                <label>Αγαπημένοι καλλιτέχνες:</label>
                <div class="setting">
                    <ul class="interesttags artists"><?php
                        foreach ( $artists as $artist ) {
                            ?><li>
                                <div class="aplbubble">
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_left.png" />
                                    <span style="margin-left:0.5px"><?php
                                    echo htmlspecialchars( $artist->Text );
                                    ?></span>
                                    <a href="" onclick="Settings.RemoveInterest( '<?php
                                    echo $artist->Id;
                                    ?>' , this );return false;" class="delete"><img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>delete.png" /></a>
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_right.png" />
                                </div>
                            </li><?php
                        }
                    ?></ul>
                    <div class="add artists">
                        <input type="text" onkeypress="Suggest.inputMove( event, 'artists' );"/>
                        <a href="" onclick="return false;"><img src="<?php
                        echo $rabbit_settings[ 'imagesurl' ];
                        ?>add.png" alt="Προσθήκη" title="Προσθήκη" /></a>
                        <form>
                        	<select size="5" onkeypress="Suggest.selectMove( event, 'artists' );"/>
                        </form>
                    </div>
                </div>
            </div>
            <div class="barfade">
                <div class="leftbar"></div>
                <div class="rightbar"></div>
            </div>
            <div class="option">
                <label>Αγαπημένα παιχνίδια:</label>
                <div class="setting">
                    <ul class="interesttags games"><?php
                        foreach ( $games as $game ) {
                            ?><li>
                                <div class="aplbubble">
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_left.png" />
                                    <span style="margin-left:0.5px"><?php
                                    echo htmlspecialchars( $game->Text );
                                    ?></span>
                                    <a href="" onclick="Settings.RemoveInterest( '<?php
                                    echo $game->Id;
                                    ?>' , this );return false;" class="delete"><img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>delete.png" /></a>
                                    <img src="<?php
                                    echo $rabbit_settings[ 'imagesurl' ];
                                    ?>aplbubble_right.png" />
                                </div>
                            </li><?php
                        }
                    ?></ul>
                    <div class="add games">
                        <input type="text" onkeypress="Suggest.inputMove( event, 'games' );"/>
                        <a href="" onclick="return false;"><img src="<?php
                        echo $rabbit_settings[ 'imagesurl' ];
                        ?>add.png" alt="Προσθήκη" title="Προσθήκη" /></a>
                        <form>
                        	<select size="5" onkeypress="Suggest.selectMove( event, 'games' );"/>
                        </form>
                    </div>
                </div>
            </div>
            <div class="aplbubble creation">
                <img src="<?php
                echo $rabbit_settings[ 'imagesurl' ];
                ?>aplbubble_left.png" />
                <span></span>
                <a href="" style="display:none;" class="delete"><img src="<?php
                echo $rabbit_settings[ 'imagesurl' ];
                ?>delete.png" /></a>
                <img src="<?php
                echo $rabbit_settings[ 'imagesurl' ];
                ?>aplbubble_right.png" />
            </div><?php
        }
    }
?>
