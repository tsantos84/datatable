<?php

namespace Tavs\DataTable\Query;

/**
 * Class Parser
 * @package Tavs\DataTable
 */
class Parser
{
    const OP_AND = 'AND';
    const OP_OR = 'OR';

    const OP_EQUAL = 'eq';
    const OP_NOT_EQUAL = 'neq';
    const OP_LIKE = 'like';
    const OP_NOT_LIKE = 'notLike';
    const OP_IN = 'in';
    const OP_NOT_IN = 'notIn';
    const OP_IS_NULL = 'isNull';
    const OP_IS_NOT_NULL = 'isNotNull';
    const OP_GREATER_THAN = 'gte';
    const OP_LESS_THAN = 'lte';
    const OP_BETWEEN = 'between';

    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var array
     */
    private $criterias;

    /**
     *
     */
    public function __construct()
    {
        $this->lexer = new Lexer();
        $this->criterias = [];
    }

    /**
     * @param $input
     *
     * @return array
     */
    public function parse($input)
    {
        $this->lexer->setInput(trim($input));

        return $this->buildCriterias();
    }

    /**
     * @return array
     */
    private function buildCriterias()
    {
        $criterias = [];

        // move para o primeiro token da string
        $this->lexer->moveNext();

        while (null !== $this->lexer->lookahead) {

            $criteria = [
                'field'       => null,
                'expressions' => []
            ];

            if ($this->lexer->isNextToken(Lexer::T_IDENTIFIER)) {

                // matches the field name
                $criteria['field'] = $this->lexer->lookahead['value'];
                $this->match(Lexer::T_IDENTIFIER);

                // matches the field separator
                $this->match(Lexer::T_COLON);

                // matches the values "Value1[,Value2,[Value3]]"
                $this->expressions($criteria);

            } else {
                $this->match(Lexer::T_IDENTIFIER);
            }

            $criterias[] = $criteria;
        }

        return $criterias;
    }

    /**
     * Parse the values
     *
     * @param array $criteria
     */
    private function expressions(array &$criteria)
    {
        do {

            $excluded = false;
            $modifier = null;

            // matches exclusion operator
            if ($this->lexer->isNextToken(Lexer::T_TILDE)) {
                $this->lexer->moveNext();
                $excluded = true;
            }

            // matches the value modifier
            if ($this->lexer->isNextTokenAny([Lexer::T_RANGE])) {
                $modifier = $this->lexer->lookahead;
                $this->lexer->moveNext();
            }

            switch ($this->lexer->lookahead['type']) {

                case Lexer::T_STRING:
                    $this->stringOperator($criteria, $excluded, $modifier);
                    break;

                case Lexer::T_INTEGER:
                case Lexer::T_FLOAT:
                    $this->numericalValue($criteria, $excluded, $modifier);
                    break;

                case Lexer::T_DATE:
                    $this->dateValue($criteria, $excluded, $modifier);
                    break;

                case Lexer::T_TRUE:
                case Lexer::T_FALSE:
                    $this->booleanValue($criteria);
                    break;

                case Lexer::T_EMPTY:
                    $this->emptyValue($criteria, $excluded);
                    break;

                default:
                    $this->matchAny($this->lexer->getValueTypes());
            }

            // matches the value separator
            if ($this->lexer->isNextToken(Lexer::T_COMMA)) {
                $this->match(Lexer::T_COMMA);
            }

        } while (
            $this->lexer->lookahead['type'] !== Lexer::T_IDENTIFIER &&
            $this->lexer->lookahead !== null
        );
    }

    /**
     * @param $criteria
     * @param $excluded
     * @param $valueModifier
     */
    private function stringOperator(&$criteria, $excluded, $valueModifier)
    {
        // Case 1: No modifier before nor after
        // Case 2: Modifier only after           ("Ta"..)
        // Case 3: Modifier only before          (.."es")
        // case 4: Modifier as a value separator ("Ta".."es")

        $stringToken = $this->lexer->lookahead;
        $value = $stringToken['value'];

        if (null == $valueModifier) {

            // moves to next token
            $this->lexer->moveNext();

            // Case 2 or 4
            if ($this->lexer->isNextToken(Lexer::T_RANGE)) {

                $this->match(Lexer::T_RANGE);

                // Case 2
                $value .= '%';

                // Case 4
                if ($this->lexer->isNextToken(Lexer::T_STRING)) {
                    $value .= $this->lexer->lookahead['value'];
                    $this->lexer->moveNext();
                }

            } else { // Case 1
                $value = '%' . $value . '%';
            }

        } else { // Case 3
            $value = '%' . $value;
            $this->lexer->moveNext();
        }

        // exclusion operator
        if ($excluded) {
            $operator = self::OP_NOT_LIKE;
            $connector = self::OP_AND;
        } else {
            $operator = self::OP_LIKE;
            $connector = self::OP_OR;
        }

        // the expression
        $criteria['expressions'][] = [
            'val'  => '\'' . $value . '\'',
            'expr' => $operator,
            'connector' => $connector
        ];
    }

