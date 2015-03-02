<?php

namespace Tavs\DataTable\Query;

use Doctrine\Common\Lexer\AbstractLexer;

/**
 * Class Lexer
 *
 * The literal query builder is able to recognize the below list of expressions:
 *
 * Text Field
 *      Field1:"Tales"
 *      Field1:"Tales","Santos"
 *      Field1:"Tales",~"Augusto","Santos"
 *      Field1:"Ta"..    => begins("Ta")
 *      Field1:.."es"    => ends("es")
 *
 * Numerical Field
 *      Field2:10
 *      Field2:10,20
 *      Field2:10,~20,30
 *      Field2:10..      => gte(10)
 *      Field2:..20      => lte(10)
 *      Field2:10..20    => btw(10 AND 20)
 *
 * Date Field
 *      Field3:"28/11/1984"
 *      Field3:"28/11/1984","02/10/1987"
 *      Field3:"28/11/1984",~"05/08/1983","02/10/1987"
 *      Field3:"01/01/2000"..                   => gte("01/01/2000")
 *      Field3:.."01/01/2000"                   => lte("01/01/2000")
 *      Field3:"01/01/2000".."01/01/2001"       => btw("01/01/2000")
 *      Field3:Yesterday,Today,Tomorrow
 *      Field3:LastWeek,ThisWeek,NextWeek
 *      Field3:LastMonth,ThisMonth,NextMonth
 *      Field3:LastYear,ThisYear,NextYear
 *
 * Boolean Field
 *      Field4:True
 *      Field4:False
 *
 * Empty(Null) Field
 *      Field5:Empty
 *      Field5:~Empty
 *
 * @package Tavs\DataTable
 */
class Lexer extends AbstractLexer
{
    const T_NONE = 1;
    const T_CLOSE_CURLY_BRACES = 2;
    const T_COLON = 3;
    const T_COMMA = 4;
    const T_DATE = 5;
    const T_DOT = 6;
    const T_FLOAT = 7;
    const T_INTEGER = 8;
    const T_OPEN_CURLY_BRACES = 9;
    const T_RANGE = 10;
    const T_STRING = 11;
    const T_TILDE = 12;

    // All tokens that are also identifiers should be >= 100
    const T_IDENTIFIER = 100;
    const T_EMPTY = 101;
    const T_FALSE = 102;
    const T_NULL = 103;
    const T_TRUE = 104;

    protected $noCase = [
        ':'  => self::T_COLON,
        ','  => self::T_COMMA,
        '.'  => self::T_DOT,
        '..' => self::T_RANGE,
        '~'  => self::T_TILDE,
        '{'  => self::T_OPEN_CURLY_BRACES,
        '}'  => self::T_CLOSE_CURLY_BRACES,
    ];

    protected $valueTypes = [
        self::T_DATE,
        self::T_EMPTY,
        self::T_FALSE,
        self::T_FLOAT,
        self::T_INTEGER,
        self::T_STRING,
        self::T_TRUE
    ];

    /**
     * @return array
     */
    public function getValueTypes()
    {
        return $this->valueTypes;
    }

    /**
     * Lexical catchable patterns.
     *
     * @return array
     */
    protected function getCatchablePatterns()
    {
        return [
            '[a-z_][a-z0-9_\.]+',
            '\d{1,2}?\/\d{1,2}?\/\d{4}',
            '(?:[+-]?[0-9]+(?:[\.][0-9]+)*)(?:[eE][+-]?[0-9]+)?',
            '"(?:""|[^"])*+"',
            '\.{2}'
        ];
//        return [
//            '[a-z_\\\][a-z0-9_\:\\\]*[a-z_][a-z0-9_]*',
//            '(?:[+-]?[0-9]+(?:[\.][0-9]+)*)(?:[eE][+-]?[0-9]+)?',
//            '"(?:""|[^"])*+"',
//        ];
    }

    /**
     * Lexical non-catchable patterns.
     *
     * @return array
     */
    protected function getNonCatchablePatterns()
    {
        return ['\s+', '\*+', '(.)'];
    }

    /**
     * Retrieve token type. Also processes the token value if necessary.
     *
     * @param string $value
     *
     * @return integer
     */
    protected function getType(&$value)
    {
        $type = self::T_NONE;

        switch (true) {
            // Recognize numeric values
            case (is_numeric($value)):
                if (strpos($value, '.') !== false || stripos($value, 'e') !== false) {
                    $value = (float)$value;

                    return self::T_FLOAT;
                }

                $value = (int)$value;

                return self::T_INTEGER;

            // Recognize quoted strings
            case ($value[0] === '"'):
                $value = str_replace('""', '"', substr($value, 1, strlen($value) - 2));

                return self::T_STRING;

            // Recognize true values
            case ('true' === strtolower($value)):
                $value = 1;

                return self::T_TRUE;

            // Reconize false values
            case ('false' === strtolower($value)):
                $value = 0;

                return self::T_FALSE;

            // Recognize identifiers
            case (ctype_alpha($value[0]) || $value[0] === '_'):
                $name = 'Tavs\DataTable\Query\Lexer::T_' . strtoupper($value);

                if (defined($name)) {
                    $type = constant($name);

                    if ($type > 100) {
                        return $type;
                    }
                }

                return self::T_IDENTIFIER;

            // Recognize input parameters
            case (isset($this->noCase[ $value ])):
                return $this->noCase[ $value ];

            // Default
            default:
                if ($type = $this->recognizeDate($value)) {
                    return $type;
                }
        }

        return $type;
    }

    private function recognizeDate(&$value)
    {

    }

}