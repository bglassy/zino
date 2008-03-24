<?php
    /*
        Developer: Dionyziz
    */
    
    abstract class Testcase extends Overloadable {
        protected $mTester;
        protected $mName;
        protected $mAppliesTo;
        
        final public function Testcase() {
        }
        public function SetUp() { // overridable
        }
        public function TearDown() { // overridable
        }
        final public function AppliesTo() {
            return $this->mAppliesTo;
        }
        protected function SetName( $name ) {
            w_assert( is_string( $name ) );
            w_assert( !empty( $name ) );
            $this->mName = $name;
        }
        protected function GetName() {
            return $this->mName;
        }
        protected function AssertNull( $actual, $message = '' ) {
            return $this->InformTester(
                New AssertResult( is_null( $actual ), $message, $actual, null )
            );
        }
        protected function AssertNotNull( $actual, $message = '' ) {
            return $this->InformTester(
                New AssertResult( !is_null( $actual ), $message, $actual, null )
            );
        }
        protected function AssertEquals( $expected, $actual, $message = '' ) {
            return $this->InformTester(
                New AssertResult( $actual === $expected, $message, $actual, $expected )
            );
        }
        protected function AssertNotEquals( $notexpected, $actual, $message = '' ) {
            return $this->InformTester(
                New AssertResult( $actual != $expected, $message, $actual, $expected )
            );
        }
        protected function Assert( $actual, $message = '' ) {
            return $this->AssertEquals( true, ( bool )$actual, $message ); // ==
        }
        protected function AssertTrue( $actual, $message = '' ) {
            return $this->InformTester(
                New AssertResult( is_bool( $actual ) && $actual == true, $message, $actual, $expected ) // is_boolean( $actual ) && $actual == $expected is faster (and for infinite recursion objects comparisons, it's infinitely faster)
            );
        }
        protected function AssertFalse( $actual, $message = '' ) {
            if ( !is_bool( $actual ) ) {
                return $this->InformTester(
                    false, $message, '[non boolean value]', false
                );
            }
            if ( $actual != false ) {
                return $this->InformTester(
                    New AssertResult( false, $message, true, false )
                );
            }
        }
        protected function RequireSuccess( AssertResult $result ) {
            if ( !$result->Success ) {
                $this->mTester->RequireFailed( $result );
            }
        }
        protected function InformTester( AssertResult $result ) {
            return $this->mTester->Inform( $result );
        }
        public function SetTester( Tester $tester ) {
            $this->mTester = $tester;
        }
    }
    
    function Test_GetTestcases() { // fetch a list of all testcases
        global $rabbit_settings;
        global $water;
        
        $ret = array();
        
        $queue = array( $rabbit_settings[ 'rootdir' ] . '/tests' );
        while ( !empty( $queue ) ) {
            $item = array_pop( $queue );
            $dh = opendir( $item );
            while ( false !== ( $df = readdir( $dh ) ) ) {
                switch ( $df ) {
                    case '.':
                    case '..':
                        break;
                    default:
                        if ( is_dir( $item . '/' . $df ) ){
                            array_push( $queue, $item . '/' . $df );
                        }
                        else if ( substr( $df, -strlen( '.php' ) ) == '.php' ) {
                            $testcase = require $item . '/' . $df;
                            if ( !( $testcase instanceof Testcase ) ) {
                                $water->Warning( "File $item/$df is not a valid Rabbit Testcase; skipping" );
                            }
                            else {
                                $appliesto = $testcase->AppliesTo();
                                $fileloadresult = Mask( $appliesto );
                                if ( isset( $fileloadresult[ 'error' ] ) ) {
                                    $water->Warning( "Rabbit Testcase $item/$df did not specify a valid 'mAppliesTo' path; skipping" );
                                }
                                else {
                                    $testcase->Name = substr( $item . '/' . $df, strlen( $rabbit_settings[ 'rootdir' ] . '/tests/' ), -strlen( '.php' ) );
                                    $ret[] = $testcase;
                                }
                            }
                        }
                }
            }
        }
        
        return $ret;
    }
    
    class Tester {
        protected $mTestResults;
        protected $mTestcases;
        protected $mAssertResults;
        protected $mRequirementsFullfilled;

        public function Tester() {
            $this->mTestcases = array();
        }
        public function AddTestcase( Testcase $testcase ) {
            $this->mTestcases[] = $testcase;
        }
        public function Run() {
            global $water;
            
            $water->Profile( 'Running ' . count( $this->mTestcases ) . ' testcases' );
            $this->mTestcaseResults = array();
            foreach ( $this->mTestcases as $i => $testcase ) {
                $water->Profile( 'Running testcase ' . $testcase->Name );
                $testcase->SetTester( $this ); // allows testcase to report results back to this tester
                $obj = New ReflectionObject( $testcase );
                $methods = $obj->getMethods();
                $runresults = array();
                try {
                    $testcase->SetUp();
                    $goodtogo = true;
                }
                catch ( Exception $e ) {
                    $runresults[] = New RunResultFailedByException( '[SetUp]', $e->getMessage() );
                    $goodtogo = false;
                }
                if ( $goodtogo ) {
                    foreach ( $methods as $method ) {
                        $methodname = $method->getName();
                        if ( $method->isPublic() && substr( $methodname, 0, strlen( 'Test' ) ) == 'Test' && $methodname != 'Testcase' ) {
                            $water->Profile( 'Running testrun ' . $methodname );
                            $this->mAssertResults = array();
                            try {
                                call_user_func( array( $testcase, $methodname ) ); // MAGIC
                                $runresults[] = New RunResult( $this->mAssertResults, $methodname );
                            }
                            catch ( Exception $e ) {
                                $runresults[] = New RunResultFailedByException( $methodname, $e->getMessage() );
                                $water->ProfileEnd();
                                break;
                            }
                            $water->ProfileEnd();
                        }
                    }
                }
                try {
                    $testcase->TearDown();
                }
                catch ( Exception $e ) {
                    $runresults[] = New RunResultFailedByException( '[TearDown]', $e->getMessage() );
                }
                $this->mTestResults[ $i ] = New TestcaseResult( $testcase, $runresults );
                $water->ProfileEnd();
            }
            $water->ProfileEnd();
        }
        public function GetResults() {
            return $this->mTestResults;
        }
        public function Inform( AssertResult $result ) {
            $this->mAssertResults[] = $result;
            return $result;
        }
        public function RequireFailed( AssertResult $result ) {
            throw New Exception( "Required assertion failed yielding to immediate TearDown: " . $result->Message );
        }
    }
    
    class TestcaseResult extends Overloadable implements Iterator { // a group of run results, the results for a complete testcase
        protected $mRunResults;
        protected $mTestcase;
        protected $mSuccess;
        protected $mNumRuns;
        protected $mNumSuccessfulRuns;
        protected $mNumAssertions;
        
        protected function GetTestcase() {
            return $this->mTestcase;
        }
        protected function GetResults() {
            return $this->mRunResults;
        }
        public function rewind() {
            return reset( $this->mRunResults );
        }
        public function current() {
            return current( $this->mRunResults );
        }
        public function key() {
            return key( $this->mRunResults );
        }
        public function next() {
            return next( $this->mRunResults );
        }
        public function valid() {
            return $this->current() !== false;
        }
        protected function GetNumRuns() {
            return $this->mNumRuns;
        }
        protected function GetNumSuccessfulRuns() {
            return $this->mNumSuccessfulRuns;
        }
        protected function GetNumAssertions() {
            return $this->mNumAssertions;
        }
        protected function GetSuccess() {
            return $this->mSuccess;
        }
        public function TestcaseResult( Testcase $testcase, array $runresults ) {
            w_assert( is_array( $runresults ) );
            $this->mNumRuns = count( $runresults );
            $this->mNumSuccessfulRuns = 0;
            $this->mNumAssertions = 0;
            $this->mSuccess = true;
            foreach ( $runresults as $runresult ) {
                w_assert( $runresult instanceof RunResult );
                if ( $runresult->Success ) {
                    ++$this->mNumSuccessfulRuns;
                }
                else {
                    $this->mSuccess = false;
                }
                $this->mNumAssertions += $runresult->NumAssertions;
            }
            $this->mTestcase = $testcase;
            $this->mRunResults = $runresults;
        }
    }
    
    class RunResult extends Overloadable implements Iterator { // a group of assertion results, a result of a test (function in the testcase class)
        protected $mAssertionResults;
        protected $mSuccess;
        protected $mRunName;
        protected $mNumSuccessfulAssertions;
        protected $mNumAssertions;
        
        public function rewind() {
            return reset( $this->mAssertionResults );
        }
        public function current() {
            return current( $this->mAssertionResults );
        }
        public function key() {
            return key( $this->mAssertionResults );
        }
        public function next() {
            return next( $this->mAssertionResults );
        }
        public function valid() {
            return $this->current() !== false;
        }
        protected function GetRunName() {
            return $this->mRunName;
        }
        protected function GetSuccess() {
            return $this->mSuccess;
        }
        protected function GetNumAssertions() {
            return $this->mNumAssertions;
        }
        protected function GetNumSuccessfulAssertions() {
            return $this->mNumSuccessfulAssertions;
        }
        public function RunResult( array $assertionresults, $runname ) {
            w_assert( is_string( $runname ) );
            w_assert( !empty( $runname ) );
            $this->mRunName = $runname;
            $this->mNumAssertions = count( $assertionresults );
            $this->mSuccess = true;
            $this->mNumSuccessfulAssertions = 0;
            foreach ( $assertionresults as $assertionresult ) {
                w_assert( $assertionresult instanceof AssertResult );
                if ( $assertionresult->Success ) {
                    ++$this->mNumSuccessfulAssertions;
                }
                else {
                    $this->mSuccess = false;
                }
            }
            $this->mAssertionResults = $assertionresults;
        }
    }
    
    class RunResultFailedByException extends RunResult {
        protected $mExceptionMessage;
        
        protected function GetMessage() {
            return $this->mExceptionMessage;
        }
        public function RunResultFailedByException( $runname, $exceptionmessage ) {
            $this->mRunName = $runname;
            $this->mExceptionMessage = $exceptionmessage;
            $this->mSuccess = false;
        }
    }

    class AssertResult extends Overloadable { // most basic test, a simple assertion
        protected $mSuccess;
        protected $mMessage;
        protected $mActual;
        protected $mExpected;
        
        protected function GetSuccess() {
            return $this->mSuccess;
        }
        protected function GetMessage() {
            return $this->mMessage;
        }
        protected function GetActual() {
            return $this->mActual;
        }
        protected function GetExpected() {
            return $this->mExpected;
        }
        public function AssertResult( $success, $message, $actual, $expected ) {
            $this->mSuccess  = $success;
            $this->mMessage  = $message;
            $this->mActual   = $actual;
            $this->mExpected = $expected;
        }
    }
?>
