<?php
// -----------------------------------------------------
/**
 *  @author Dumitru Uzun (DUzun.Me)
 *
 */
// -----------------------------------------------------
define('PHPUNIT_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('ROOT_DIR', strtr(dirname(PHPUNIT_DIR), '\\', '/').'/');
define('PHP_IS_NEW', version_compare(PHP_VERSION, '5.3.0') >= 0);
// -----------------------------------------------------
if ( !class_exists('PHPUnit_Framework_TestCase') ) {
    class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}

// -----------------------------------------------------
if ( file_exists(ROOT_DIR . 'vendor/autoload.php') ) {
    require_once ROOT_DIR . 'vendor/autoload.php';
}
else {
    require_once ROOT_DIR . 'src/siphash.php';
}
// -----------------------------------------------------
// -----------------------------------------------------
/**
 * @backupGlobals disabled
 */
// -----------------------------------------------------
abstract class PHPUnit_BaseClass extends PHPUnit_Framework_TestCase {
    public static $log = true;
    public static $testName;
    public static $className;
    // -----------------------------------------------------
    // Before any test
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
    }
    // After all tests
    public static function tearDownAfterClass() {
        parent::tearDownAfterClass();
    }

    // -----------------------------------------------------
    // Before every test
    public function setUp() {
        self::$testName = $this->getName();
        isset(static::$className) or static::$className = get_class($this);

        parent::setUp();
    }
    // -----------------------------------------------------
    /**
     * Asserts that a method exists.
     *
     * @param  string $methodName
     * @param  string|object  $className
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertMehodExists($methodName, $className = NULL, $message = NULL) {
        isset($className) or $className = static::$className;
        isset($message) or $message = 'Method '.$className.'::'.$methodName.'() not found';
        self::assertThat(method_exists($className, $methodName), self::isTrue(), $message);
    }

    // Alias to $this->assertMehodExists()
    public function assertClassHasMethod() {
        $args = func_get_args();
        return call_user_func_array(array($this, 'assertMehodExists'), $args);
    }

    // -----------------------------------------------------
    // Helper methods:

    public static function log() {
        if ( empty(self::$log) ) return;
        static $idx = 0;
        static $lastTest;
        static $lastClass;
        if ( $lastTest != self::$testName || $lastClass != self::$className ) {
            echo PHP_EOL, PHP_EOL, '-> ', self::$className.'::'.self::$testName, ' ()';
            $lastTest  = self::$testName;
            $lastClass = self::$className;
        }
        $args = func_get_args();
        foreach($args as $k => $v) is_string($v) or is_int($v) or is_float($v) or $args[$k] = var_export($v, true);
        echo PHP_EOL
            , ""
            , str_pad(++$idx, 3, ' ', STR_PAD_LEFT)
            , ")\t"
            , implode(' ', $args)
        ;
    }
    // -----------------------------------------------------
    public static function deleteTestData() {
    }
    // -----------------------------------------------------
}
// -----------------------------------------------------
// Delete the temp test user after all tests have fired
register_shutdown_function('PHPUnit_BaseClass::deleteTestData');
// -----------------------------------------------------
?>
