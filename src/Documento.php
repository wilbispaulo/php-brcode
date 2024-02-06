<?php

namespace wilbispaulo\BRcode;

class Documento extends DocumentoBase
{
    public $obj;

    public function isValid()
    {
        return $this->obj->isValid();
    }

    public function format()
    {
        return $this->obj->format();
    }

    public function getType()
    {
        if ($this->isValid())
            return $this->obj->getClassName();
        return false;
    }

    public function getValue()
    {
        return $this->obj->getValue();
    }

    public function setValue(string $value)
    {
        $value = (string) preg_replace('/[^0-9]/', '', $value);

        if (strlen($value) === 11)
            $this->obj = new CPF($value);
        else
            $this->obj = new CNPJ($value);
    }
}
