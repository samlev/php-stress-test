PHP Stress Testing
==================

This is a basic repo that holds some scripts to stress-test the PHP interpreter.

"Stress Testing" in this instance is mostly about doing something stupid to see how the interpreter reacts. It likely
isn't actually helpful for real-world usage, but will hopefully shed some light on some outer-edge limitations of the
interpreter.

## Tests

`test-length.php` tests the maximum length of a string
assigned to a variable.

`test-operations.php` tests the maximum number of operations
in a single file.

`test-size.php` tests for a maximum filesize that can be
read by the PHP interpreter.

`test-tokens.php` tests for the maximum number of tokens
that can be interpreted by the PHP interpreter.

It's also highly likely that the methods are breaking the
interpreter in unexpected ways that could mean that the
actual real-world limit is much higher.

## Process

Each script writes a file with an increasing amount of some
form of stressor, then executes the file and looks for an
error response. The file is written as a chunked stream to
avoid running into memory limits as it gets written, then
called with `shell_exec` so that it can be interpreted by
different versions of PHP cli.

## Interesting Results

Local testing has had some interesting results.

### String Length

In PHP <=7.2, the maximum length of a string appears to be
roughly the memory limit of PHP, i.e. each single character
takes one byte of memory. This result in itself isn't
interesting - the interesting part is that in php 7.3+, that
is not the case.

PHP 7.3 uses consistently two bytes of memory for each
single character in a string. What's more odd is that PHP
7.4+ uses _three_ bytes of memory for each character, but
_only_ if you assign the string to a variable. Without the
variable PHP uses 2 bytes of memory per character.

This could _potentially_ cause unexpected memory errors on
previously working code that deals with large amounts of
string manipulation (e.g. cron tasks processing text files)

### Operations

The surprising part of the operations test was how few
operations could be performed, and that the "out of memory"
error encountered came from the parser, not runtime. In fact
all version of PHP consistently failed to parse a file that
was less than 10KB.

It's likely that this would not be a consistent problem in
the real world, and may just be an error caused by the tree
of negations. Either way, every PHP interpreter I tried with
any memory limit could only handle 9994 consecutive
negations before the parser ran out of memory.

### Size

The most interesting part is that there appears to be no
size limit for a PHP file. I tested with both whitespace and
a block comment, and had to manually stop the scripts once
the output files hit around 1GB. I suspect that this means
that the PHP interpreter is using file streams to load files
into the parser, and is simply discarding the whitespace or
block comments as it goes, because they're not important for
execution.

### Tokens

I'm the least sure of the methodology on this one. It
appears to fail as a runtime out of memory error, despite
the fact that there is only one variable declared, and no
value is assigned to it. In theory the memory use shouldn't
go up as the variable isn't actually ever _set_, but the
failure (when it occurs) is not in the parser, like it was
for operations.

Executing the file takes a lot longer than any other test
so this may point to an issue with the garbage collector, or
it may just mean that you shouldn't attempt to declare the
same empty variable 100000 times in a row.