    /**
     * @param $criteria
     * @param $excluded
     * @param $valueModifier
     */
    private function numericalValue(&$criteria, $excluded, $valueModifier)
    {
        // Case 1: No modifier before nor after
        // Case 2: Modifier only after           (10..)
        // Case 3: Modifier only before          (..20)
        // case 4: Modifier as a value separator (10..20)

        $numericalToken = $this->lexer->lookahead;
        $value = $numericalToken['value'];
        $expr = $excluded ? self::OP_NOT_EQUAL : self::OP_EQUAL;

        if (null == $valueModifier) {

            // moves to next token
            $this->lexer->moveNext();

            // Case 2 or 4
            if ($this->lexer->isNextToken(Lexer::T_RANGE)) {

                $this->match(Lexer::T_RANGE);

                // Case 4
                if ($this->lexer->isNextTokenAny([Lexer::T_INTEGER, Lexer::T_FLOAT])) {
                    $value = [$value, $this->lexer->lookahead['value']];
                    $expr = self::OP_BETWEEN;
                    $this->lexer->moveNext();
                } else {
                    // Case 2
                    $expr = self::OP_GREATER_THAN;
                }

            }

        } else { // Case 3
            $this->lexer->moveNext();
        }

        // the expression
        $criteria['expressions'][] = [
            'val'  => $value,
            'expr' => $expr,
            'connector' => $excluded ? self::OP_AND : self::OP_OR
        ];
    }

    /**
     * @param $criteria
     * @param $excluded
     * @param $valueModifier
     */
    private function dateValue(&$criteria, $excluded, $valueModifier)
    {
        // Case 1: No modifier before nor after
        // Case 2: Modifier only after           (28/11/1984..)
        // Case 3: Modifier only before          (..28/11/1984)
        // case 4: Modifier as a value separator (02/10/1984..28/11/1984)

        $numericalToken = $this->lexer->lookahead;
        $value = sprintf("'%s'", $numericalToken['value']);
        $expr = $excluded ? self::OP_NOT_EQUAL : self::OP_EQUAL;

        if (null == $valueModifier) {

            // moves to next token
            $this->lexer->moveNext();

            // Case 2 or 4
            if ($this->lexer->isNextToken(Lexer::T_RANGE)) {

                $this->match(Lexer::T_RANGE);

                // Case 4
                if ($this->lexer->isNextToken(Lexer::T_DATE)) {
                    $value = [$value, sprintf("'%s'", $this->lexer->lookahead['value'])];
                    $expr = self::OP_BETWEEN;
                    $this->lexer->moveNext();
                } else {
                    // Case 2
                    $expr = self::OP_GREATER_THAN;
                }

            }

        } else { // Case 3
            $expr = self::OP_LESS_THAN;
            $this->lexer->moveNext();
        }

        $criteria['field'] = sprintf('DATE(%s)', $criteria['field']);

        // the expression
        $criteria['expressions'][] = [
            'val'  => $value,
            'expr' => $expr,
            'connector' => $excluded ? self::OP_AND : self::OP_OR
        ];
    }

    /**
     * @param $criteria
     */
    private function booleanValue(&$criteria)
    {
        $criteria['expressions'][] = [
            'val' => $this->lexer->lookahead['value'],
            'expr' => self::OP_EQUAL,
            'connector' =>  self::OP_AND
        ];

        $this->lexer->moveNext();
    }

    /**
     * @param $criteria
     * @param $excluded
     */
    private function emptyValue(&$criteria, $excluded)
    {
        $criteria['expressions'][] = [
            'expr' => $excluded ? self::OP_IS_NOT_NULL : self::OP_IS_NULL,
            'val' => null,
            'connector' => $excluded ?  self::OP_AND : self::OP_OR
        ];

        $this->lexer->moveNext();
    }

    /**
     * Attempts to match the given token with the current lookahead token.
     * If they match, updates the lookahead token; otherwise raises a syntax error.
     *
     * @param integer $token Type of token.
     *
     * @return boolean True if tokens match; false otherwise.
     */
    private function match($token)
    {
        if (!$this->lexer->isNextToken($token)) {
            $this->syntaxError($this->lexer->getLiteral($token));
        }

        return $this->lexer->moveNext();
    }

    /**
     * Attempts to match the current lookahead token with any of the given tokens.
     *
     * If any of them matches, this method updates the lookahead token; otherwise
     * a syntax error is raised.
     *
     * @param array $tokens
     *
     * @return boolean
     */
    private function matchAny(array $tokens)
    {
        if (!$this->lexer->isNextTokenAny($tokens)) {
            $this->syntaxError(implode(' or ', array_map([$this->lexer, 'getLiteral'], $tokens)));
        }

        return $this->lexer->moveNext();
    }

    /**
     * Generates a new syntax error.
     *
     * @param string     $expected Expected string.
     * @param array|null $token    Optional token.
     *
     * @return void
     *
     * @throws \Exception
     */
    private function syntaxError($expected, $token = null)
    {
        if ($token === null) {
            $token = $this->lexer->lookahead;
        }

        $message = sprintf('Expected %s, got ', $expected);
        $message .= ($this->lexer->lookahead === null)
            ? 'end of string'
            : sprintf("'%s' at position %s.", $token['value'], $token['position']);

//        if (strlen($this->context)) {
//            $message .= ' in ' . $this->context;
//        }

        throw new \Exception('[Syntax Error] ' . $message);
    }
}