<?php

namespace ITeam\Kashier\Validation;

/**
 * Class NumericValidator
 *
 * @package ITeam\Kashier\Validation
 */
class NumericValidator
{

    /**
     * Helper method for validating an argument if it is numeric
     *
     * @param mixed     $argument
     * @param string|null $argumentName
     * @return bool
     */
    public static function validate($argument, $argumentName = null)
    {
        if (!is_numeric($argument) && trim($argument) != null) {
            throw new \InvalidArgumentException("$argumentName is not a valid numeric value");
        }
        return true;
    }
}
