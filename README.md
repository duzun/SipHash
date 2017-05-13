# SipHash
SipHash-2-4 implementation in PHP

# Usage

```php
use duzun\SipHash; // PHP >= 5.3.0

// 128-bit $key is a string of max 16 chars
// $message is any string you want to hash
$hash = SipHash::hash_2_4($key, $message); // eg. "6dd48df68066d1bd"


```
