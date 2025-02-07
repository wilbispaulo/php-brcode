<?php

/**
 * Formata uma string substituindo %20 por " "
 * 
 * @param string $string
 * @return string
 */
function removeEscEsp(string $string): string
{
    return strtr($string, ['%20' => ' ']);
}

/**
 * Formata uma string removendo acentos e caracteres especiais
 * 
 * @param string $string
 * @return string
 */
function limpa(string $string): string
{
    $table = array(
        'Š' => 'S',
        'š' => 's',
        'Đ' => 'Dj',
        'đ' => 'dj',
        'Ž' => 'Z',
        'ž' => 'z',
        'Č' => 'C',
        'č' => 'c',
        'Ć' => 'C',
        'ć' => 'c',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'A',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ý' => 'Y',
        'Þ' => 'B',
        'ß' => 'Ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'a',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'o',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ý' => 'y',
        'ý' => 'y',
        'þ' => 'b',
        'ÿ' => 'y',
        'Ŕ' => 'R',
        'ŕ' => 'r',
        '\\' => '',
        '%20' => ' ',
        '%C3%81' => 'A',
        '%C3%89' => 'E',
        '%C3%8D' => 'I',
        '%C3%93' => 'O',
        '%C3%9A' => 'U',
        '%C3%A1' => 'a',
        '%C3%A9' => 'e',
        '%C3%AD' => 'i',
        '%C3%B3' => 'o',
        '%C3%BA' => 'u',
        '%C3%83' => 'A',
        '%C3%A3' => 'a',
        '%C3%87' => 'C',
        '%C3%A7' => 'c'
    );
    $slug_table = strtr($string, $table);
    $slug_trim = trim($slug_table);
    $slug_tag = strip_tags($slug_trim);
    $slug_space = preg_replace('/[@%\[\]{}()\'\"`´^~|<>,.:;?+\/-]/', '', $slug_tag);
    // $slug = strtolower($slug_space);
    return $slug_space;
}

/**
 * Gera um CRC16
 * 
 * @param $str
 * @param $polynomial
 * @param $initValue
 * @param $strType = "hex"
 * @param $xOrValue = 0
 * @param $inputReverse = false
 * @param $outputRecerse = false
 * @return $string
 */
function crc16($str, $polynomial, $initValue, $strType = "hex", $xOrValue = 0, $inputReverse = false, $outputReverse = false)
{
    $crc = $initValue;
    if ($strType == "hex") {
        $str = pack('H*', $str);
    }
    for ($i = 0; $i < strlen($str); $i++) {
        if ($inputReverse) {
            // Cada byte de dados de entrada é revertido bit a bit
            $c = reverseChar($str[$i]);
            $c = ord((string)$c);
        } else {
            $c = ord($str[$i]);
        }
        $crc ^= ($c << 8);
        for ($j = 0; $j < 8; ++$j) {
            if ($crc & 0x8000) {
                $crc = (($crc << 1) & 0xffff) ^ $polynomial;
            } else {
                $crc = ($crc << 1) & 0xffff;
            }
        }
    }
    if ($outputReverse) {
        // Armazene o endereço baixo no bit baixo, ou seja, use o método little endian para converter o inteiro em uma string
        $ret = pack('cc', $crc & 0xff, ($crc >> 8) & 0xff);
        // O resultado de saída é uma string inteira invertida em bits
        $ret = reverseString($ret);
        // Em seguida, converta o resultado de volta para um número inteiro de acordo com o método little endian
        $arr = unpack('vshort', $ret);
        $crc = $arr['short'];
    }
    return str_pad(strtoupper(base_convert($crc ^ $xOrValue, 10, 16)), 4, '0', STR_PAD_LEFT);
}

/**
 * bit-reverse um fluxo de bytes eg: 'AB'(01000001 01000010)  --> '\x42\x82'(01000010 10000010)
 * @param $str
 */
function reverseString($str)
{
    $m = 0;
    $n = strlen($str) - 1;
    while ($m <= $n) {
        if ($m == $n) {
            $str[$m] = reverseChar($str[$m]);
            break;
        }
        $ord1 = reverseChar($str[$m]);
        $ord2 = reverseChar($str[$n]);
        $str[$m] = $ord2;
        $str[$n] = $ord1;
        $m++;
        $n--;
    }
    return $str;
}

/**
 * Inverte um caractere pouco a pouco eg: 65 (01000001) --> 130(10000010)
 * @param $char
 * @return $string
 */
function reverseChar($char): string
{
    $byte = ord($char);
    $tmp = 0;
    for ($i = 0; $i < 8; ++$i) {
        if ($byte & (1 << $i)) {
            $tmp |= (1 << (7 - $i));
        }
    }
    return chr($tmp);
}
