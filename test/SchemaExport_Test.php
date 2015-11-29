<?php

namespace Kicaj\Test\SchemaDump;

use Kicaj\SchemaDump\Database\SchemaDumpFactory;
use Kicaj\SchemaDump\SchemaDump;
use Kicaj\Tools\Helper\Str;

/**
 * SchemaDump tests.
 *
 * @coversDefaultClass \Kicaj\SchemaDump\SchemaDump
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class SchemaExport_Test extends BaseTest
{
    /**
     * @var SchemaDump
     */
    protected $schemaDump;

    public function setUp()
    {
        parent::setUp();

        $this->schemaDump = SchemaDump::make(self::getDefaultConfig());
    }

    /**
     * @covers ::make
     * @covers ::__construct
     */
    public function test___construct()
    {
        SchemaDumpFactory::_resetInstances();
        $se = SchemaDump::make(self::getDefaultConfig());
        $this->assertFalse($se->hasError());
    }

    /**
     * @covers ::__construct
     */
    public function test___construct_error()
    {
        SchemaDumpFactory::_resetInstances();
        $dbConfig = self::getDefaultConfig();
        $dbConfig['connection']['password'] = 'wrongOne';

        $se = SchemaDump::make($dbConfig);
        $this->assertTrue($se->hasError());
    }

    /**
     * @dataProvider isValidFormatProvider
     *
     * @covers ::isValidFormat
     *
     * @param string $format
     * @param string $expected
     */
    public function test_isValidFormat($format, $expected)
    {
        $this->assertSame($expected, $this->schemaDump->isValidFormat($format));
    }

    public function isValidFormatProvider()
    {
        return [
            [SchemaDump::FORMAT_PHP_ARRAY, true],
            [SchemaDump::FORMAT_PHP_FILE, true],
            [SchemaDump::FORMAT_SQL, true],
            ['', false],
            ['aa', false],
        ];
    }

    /**
     * @covers ::getCreateStatements
     */
    public function test_getCreateStatements_phpArray()
    {
        self::dbDropAllTables();
        self::dbLoadFixture('test1.sql');

        $gotDef = $this->schemaDump->getCreateStatements();
        $got = $this->schemaDump->getCreateStatements(SchemaDump::FORMAT_PHP_ARRAY);

        $this->assertSame($gotDef, $got);
        $this->assertSame(1, count(array_keys($got)));
        $this->assertArrayHasKey('test1', $got);
    }

    /**
     * @covers ::getCreateStatements
     */
    public function test_getCreateStatements_phpFile()
    {
        self::dbDropAllTables();
        self::dbLoadFixture('test1.sql');

        $dbConfig = self::getDefaultConfig();
        $dbConfig['add_if_not_exists'] = true;

        $got = SchemaDump::make($dbConfig)->getCreateStatements(SchemaDump::FORMAT_PHP_FILE);

        $this->assertSame(self::loadFileFixture('test1.txt'), $got);
    }

    /**
     * @covers ::getCreateStatements
     */
    public function test_getCreateStatements_sql()
    {
        self::dbDropAllTables();
        self::dbLoadFixture('test1.sql');

        $got = $this->schemaDump->getCreateStatements(SchemaDump::FORMAT_SQL);
        $got = Str::oneLine($got);
        $expected = self::loadFileFixture('test1.sql')[0];

        $this->assertSame($expected, $got);
    }

    /**
     * @covers ::getCreateStatements
     *
     * @expectedException \Kicaj\SchemaDump\SchemaException
     * @expectedExceptionMessage unknown format: unknown
     */
    public function test_getCreateStatements_unknown()
    {
        $this->schemaDump->getCreateStatements('unknown');
    }
}
