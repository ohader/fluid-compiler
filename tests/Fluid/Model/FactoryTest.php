<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Tests\Fluid\Model;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use FriendsOfTYPO3\FluidCompiler\Compiler\Compiler;
use FriendsOfTYPO3\FluidCompiler\Fluid;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function dumpMatchesSyntaxTreeDataProvider(): array
    {
        return [
            '#001' => ['this is just text'],
            '#002' => ['  this  is  just text  with  spaces  '],
            '#003' => ['this is just <b>text</b> and some <img src="whatever.png" /> image'],
            '#004' => ['<div data-a data-b="c"> Hello World </div>'],

            '#051' => ['{namespace abc*}'],
            '#052' => ['{namespace abc=Vendor\Abc\ViewHelpers}'],
            '#053' => ['{escaping=true}', '{escaping true}'],
            '#054' => ['{escaping=on}', '{escaping true}'],
            '#055' => ['{escaping on}', '{escaping true}'],

            '#101' => ['{hello.world}'],
            '#102' => ['{{hello.world.reference}}'],
            '#103' => ['{if ? then : else}'],
            '#104' => ['{if ?: else}'],

            '#121' => ['{cha.cha -> cha:chain.ed()}'],
            '#122' => ['{cha.cha -> cha:chain.ed(my:mind)}'],
            '#123' => ["{cha.cha -> cha:chain.ed(my:'{mind}')}"],

            '#151' => ["{f:variable(name:'first', value:'{variable}')}"],
            '#152' => ["{f:variable(name:'first', value:'{0:variable}')}"],
            '#153' => ["{f:variable(name:'first', value:'{0:\'text\'}')}"],

            '#201' => ['<f:abc a b="2">'],
            '#202' => ['<f:abc a b="2" />'],
            '#203' => ['<f:abc a b="2">ABC</f:abc>'],
            '#211' => ['<f:abc a b="2" condition="{variable}" then="{variable ?: else}">'],
            '#212' => ['<f:abc a="{b:c}" />'],
            '#213' => ["<f:abc a=\"{b:'d'}\" />"],
            '#291' => ['<f:i.am></f:just></f:a.parser>'],

            '#301' => ['<html xmlns:f="TYPO3/CMS/Fluid/ViewHelpers" data-namespace-typo3-fluid="true">'],
            '#302' => ['<html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers" data-namespace-typo3-fluid="true">'],
        ];
    }

    /**
     * @param string $data
     * @param string|null $expectation
     *
     * @test
     * @dataProvider dumpMatchesSyntaxTreeDataProvider
     */
    public function dumpMatchesSyntaxTree(string $data, string $expectation = null): void
    {
        $compiler = new Compiler(Fluid::VERSION_2x);
        $syntaxTree = $compiler->parseSource($data);

        $factory = new Factory();
        $fluidTree = $factory->buildFromTree($syntaxTree);

        self::assertSame($expectation ?? $data, $fluidTree->dump());
    }
}
