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

class Token implements Assignable, Matchable, Identifiable, StringRepresentable
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $namespace;


    public function __construct(string $value, int $offset, string $token, string $namespace)
    {
        $this->value = $value;
        $this->offset = $offset;
        $this->token = $token;
        $this->namespace = $namespace;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function matches($criteria, $scope = null): bool
    {
        return (string)$criteria === (string)($this->{$scope} ?? $this->value);
    }

    public function identify(): string
    {
        return sprintf('$%s:%s=%s', $this->namespace, $this->token, $this->value);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
