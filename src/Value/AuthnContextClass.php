<?php

namespace Surfnet\StepupBundle\Value;

use Surfnet\SamlBundle\Exception\InvalidArgumentException;

final class AuthnContextClass
{
    const TYPE_GATEWAY='gateway';
    const TYPE_SECOND_FACTOR_ONLY='second-factor-only';

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $type;

    /**
     * AuthnContextClassRef constructor.
     * @param string $identifier
     * @param string $type
     */
    public function __construct($identifier, $type)
    {
        $this->identifier = $identifier;

        if (!in_array($type, [self::TYPE_GATEWAY, self::TYPE_SECOND_FACTOR_ONLY])) {
            throw InvalidArgumentException::invalidType(
              'AuthnContextClassRef class constant',
              'type',
              $type
            );
        }

        $this->type = $type;
    }

    public static function getTypes()
    {
        return [
            static::TYPE_GATEWAY,
            static::TYPE_SECOND_FACTOR_ONLY,
        ];
    }

    /**
     * @param string $authnContextClassRef
     */
    public function isIdentifiedBy($authnContextClassRef)
    {
        return $this->identifier === $authnContextClassRef;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isOfType($type)
    {
        return $this->type === $type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return  $this->identifier;
    }
}
