<?php

namespace wilbispaulo\BRcode;

class BRcode
{
    protected $data = [
        '00' => '01',
        '26' => ['00' => 'BR.GOV.BCB.PIX'],
        '52' => '0000',
        '53' => '986',
        '58' => 'BR',
        '63' => ''
    ];

    public function __construct(string $chave, string $nome, string $cidade, string $txId = "***", string $valor = "")
    {
        $chave = self::validaChave($chave);
        if ($chave === false) {
            unset($this->data);
            return $this->data = false;
        }
        $nome = self::validaNome($nome);
        if ($nome === false) {
            unset($this->data);
            return $this->data = false;
        }
        $cidade = self::validaCidade($cidade);
        if ($cidade === false) {
            unset($this->data);
            return $this->data = false;
        }
        $txId = self::validaTxId($txId);
        if ($txId === false) {
            unset($this->data);
            return $this->data = false;
        }
        $valor = self::validaValor($valor);
        if ($valor === false) {
            unset($this->data);
            return $this->data = false;
        }
        $this->data["26"][CHAVE] = $chave;
        $this->data[NOME] = $nome;
        $this->data[CIDADE] = $cidade;
        $this->data["62"][TXID] = $txId;
        $this->data[VALOR] = $valor;
    }

    public static function validaChave(string $chave): string | bool
    {
        if (strlen($chave) > MAXL_CHAVE) {
            return false;
        }
        if (
            !preg_match("/^([a-z0-9]+\.?)+[a-z0-9]@[a-z0-9]+\.[a-z]+(\.[a-z]+)?$/", $chave) &&
            !preg_match("/^(\+55)[0-9]{11}$/", $chave) &&
            !preg_match("/^([0-9a-f]{8})-([0-9a-f]{4}-){3}([0-9a-f]{12})$/", $chave)
        ) {;
            if (!$chave = (new Documento($chave))->isValid())
                return false;
        }
        return $chave;
    }

    public static function validaNome(string $nome)
    {
        $nome = strtoupper(limpa($nome));
        $nome = substr($nome, 0, 25);

        if (preg_match("/^[A-Z][A-Z ]+$/", $nome)) {
            return $nome;
        }
        return false;
    }

    public static function validaCidade(string $cidade)
    {
        $cidade = strtoupper(limpa($cidade));
        $cidade = substr($cidade, 0, 15);

        if (preg_match("/^[A-Z][A-Z ]+$/", $cidade)) {
            return $cidade;
        }
        return false;
    }

    public static function validaTxId(string $txId)
    {
        $txId = limpa($txId);
        $txId = substr($txId, 0, 25);
        if ($txId === '***') {
            return $txId;
        }
        if ($txId = preg_replace("/[^a-zA-Z0-9]+/", '', $txId)) {
            return $txId;
        }
        return false;
    }

    public static function validaValor(string $valor)
    {
        if ($valor == '') {
            return $valor;
        }
        $valor = strip_tags(trim($valor));
        if (preg_match("/^([0-9]{1,3})(\.[0-9]{3})*(,[0-9]{2})$/", $valor)) {
            $tr = ["." => "", "," => "."];
            $valor = strtr($valor, $tr);
        }
        if (preg_match("/^([0-9]{1,10})(\.[0-9]{2})$/", $valor)) {
            return $valor;
        }
        if (preg_match("/^([0-9]{3,12})$/", $valor)) {
            return substr_replace($valor, ".", -2, 0);
        }
        return false;
    }

    public static function removeEscEsp(string $string): string
    {
        return removeEscEsp($string);
    }

    public function setChave(string $chave)
    {
        $this->data[CHAVE] = $chave;
    }

    public function setNome(string $nome)
    {
        $nome = strtoupper(limpa($nome));
        $this->data[NOME] = $nome;
    }

    public function setCidade(string $cidade)
    {
        $cidade = strtoupper(limpa($cidade));
        $this->data[CIDADE] = $cidade;
    }

    public function setTxId(string $txid)
    {
        $this->data[TXID] = $txid;
    }

    public function setValor(string $valor)
    {
        $this->data[VALOR] = $valor;
    }

    public function geraPixCode(): string | bool
    {
        if (!$this->data) {
            return false;
        }

        $codePix = "";
        $subCodePix = "";
        $keys = ['00', '26', '52', '53', '54', '58', '59', '60', '62', '63'];
        foreach ($keys as $key) {
            if (!is_array($this->data[$key])) {
                if ($key === '63') {
                    $codePix .= '6304';
                    $crc16 = CRC16($codePix, POLINOMIO, INICIAL, TIPO);
                    $codePix .= $crc16;
                } elseif ($this->data[$key] == '') {
                    continue;
                } else {
                    $codePix .= $key;
                    $codePix .= sprintf("%02d", strlen($this->data[$key]));
                    $codePix .= $this->data[$key];
                }
            }
            if (is_array($this->data[$key])) {
                $id1 = array_keys($this->data[$key]);
                foreach ($id1 as $id) {
                    $subCodePix .= $id;
                    $subCodePix .= sprintf("%02d", strlen($this->data[$key][$id]));
                    $subCodePix .= $this->data[$key][$id];
                }
                $codePix .= $key . strlen($subCodePix) . $subCodePix;
                $subCodePix = "";
            }
        }
        return $codePix;
    }
}
