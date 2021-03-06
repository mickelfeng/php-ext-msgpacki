--TEST--
Check for serialization handler
--FILE--
<?php
if(!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$output = '';

function open($path, $name) {
    return true;
}

function close() {
    return true;
}

function read($id) {
    global $output;
    $output .= "read" . PHP_EOL;
    return pack('H*', '81a3666f6f01');
}

function write($id, $data) {
    global $output;
    $output .= "wrote: ";
    $output .= bin2hex($data). PHP_EOL;
    return true;
}

function destroy($id) {
    return true;
}

function gc($time) {
    return true;
}

class Foo {
}

class Bar {
}

ini_set('session.serialize_handler', 'msgpacki');

session_set_save_handler('open', 'close', 'read', 'write', 'destroy', 'gc');


$db_object = new Foo();
$session_object = new Bar();

$v = session_start();
var_dump($v);
$_SESSION['test'] = "foobar";

session_write_close();

echo $output;
var_dump($_SESSION);
?>
--EXPECT--
bool(true)
read
wrote: 82a3666f6f01a474657374a6666f6f626172
array(2) {
  ["foo"]=>
  int(1)
  ["test"]=>
  string(6) "foobar"
}
