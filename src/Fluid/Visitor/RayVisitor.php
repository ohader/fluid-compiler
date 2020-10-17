<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Visitor;

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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\ParsableTrait;

class RayVisitor implements Visitable
{
    use ParsableTrait;

    private const IGNORE = [
        Model\Token::class,
        Model\Tree::class,
    ];
    private const SELF_TERMINATING = [
        Model\Node\SelfClosing::class,
        Model\ViewHelper\SelfClosing::class,
        Model\Attribute::class,
        Model\Inline\Wrapped::class,
        Model\Inline\Assignable\Quoted::class,
        Model\Inline\Assignable\Variable::class,
        Model\Inline\Assignable\Numeric::class,
    ];

    /**
     * @var RayItem
     */
    private $ray;

    public function __construct()
    {
        $this->ray = new RayItem();
    }

    /**
     * @return RayItem
     */
    public function get(): RayItem
    {
        return $this->ray;
    }

    public function enter(Parsable $parsable, ?string $index): ?Parsable
    {
        if (!$this->isA($parsable, self::IGNORE)) {
            $this->ray = $this->ray->append($parsable);
        }
        return null;
    }

    public function leave(Parsable $parsable, ?string $index): ?Parsable
    {
        if ($this->isA($parsable, self::IGNORE)) {
            return null;
        }
        if ($parsable instanceof Model\Closable) {
            $this->ray = $this->ray->close($parsable);
        } elseif ($this->isA($parsable, self::SELF_TERMINATING)){
            $this->ray = $this->ray->pruneBefore($parsable);
        }
        return null;
    }

    private function isA(Parsable $parsable, array $classNames): bool
    {
        foreach ($classNames as $className) {
            if (is_a($parsable, $className)) {
                return true;
            }
        }
        return false;
    }
}
