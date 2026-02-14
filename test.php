<?php

use JordJD\HCLParser\HCLParser;

require __DIR__.'/vendor/autoload.php';

$hcl = '
# Specify the provider and access details
provider "aws" {
  access_key = "${var.access_key}"
  secret_key = "${var.secret_key}"
  region     = "${var.region}"
}

# Create an instance
resource "aws_instance" "example" {
  ami           = "ami-2757f631"
  instance_type = "t2.micro"
}

';

$configObject = (new HCLParser($hcl))->parse();

var_dump($configObject);

/*

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

*/
