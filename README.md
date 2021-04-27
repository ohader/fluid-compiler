# Fluid Compiler

using a Fork of `hoa/compiler` at `sanmai/hoa-compiler`

# Example

The following example simulates a parsing round-trip.
Markup is read from a file, parsed, and serialized again
to HTML. Besides removing (superfluous) whitespaces input
and output data are expected to be the same.

This example was taken from
[FactoryTest test case](tests/Fluid/Model/FactoryTest.php).

```php
<?php
require_once('vendor/autoload.php');

use FriendsOfTYPO3\FluidCompiler\Compiler\Compiler;
use FriendsOfTYPO3\FluidCompiler\Fluid;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Factory;

$sourceFile = 'fluid-template.html';
$expectation = file_get_contents($sourceFile);
$compiler = new Compiler(Fluid::VERSION_2x);
$syntaxTree = $compiler->parseFile($sourceFile);

$factory = new Factory();
$fluidTree = $factory->buildFromTree($syntaxTree);

$dumpingVisitor = new Fluid\Visitor\DumpingVisitor(
    new Fluid\Exporter\DumpingExporter()
);

$context = new Fluid\Context(Fluid::VERSION_2x);
$traverser = new Fluid\Traverser($fluidTree, $context);
$traverser->addVisitor($dumpingVisitor);
$html = $traverser->traverse();
```
