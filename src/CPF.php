<?php

namespace wilbispaulo\BRcode;

class CPF extends DocumentoBase
{
    protected $blacklist = [
        '00000000000',
        '11111111111',
        '22222222222',
        '33333333333',
        '44444444444',
        '55555555555',
        '66666666666',
        '77777777777',
        '88888888888',
        '99999999999'
    ];

    public function isValid()
    {
        // Check the size
        if (strlen($this->value) != 11)
            return false;

        // Check if it is blacklisted
        if (in_array($this->value, $this->blacklist))
            return false;

        // Validate first check digit
        for ($i = 0, $j = 10, $sum = 0; $i < 9; $i++, $j--)
            $sum += $this->value[$i] * $j;

        $result = $sum % 11;

        if ($this->value[9] != ($result < 2 ? 0 : 11 - $result))
            return false;

        // Validate first second digit
        for ($i = 0, $j = 11, $sum = 0; $i < 10; $i++, $j--)
            $sum += $this->value[$i] * $j;

        $result = $sum % 11;

        return ($this->value[10] == ($result < 2 ? 0 : 11 - $result)) ? $this->value : false;
    }

    public function format()
    {
        if (!$this->isValid())
            return false;

        // Format ###.###.###-##
        $result  = substr($this->value, 0, 3) . '.';
        $result .= substr($this->value, 3, 3) . '.';
        $result .= substr($this->value, 6, 3) . '-';
        $result .= substr($this->value, 9, 2) . '';

        return $result;
    }
}
