<?php

namespace wilbispaulo\BRcode;

require_once "helpers.php";

define("CHAVE", "2601");
define("VALOR", "54");
define("NOME", "59");
define("CIDADE", "60");
define("TXID", "6205");
define("CRC16", "63");
define("POLINOMIO", 0x1021);
define("INICIAL", 0xffff);
define("TIPO", "ascii");

/**
 * Cria uma string 'PIX copia e cola' no padrão BR CODE v2.0.1
 */

class BRcode
{
    
    protected $arranjo = array();

    public function __construct()
    {
        $this->arranjo = array(
            ['00', '01'],                   //Payload Format Indicator
            ['2600', 'BR.GOV.BCB.PIX'],     //Merchant Account Information-GUI
            ['2601', '1234567'],            //Merchant Account Information-Chave
            ['52', '0000'],                 //Merchant Category Code
            ['53', '986'],                  //Transaction Currency (R$)
            ['54', ''],                     //Amount (R$)
            ['58', 'BR'],                   //Country Code
            ['59', 'FULANO DE TAL'],        //Merchant Name
            ['60', 'BRASILIA'],             //Merchant City
            ['6205', '***'],                //Additional Data Field Template-TXid
            ['63', '1D3D']                  //CRC16 Pol 0x1021 CI 0xFFFF ordem modifica CRC
        );
    }

    public function setChave(string $chave)
    {
        $id = array_search(CHAVE, array_column($this->arranjo, 0));
        $this->arranjo[$id][1] = $chave;
    }

    public function setNome(string $nome)
    {
        $id = array_search(NOME, array_column($this->arranjo, 0));
        $nome = strtoupper(limpa($nome));
        $this->arranjo[$id][1] = $nome;
    }

    public function setCidade(string $cidade)
    {
        $id = array_search(CIDADE, array_column($this->arranjo, 0));
        $cidade = strtoupper(limpa($cidade));
        $this->arranjo[$id][1] = $cidade;
    }

    public function setTxId(string $txid)
    {
        $id = array_search(TXID, array_column($this->arranjo, 0));
        $this->arranjo[$id][1] = $txid;
    }

    public function setValor(string $valor)
    {
        $id = array_search(VALOR, array_column($this->arranjo, 0));
        $this->arranjo[$id][1] = $valor;
    }

    public function gerar(): string
    {
        $brcode = "";
        $subBrCode = "";
        $idAnt = "";
        $tamSubBrCode = "";
        $colId = array_column($this->arranjo, 0);

        foreach($colId as $key => $nomeId)
        {
            $tam = strlen($this->arranjo[$key][1]);
            if ($tam == 0) {
                continue;
            }
            $tam = sprintf("%02d", $tam);
            $id = substr($nomeId, 0, 2);
            if ($id != $idAnt) {
                $brcode .= ($idAnt . $tamSubBrCode . $subBrCode);
                $subBrCode = "";
                $idAnt = "";
                $tamSubBrCode = "";
            }
            if ($id == "63") {
                $brcode .= ($id . "04");
                $crc16 = crc16($brcode, POLINOMIO, INICIAL, TIPO);
                $brcode .= $crc16;
            } else {
                if (strlen($nomeId) == 2) {
                    $brcode .= ($id . $tam . $this->arranjo[$key][1]);
                } else {
                    $idd = substr($nomeId, 2, 2);
                    $subBrCode .= ($idd . $tam . $this->arranjo[$key][1]);
                    $tamSubBrCode = sprintf("%02d", strlen($subBrCode));
                    $idAnt = $id;
                }
            }
        }
        return $brcode;
    }
}
