<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Model;

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

class Attribute implements Parsable, Matchable, Descending
{
    private $name;
    private $value;

    public function __construct(Token $name, Assignable $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function matches($criteria, $scope = null): bool
    {
        return (string)$criteria === (string)($this->{$scope} ?? $this->value);
    }

    public function getDescendants(): array
    {
        return [
            $this->name,
            $this->value
        ];
    }

    /**
     * @return \FriendsOfTYPO3\FluidCompiler\Fluid\Model\Token
     */
    public function getName(): \FriendsOfTYPO3\FluidCompiler\Fluid\Model\Token
    {
        return $this->name;
    }

    /**
     * @return \FriendsOfTYPO3\FluidCompiler\Fluid\Model\Assignable|null
     */
    public function getValue(): ?\FriendsOfTYPO3\FluidCompiler\Fluid\Model\Assignable
    {
        return $this->value;
    }
}
