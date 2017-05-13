<?php
// -----------------------------------------------------
/**
 *  @author Dumitru Uzun (DUzun.Me)
 *
 */
use duzun\SipHash; // PHP >= 5.3.0
// -----------------------------------------------------
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_PHPUnit_BaseClass.php';
// -----------------------------------------------------
class TestSipHash extends PHPUnit_BaseClass {
    // -----------------------------------------------------
    public static $className = 'duzun\\SipHash';
    public static $log       = true;

    // -----------------------------------------------------
    // -----------------------------------------------------

    public function testClass() {
        $this->assertTrue(class_exists(self::$className), 'Class ' . self::$className . ' not found');
        $this->assertMehodExists('hash_2_4');
    }

    public function test_hash_2_4() {
        $vectors = include __DIR__ . '/vectors.php';
        foreach($vectors as $i => $vector) {
          list($key, $message, $expected) = $vector;
          $found = SipHash::hash_2_4($key, $message, false);
          $this->assertEquals($expected, $found
                , 'Idx: [' . $i . '] Key: [' . $key . '] Message: [' . $message . ']' .
                  ' ' .
                  'Found: [' . $found . '] Expected: [' . $expected . ']'
          );

          self::log($found);
        }

        $key = hex2bin('efbeaddebebafeca0df0ad8b02b0ad1b');
        $message = 'Short test message';
        $found = SipHash::hash_2_4($key, $message);
        $expected = 'f2e893485bd3bade';

        $this->assertEquals($expected, $found);
    }

    // -----------------------------------------------------

}
?>
