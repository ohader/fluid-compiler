<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Compiler;

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

use FriendsOfTYPO3\FluidCompiler\Fluid;
use Hoa\Compiler\Llk\Llk;
use Hoa\Compiler\Llk\TreeNode;
use Hoa\File\Read;

class Compiler
{
    private $grammarPath;

    public function __construct(string $version = Fluid::VERSION_2x)
    {
        $grammarPath = sprintf(
            '%s/grammar/fluid-%s.pp',
            dirname(dirname(__DIR__)),
            $version
        );
        if (!file_exists($grammarPath)) {
            throw new \LogicException(
                sprintf('Fluid grammar for version %s not available', $version),
                1601881923
            );
        }
        $this->grammarPath = $grammarPath;
    }

    public function parseFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \LogicException(
                sprintf('File not found at %s', $filePath),
                1601881924
            );
        }
        return $this->parseSource(
            file_get_contents($filePath)
        );
    }

    public function parseSource(string $source): TreeNode
    {
        $compiler = Llk::load(new Read($this->grammarPath));
        return $compiler->parse($source, 'root', true);
    }

}
