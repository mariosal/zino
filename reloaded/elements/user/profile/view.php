<?php
	function ElementUserProfileView( tInteger $id, tString $name, tString $subdomain, tBoolean $oldcomments, tBoolean $viewingalbums, tBoolean $viewingfriends ) {
		global $user;
		global $page;
		global $water;
		global $libs;
		global $xc_settings;
        
        $userid = $id->Get();
        $name = $name->Get();
		$subdomain = $subdomain->Get();
        $oldcomments = $oldcomments->Get();
        
		$page->AttachScript( 'js/userprofile.js' );
		
		$libs->Load( 'search' );
		$libs->Load( 'comment' );
        $libs->Load( 'albums' );
        $libs->Load( 'relations' );
		
		$libs->Load( 'question' );
		
		if ( $userid > 0 ) {
            if ( $userid == $user->Id() ) {
                $theuser = $user;
            }
            else {
                $theuser = New User( $userid );
            }
		}
		else if ( $name != '' ) {
            if ( strtolower( $name ) == strtolower( $user->Username() ) ) {
                $theuser = $user;
            }
            else {
                $theuser = New User( $name );
            }
		}
		else if ( $subdomain != '' ) {
            if ( strtolower( $subdomain ) == strtolower( $user->Subdomain() ) ) {
                $theuser = $user;
            }
            else {
                $theuser = New User_BySubdomain( $subdomain );
            }
		}
		if ( !$theuser->Exists() || $theuser->Locked() ) {
			$page->SetTitle( 'Δε βρέθηκε ο χρήστης' );
			?>Ο χρήστης που προσπαθείτε να δείτε δεν υπάρχει στη βάση δεδομένων!<?php
			return;
		}
        $id = $theuser->Id();
        $commentscount = $theuser->Contribs();
        
        $page->SetTitle( $theuser->Username() );
        $page->AttachStyleSheet( 'css/user.css' );
        $page->AttachStyleSheet( 'css/articles.css' );
        $page->AttachStyleSheet( 'css/rounded.css' );
        $page->AttachScript( 'js/coala.js' );
        if ( $theuser->Subtitle() ) {
            $description = htmlspecialchars( $theuser->Subtitle() );
        }
        else if ( $theuser->HasSpecialRank() ) {
            $rankshown = true;
            $description = "";
        }
        else if ( $commentscount ) {
            $description = "";
        }
        else {
            $description = "Νέος Χρήστης";
        }

		$qanswernum = count( $theuser->GetAnsweredQuestions() );
		if ( $qanswernum != 0 || $theuser->Id() == $user->Id() ) {
			$viewquestion = true;
		}
		else {
			$viewquestion = false;
		}

		$libs->Load( 'search' );
		$libs->Load( 'article' );
		
		if ( $theuser->CanModifyStories() ) {
			$search = New Search_Articles();
			$search->SetFilter( 'editor' , $theuser->Id() );
			$search->SetFilter( 'typeid', 0 );
			$search->SetFilter( 'delid', 0 );
			$search->SetFilter( 'revision_minor', 'no' );
		//	$search->SetNegativeFilter( 'category', 0 );    <--Category 0 means No Category
			$search->SetSortMethod( 'date', 'DESC' );
			$search->SetRequirement( 'text' );
            $search->SetRequirement( 'editors' );
			$search->SetLimit( 20 );
			$articles = $search->Get();
		}
		else {
			$articles = array();
		}
		$articlesnum = count( $articles );
		$usericonid = $theuser->Icon();
		$theuser->AddPageView();
		$viewingtabs = 0;
		$friends = $theuser->GetFriends();
		$fans = $theuser->GetFans();
        $allalbums = Albums_RetrieveUserAlbums( $theuser->Id(), true );
        $viewalbums = count( $allalbums ) > 0 || $theuser->Id() == $user->Id();
		$viewfriends = count( $friends ) > 0;
		$viewfans = count( $fans ) > 0;
		?><div class="userprofile"><br /><br /><br />
			<div class="top"><span>
                <?php
                Element( 'image', $theuser->Icon(), 50, 50, 'avatar', '', $theuser->Username(), $theuser->Username() );
                ?>
				</span><?php
				if ( $theuser->Position() > 0 ) {
					Element( 'user/avatar/view', $theuser );
				}
				if ( $theuser->HasSpecialRank() ) {
				}
				?><span class="abnormality1"><?php
					echo $theuser->Username(); 
				?></span><br />
				<span class="abnormality2"><?php
					echo $description;
				?></span><br /><br />
                
                <div class="tabs" id="userprofile_tabs"<?php
                    if ( $user->Id() != $theuser->Id() ) {
                        ?> style="padding-right:60px"<?php
                    }
                    ?>><?php
                    if ( $user->Id() == $theuser->Id() && $user->Rights() >= $xc_settings[ 'readonly' ] ) {
                        $page->AttachStylesheet( 'css/modal.css' );
                        $page->AttachStylesheet( 'css/colorpicker.css' );
                        $page->AttachStylesheet( 'css/colorpicker_ie.css', true );
                        $page->AttachScript( 'js/modal.js' );
                        $page->AttachScript( 'js/colorpicker.js' );
                        ?>
                        <a href="" id="paintbrush" onclick="Profile.ChangeColor();return false" title="Αλλαγή χρωματικού συνδιασμού">
                            <img src="<?php
                            echo $xc_settings[ 'staticimagesurl' ];
                            ?>icons/paintbrush.png" style="width:16px;height:16px" />
                        </a>
                        <?php
                    }
                    
                    if ( $user->Rights() >= $xc_settings[ 'readonly' ] ) {
                        ?><div style="float:left;padding-top:9px;"><?php
                        if ( !$user->IsAnonymous() && $user->Id() != $theuser->Id() ) {
                            $relations = AllRelations();
                            if ( count( $relations ) ) {		
                            	$is_friend = $user->IsFriend( $theuser->Id() );
								?><span id="friendadd">
								<a href="" onclick="Friends.ShowAll( true );return false;"><img src="<?php
	                            echo $xc_settings[ 'staticimagesurl' ];
								?>icons/group.png" title="Προσθήκη στους φίλους μου" alt="Προσθήκη στους φίλους" width="16" height="16" />
								</a></span>
	                            <?php
							}
						}	
                        if ( $user->Id() != $theuser->Id() ) {
                            ?>&nbsp;<a href="?p=pms&amp;id=new&amp;to=<?php
                            echo $theuser->Username();
                            ?>"><img src="<?php
                            echo $xc_settings[ 'staticimagesurl' ];
                            ?>icons/usercontact.png" title="Αποστολή Μηνύματος" alt="Αποστολή Μηνύματος" width="16" height="16" /></a><?php
                        }
                        if ( $user->CanModifyCategories() && ( $user->Rights() > $theuser->Rights() || $user->Id() == $theuser->Id() ) ) { 
                            ?>&nbsp;<a href="?p=useradmin&amp;id=<?php
                            echo $theuser->Id();
                            ?>"><img src="<?php
                            echo $xc_settings[ 'staticimagesurl' ];
                            ?>icons/group_edit.png" title="Επεξεργασία Δικαιωμάτων" alt="Επεξεργασία Δικαιωμάτων" width="16" height="16" /></a><?php
                        }
                        if ( $user->IsSysOp() && $user->Id() != $theuser->Id() ) {
                            ?>&nbsp;<a href="?p=su&amp;username=<?php
                            echo $theuser->Username();
                            ?>"><img src="<?php
                            echo $xc_settings[ 'staticimagesurl' ];
                            ?>icons/user_go.png" title="Είσοδος ως <?php
                            echo $theuser->Username();
                            ?>" alt="Είσοδος ως <?php
                            echo $theuser->Username();
                            ?>" /></a><?php
                        }
                        ?></div><?php
                    }
					if ( $viewalbums ) {
						?><div class="rightism"></div>
						<div class="tab"><a>Albums</a></div>
						<div class="leftism"></div><?php
					}
					if ( $articlesnum > 0 || $user->Id() == $theuser->Id() ) {
						?><div class="rightism"></div>
						<div class="tab"><a>Άρθρα</a></div>
						<div class="leftism"></div><?php
					}
					if ( $viewfriends || $viewfans ) {
						?><div class="rightism"></div>
						<div class="tab"><a>Φίλοι</a></div>
						<div class="leftism"></div><?php
					}
					if ( $viewquestion ) { 
						?><div class="rightism"></div>
						<div class="tab"><a>Ερωτήσεις</a></div>
						<div class="leftism"></div><?php
					}
					if ( $theuser->Blog() != 0 || $user->Id() == $theuser->Id() ) {
						?><div class="rightism"></div>
						<div class="tab"><a>Χώρος</a></div>
						<div class="leftism"></div><?php
					}
					?><div class="rightism activeright"></div>
					<div class="tab active"><a><?php 
						echo $theuser->Username(); 
					?></a></div>
					<div class="leftism activeleft"></div>
					<br />
				</div>
			</div>
			<div id="alltabs">
				<div id="tab<?php 
					$search = New Search_Comments();
					$search->SetFilter( 'typeid', 1 );
					$search->SetFilter( 'page', $theuser->Id(), 1 );
					$search->SetFilter( 'delid', 0 ); // do not show deleted comments
					$search->SetSortMethod( 'date', 'DESC' ); //sort by date, newest shown first
					
					if ( $oldcomments ) { // If the user wants to see all comments
						$search->SetLimit( 10000 );
					}
					else {
						$search->SetLimit( 50 );  // show no more than 50 comments
					}
					
					$comments = $search->GetParented( true ); // get comments
					
					echo $viewingtabs++;
					?>" class="profile"<?php
                    $rgb = Color_Decode( $theuser->ProfileColor() );
                    if ( $rgb !== false ) {
                        ?> style="background-image:url('<?php
                        echo $xc_settings[ 'imagesurl' ];
                        ?>gradient/<?php
                        echo $rgb[ 0 ];
                        ?>/<?php
                        echo $rgb[ 1 ];
                        ?>/<?php
                        echo $rgb[ 2 ];
                        ?>');width:100%;height:100%"<?php
                    }
                    ?>><?php
					Element( 'user/profile/main' , $theuser, $articlesnum, $oldcomments );
					?><div style="clear:both"></div>
			        <br /><br />
			        <div class="comments" id="comments"><?php
                        Element( 'comment/import' );
						Element( 'comment/reply', $theuser, 1 );
						Element( 'comment/list' , $comments , 0 , 0 );
                    ?></div>
                    <br /><?php
                    Element( "ad/leaderboard" );
                    ?><br />
				</div><?php
				if ( $theuser->Blog() != 0 || $user->Id() == $theuser->Id() ) {
					?><div id="tab<?php 
						echo $viewingtabs++;
					?>" style="display:none">
					<br /><?php
						Element( 'user/profile/space' , $theuser );
					?></div><?php
				}
				
				if ( $viewquestion ) {
					?><div id="tab<?php
					echo $viewingtabs++; 
					?>" style="display:none"><?php	
					Element( 'user/profile/questions' , $theuser , $qanswernum );
					?></div><?php
				}
				
				if ( $viewfriends || $viewfans ) {
					?><div id="tab<?php
					$friendstab = $viewingtabs;
					echo $viewingtabs++;
					?>" style="display:none">
					<br /><?php
					if ( $viewfriends ) {
						Element( 'user/profile/friends' , $friends, $user->Id(), false, "" );
					} ?><br /><br /><?php
					if ( $viewfans ) {
						Element( 'user/profile/fans' , $theuser , $fans );
					}
                    ?></div><?php
				}
				if ( $articlesnum > 0 || $user->Id() == $theuser->Id() ) {
					?><div id="tab<?php 
						echo $viewingtabs++; 
					?>" style="display:none"><?php
						Element( 'user/articles' , $theuser , $articles );
					?></div><?php
				}
				if ( $viewalbums ) {
					?><div id="tab<?php
						echo $viewingtabs;
					?>" style="display:none"><br /><?php
					Element( 'user/albums' , $theuser , $allalbums ) ;
					?></div><?php
				}
			?></div>
		</div>
		<div style="display: none;" id="userprofile_viewalbums"><?php
			if ( $viewingalbums->Get() ) {
				?>yes<?php
			}
			else {
				?>no<?php
			}
		?></div>
		<div style="display: none;" id="userprofile_viewfriends"><?php
			if ( $viewingfriends->Get() ) {
				?>yes<?php
			}
			else {
				?>no<?php
			}
		?></div>
		<div style="display: none;" id="userprofile_viewingtabs"><?php
			echo $viewingtabs;
		?></div>
		<div style="display: none;" id="userprofile_friendstab"><?php
			echo $friendstab;
		?></div><?php
		if( !$user->IsAnonymous() && $user->Id() != $theuser->Id() && count( $relations ) ) { // $relations is defined at line 139 and $is_friend at line 141
			Element( 'user/profile/frelations', $relations, $is_friend, $theuser->Id() );
		}
	}
?>
