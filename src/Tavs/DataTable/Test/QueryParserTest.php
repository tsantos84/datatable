<?php

namespace Tavs\DataTable\Test;

use Tavs\DataTable\Query\Parser;

/**
 * Class QueryParserTest
 * @package Tavs\DataTable\Test
 */
class QueryParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * Initiate the parser
     */
    public function setUp()
    {
        $this->parser = new Parser();
    }

    /**
     * Test all possible expressions in text values
     */
    public function tesstTextValues()
    {
        $input = implode(' ', [
            'Alias.Field1:"Tales"',
            'Field2:"Tales","Santos"',
            'Field3:"Tales",~"Augusto","Santos"',
            'Field4:"Ta"..',
            'Field5:.."es"',
            'Field6:"Ta".."es"'
        ]);

        $expected = [

            // Field1:"Tales"
            [
                'field'       => 'Alias.Field1',
                'expressions' => [
                    [
                        'val'  => "'%Tales%'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ],

            // Field2:"Tales","Santos"
            [
                'field'       => 'Field2',
                'expressions' => [
                    [
                        'val'  => "'%Tales%'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ],
                    [
                        'val'  => "'%Santos%'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ],

            // Field3:"Tales",~"Augusto","Santos"
            [
                'field'       => 'Field3',
                'expressions' => [
                    [
                        'val'  => "'%Tales%'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ],
                    [
                        'val'  => "'%Augusto%'",
                        'expr' => 'notLike',
                        'connector' => Parser::OP_AND
                    ],
                    [
                        'val'  => "'%Santos%'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ],

            // Field4:"Ta"..
            [
                'field'       => 'Field4',
                'expressions' => [
                    [
                        'val'  => "'Ta%'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ],

            // Field5:.."es"
            [
                'field'       => 'Field5',
                'expressions' => [
                    [
                        'val'  => "'%es'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ],

            // Field6:"Ta".."es"
            [
                'field'       => 'Field6',
                'expressions' => [
                    [
                        'val'  => "'Ta%es'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->parser->parse($input));
    }

    /**
     * Test all possible expressions in numeric values
     */
    public function tesstNumericValues()
    {
        $input = implode(' ', [
            'Field1:10',
            'Field2:10,20,30.5',
            'Field3:10,~20,30.5',
            'Field4:10..',
            'Field5:..10',
            'Field6:10..20'
        ]);

        $expected = [

            // Field1:10
            [
                'field'       => 'Field1',
                'expressions' => [
                    [
                        'val'  => 10,
                        'expr' => 'eq'
                    ]
                ]
            ],

            // Field2:10,20,30.5
            [
                'field'       => 'Field2',
                'expressions' => [
                    [
                        'val'  => [10, 20, 30.5],
                        'expr' => 'in'
                    ]
                ]
            ],

            // Field3:10,~20,30.5
            [
                'field'       => 'Field3',
                'expressions' => [
                    [
                        'val'  => [10, 30.5],
                        'expr' => 'in',
                    ],
                    [
                        'val'  => [20],
                        'expr' => 'notIn',
                    ]
                ]
            ],

            // Field4:10..
            [
                'field'       => 'Field4',
                'expressions' => [
                    [
                        'val'  => 10,
                        'expr' => 'gte'
                    ]
                ]
            ],

            // Field5:..20
            [
                'field'       => 'Field5',
                'expressions' => [
                    [
                        'val'  => 20,
                        'expr' => 'lte'
                    ]
                ]
            ],

            // Field6:10..20
            [
                'field'       => 'Field6',
                'expressions' => [
                    [
                        'val'  => [10, 20],
                        'expr' => 'btw'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->parser->parse($input));
    }

    /**
     * Test all possible expressions in date values
     */
    public function testDateValues()
    {
        $input = implode(' ', [
            'Alias.Field1:28/11/1984',
            'Field2:28/11/1984,02/10/1987',
            'Field3:28/11/1984,~02/10/1987',
            'Field4:28/11/1984..',
            'Field5:..28/11/1984',
            'Field6:28/11/1984..02/10/1987'
        ]);

        $expected = [

            // Alias.Field1:{28/11/1984}
            [
                'field'       => 'Alias.Field1',
                'expressions' => [
                    [
                        'val'  => \DateTime::createFromFormat('dd/mm/YYY', '28/11/1984'),
                        'expr' => 'eq',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ],

            // Field2:"Tales","Santos"
            [
                'field'       => 'Field2',
                'expressions' => [
                    [
                        'val'  => "'%Tales%'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ],
                    [
                        'val'  => "'%Santos%'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ],

            // Field3:"Tales",~"Augusto","Santos"
            [
                'field'       => 'Field3',
                'expressions' => [
                    [
                        'val'  => "'%Tales%'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ],
                    [
                        'val'  => "'%Augusto%'",
                        'expr' => 'notLike',
                        'connector' => Parser::OP_AND
                    ],
                    [
                        'val'  => "'%Santos%'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ],

            // Field4:"Ta"..
            [
                'field'       => 'Field4',
                'expressions' => [
                    [
                        'val'  => "'Ta%'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ],

            // Field5:.."es"
            [
                'field'       => 'Field5',
                'expressions' => [
                    [
                        'val'  => "'%es'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ],

            // Field6:"Ta".."es"
            [
                'field'       => 'Field6',
                'expressions' => [
                    [
                        'val'  => "'Ta%es'",
                        'expr' => 'like',
                        'connector' => Parser::OP_OR
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->parser->parse($input));
    }

    /**
     * 'Field1:True Field2:False'
     */
    public function testBooleanValues()
    {
        $input = 'Field1:True Field2:False';

        $expected = [
            [
                'field'       => 'Field1',
                'expressions' => [
                    [
                        'val'  => 1,
                        'expr' => 'eq',
                        'connector' => Parser::OP_AND
                    ]
                ]
            ],
            [
                'field'       => 'Field2',
                'expressions' => [
                    [
                        'val'  => 0,
                        'expr' => 'eq',
                        'connector' => Parser::OP_AND
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->parser->parse($input));
    }

    /**
     * Test all possible expressions in empty fields
     */
    public function testEmptyEvalues()
    {
        $input = 'Field1:Empty Field2:~Empty';

        $expected = [
            [
                'field'       => 'Field1',
                'expressions' => [
                    [
                        'expr' => 'isNull',
                        'val'  => null,
                        'connector' => Parser::OP_OR
                    ]
                ]
            ],
            [
                'field'       => 'Field2',
                'expressions' => [
                    [
                        'expr' => 'isNotNull',
                        'val'  => null,
                        'connector' => Parser::OP_AND
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->parser->parse($input));
    }
}