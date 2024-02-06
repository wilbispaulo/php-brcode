<?php

namespace wilbispaulo\BRcode;

abstract class DocumentoBase
{
    protected string $value = '';

    public function __construct(?string $value = null)
    {
        if ($value) $this->setValue($value);
    }

    public function getClassName()
    {
        return substr(strrchr(get_class($this), '\\'), 1);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue(string $value)
    {
        $this->value = $value;
        return $this;
    }

    abstract public function isValid();
    abstract public function format();
}
