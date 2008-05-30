<?php

    class TestTag extends Testcase {
        protected $mAppliesTo='libs/tag';
        private $mUser1;
        private $mUser2;
        private $mBookTag;
        private $mMovieTag1;
        private $mMovieTag2;

        public function SetUp() {
        	global $libs;
        	$libs->Load( 'tag' );
        	
            $finder = New UserFinder();
            $users = $finder->FindByName( 'testtag1' );
            if ( is_object( $users ) ) {
            	$users->Delete();
            }
            $users = $finder->FindByName( 'testtag2' );
            if ( is_object( $users ) ) {
            	$users->Delete();
            }

            $user = New User();
            $user->Name = 'testtag1';
            $user->Subdomain = 'testtag1';
            $user->Save();

            $this->mUser1 = $user;

            $user = New User();
            $user->Name = 'testtag2';
            $user->Subdomain = 'testtag2';
            $user->Save();

            $this->mUser2 = $user;
        }
        public function TestClassesExist() {
            $this->Assert( class_exists( 'Tag' ), 'Tag class does not exist' );
            $this->Assert( class_exists( 'TagFinder' ), 'TagFinder class does not exist' );
        }
        public function TestFunctionsExist() {
            $this->Assert( function_exists( 'Tag_Clear' ), 'Tag_Clear function does not exist' );
        }
        public function TestMethodsExist() {
            $tag = New Tag();

            $this->Assert( method_exists( $tag, 'Save' ), 'Tag::Save method does not exist' );
            $this->Assert( method_exists( $tag, 'Delete' ), 'Tag::Delete method does not exist' );
            $this->Assert( method_exists( $tag, 'Exists' ), 'Tag::Exists method does not exist' );
            $this->Assert( method_exists( $tag, 'MoveAfter' ), 'Tag::MoveAfter method does not exist' );
            $this->Assert( method_exists( $tag, 'MoveBefore' ), 'Tag::MoveBefore method does not exist' );

            $finder = New TagFinder();
            $this->Assert( method_exists( $finder, 'FindByUser' ), 'TagFinder::FindByUser method does not exist' );
            $this->Assert( method_exists( $finder, 'FindByTextAndType' ), 'TagFinder::FindByTextAndType method does not exist' );
        }
        public function TestCreate() {
            $user = $this->mUser1;

            $tag = New Tag();
            $tag->Userid = $user->Id;
            $tag->Typeid = TAG_MOVIE;
            $tag->Text = 'Sin City';
            $this->AssertFalse( $tag->Exists(), 'Tag appears to exist before saving' );
            $tag->Save();
            $this->Assert( $tag->Exists(), 'Tag does not appear to exist after saving' );

            $tag = New Tag();
            $tag->Userid = $user->Id;
            $tag->Typeid = TAG_BOOK;
            $tag->Text = 'The journal of a Magus';
            $tag->Save();

            $this->mBookTag = $tag;

            $user = $this->mUser2;

            $tag1 = New Tag();
            $tag1->Typeid = TAG_MOVIE;
            $tag1->Userid = $user->Id;
            $tag1->Text = 'Sin City';
            $tag1->Save();

            $this->mMovieTag1 = $tag1;

            $tag2 = New Tag();
            $tag2->Userid = $user->Id;
            $tag2->Typeid = TAG_MOVIE;
            $tag2->Text = 'Straight Story'; // NOTICE: Straight Story by David Lynch; not to be confused with the greek comedy.
            $tag2->Next = $tag1->Id;
            $tag2->Save();

            $this->mMovieTag2 = $tag2;
        }
        public function TestFindByUser() {
            $finder = New TagFinder();
            $tags = $finder->FindByUser( $this->mUser1 );
            
            $this->Assert( is_array( $tags ), 'Finder::FindByUser did not return an array' );
            $this->AssertEquals( 2, count( $tags ), 'Finder::FindByUser did not return the right number of tags' );
            
            $texts = array( 'Sin City', 'Journal of a Magus' );
            $types = array( TAG_MOVIE, TAG_BOOK );
            for ( $i = 0; $i < 2; ++$i ) {
                $tag = $tags[ $i ];
                $this->Assert( $tag instanceof Tag, 'Finder::FindByUser did not return an array of tags' );
                $this->AssertEquals( $texts[ $i ], $tag->Text, 'Tag returned by Finder::FindByUser doesn\'t have the right text, or it is returned in wrong order' );
                $this->AssertEquals( $types[ $i ], $tag->Typeid, 'Tag returned by Finder::FindByUser doesn\'t have the right type, or it is returned in wrong order' );
            }
        }
        public function TestFindByTextAndType() {
            $finder = New TagFinder();
            $tags = $finder->FindByTextAndType( 'Sin City', TAG_MOVIE );

            $this->Assert( is_array( $tags ), 'Finder::FindByTextAndType did not return an array' );
            $this->AssertEquals( 2, count( $tags ), 'Finder::FindByTextAndType did not return the right number of tags' );
            
            $users = array( 'testtag1', 'testtag2' );
            for ( $i = 0; $i < 2; ++$i ) {
                $tag = $tags[ $i ];
                $this->Assert( $tag instanceof Tag, 'Finder::FindByTextAndType did not return an array of tags' );
                $this->AssertEquals( $users[ $i ], $tag->User->Name, 'Tag returned by Finder::FindByTextAndType doesn\'t have the right user, or it is returned in wrong order' );
            }
        }
        public function TestFindSuggestions() {
            $finder = New TagFinder();
            $texts = $inder->FindSuggestions( 'S', TAG_MOVIE );

            $this->Assert( is_array( $tags ), 'Finder::FindSuggestions did not return an array' );
            
            foreach ( $texts as $text ) {
                $this->Assert( is_string( $text ), 'Finder::FindSuggestions did not return an array of strings' );
                $this->AssertEquals( 'S', $text[ 0 ], 'Finder::FindSuggestions returned a wrong text' );
            }
        }
        public function TestEdit() {
            // no ability to edit tags
        }
        public function TestDelete() {
            $this->Assert( $this->mBookTag->Exists(), 'Tag does not appear to exist before deleting' );
            $this->mBookTag->Delete();
            $this->AssertFalse( $this->mBookTag->Exists(), 'Tag appears to exist after deleting' );

            $finder = New TagFinder();
            $finder->FindByUser( $this->mUser1 );

            $this->Assert( is_array( $tags ), 'Finder::FindByUser did not return an array' );
            $this->AssertEquals( 1, count( $tags ), 'Finder::FindByUser did not return the right number of tags' );
            
            $texts = array( 'Sin City' );
            $types = array( TAG_MOVIE );
            for ( $i = 0; $i < 1; ++$i ) {
                $tag = $tags[ $i ];
                $this->Assert( $tag instanceof Tag, 'Finder::FindByUser did not return an array of tags' );
                $this->AssertEquals( $texts[ $i ], $tag->Text, 'Tag returned by Finder::FindByUser doesn\'t have the right text, or it is returned in wrong order' );
                $this->AssertEquals( $types[ $i ], $tag->Typeid, 'Tag returned by Finder::FindByUser doesn\'t have the right type, or it is returned in wrong order' );
            }
        }
        public function TestReorder() {
            $finder = New TagFinder();

            $this->mMovieTag2->MoveBefore( $this->mMovieTag1 );

            $tags = $finder->FindByUser( $this->mUser2 );

            $texts = array( 'Straight Story', 'Sin City' );
            $types = array( TAG_MOVIE, TAG_MOVIE );
            for ( $i = 0; $i < 1; ++$i ) {
                $tag = $tags[ $i ];
                $this->Assert( $tag instanceof Tag, 'Finder::FindByUser did not return an array of tags' );
                $this->AssertEquals( $texts[ $i ], $tag->Text, 'Tag returned by Finder::FindByUser doesn\'t have the right text, or it is returned in wrong order' );
                $this->AssertEquals( $types[ $i ], $tag->Typeid, 'Tag returned by Finder::FindByUser doesn\'t have the right type, or it is returned in wrong order' );
            }

            $this->mMovieTag2->MoveAfter( $this->mMovieTag1 );

            $tags = $finder->FindByUser( $this->mUser2 );

            $texts = array( 'Sin City', 'Straight Story' );
            $types = array( TAG_MOVIE, TAG_MOVIE );
            for ( $i = 0; $i < 1; ++$i ) {
                $tag = $tags[ $i ];
                $this->Assert( $tag instanceof Tag, 'Finder::FindByUser did not return an array of tags' );
                $this->AssertEquals( $texts[ $i ], $tag->Text, 'Tag returned by Finder::FindByUser doesn\'t have the right text, or it is returned in wrong order' );
                $this->AssertEquals( $types[ $i ], $tag->Typeid, 'Tag returned by Finder::FindByUser doesn\'t have the right type, or it is returned in wrong order' );
            }

        }
        public function TestClear() {
            Tag_Clear( $this->mUser1 );

            $finder = New TagFinder();

            $tags = $finder->FindByUser( $this->mUser1 );
            $this->Assert( is_array( $tags ), 'TagFinder::FindByUser did not return an array' );
            $this->Assert( empty( $tags ), 'Array returned by TagFinder::FindByUser, after calling Tag_Clear, was not empty' );

            Tag_Clear( $this->mUser2 ); // this should accept user objects or user ids!
        }
        public function TearDown() {
            if ( is_object( $this->mUser1 ) ) {
                $this->mUser1->Delete();
            }
            if ( is_object( $this->mUser2 ) ) {
                $this->mUser2->Delete();
            }
        }
    }

    return New TestTag();

?>
