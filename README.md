# ⚒🔀🐘 PHP HCL Parser

[![Tests](https://github.com/Jord-JD/php-hcl-parser/actions/workflows/tests.yml/badge.svg)](https://github.com/Jord-JD/php-hcl-parser/actions/workflows/tests.yml)

HCL is a configuration language make by HashiCorp. HCL files are used by several HashiCorp products,
including Terraform.

This library parses HCL configuration files into PHP objects.

> **Compatibility note:** The bundled `json2hcl` 0.0.6 converter supports legacy HCL syntax. Its upstream project is archived and does not provide ARM64 binaries, so this package is not suitable for modern HCL 2 syntax or ARM64 hosts.

## Installation

You can install the PHP HCL Parser library using Composer. Just run the following command
from the root of your project.

```
composer require jord-jd/php-hcl-parser
```

## Usage

To parse HCL into a PHP object, create a new `HCLParser` object, passing it the HCL (as a string), then call the `parse` method. See the example below.

```php
$hcl = file_get_contents('example.tf');
$configObject = (new HCLParser($hcl))->parse();
```

Invalid HCL, converter failures, and invalid converter output throw `JordJD\HCLParser\Exceptions\HCLParseException`. Downloaded converter binaries are checked against pinned SHA-256 hashes before they are installed.

The resulting object will look similar to the following.

```php
object(stdClass)#5 (2) {
  ["provider"]=>
  array(1) {
    [0]=>
    object(stdClass)#4 (1) {
      ["aws"]=>
      array(1) {
        [0]=>
        object(stdClass)#2 (3) {
          ["access_key"]=>
          string(17) "${var.access_key}"
          ["region"]=>
          string(13) "${var.region}"
          ["secret_key"]=>
          string(17) "${var.secret_key}"
        }
      }
    }
  }
  ["resource"]=>
  array(1) {
    [0]=>
    object(stdClass)#8 (1) {
      ["aws_instance"]=>
      array(1) {
        [0]=>
        object(stdClass)#7 (1) {
          ["example"]=>
          array(1) {
            [0]=>
            object(stdClass)#6 (2) {
              ["ami"]=>
              string(12) "ami-2757f631"
              ["instance_type"]=>
              string(8) "t2.micro"
            }
          }
        }
      }
    }
  }
}
```
