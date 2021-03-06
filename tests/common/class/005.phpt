--TEST--
MessagePacki::pack() mangles objects with __sleep
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class test {
    public $a, $b;

    function __construct() {
        $this->a = 7;
        $this->b = 2;
    }

    function __sleep() {
        $this->b = 0;
    }
}

$t['one'] = 'ABC';
$t['two'] = new test();

var_dump($t);

$m = new MessagePacki();

$s =  @$m->pack($t);
echo bin2hex($s) . "\n";

var_dump($m->unpack($s));
?>
--EXPECT--
array(2) {
  ["one"]=>
  string(3) "ABC"
  ["two"]=>
  object(test)#1 (2) {
    ["a"]=>
    int(7)
    ["b"]=>
    int(2)
  }
}
82a36f6e65a3414243a374776fc0
array(2) {
  ["one"]=>
  string(3) "ABC"
  ["two"]=>
  NULL
}
