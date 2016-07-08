<?php

/**
 * Copyright 2015 Rafal Zajac <rzajac@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
namespace Kicaj\Test\SchemaDump\Database;

use Kicaj\SchemaDump\Database\SchemaDumpFactory;
use Kicaj\Test\SchemaDump\BaseTest;
use Kicaj\Tools\Db\DatabaseException;
use Kicaj\Tools\Db\DbConnector;
use Kicaj\Tools\Exception;

/**
 * SchemaDumpFactory tests.
 *
 * @coversDefaultClass Kicaj\SchemaDump\Database\SchemaDumpFactory
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class SchemaDumpFactory_Test extends BaseTest
{
    /**
     * @dataProvider factoryProvider
     *
     * @covers ::factory
     *
     * @param string $driverName
     * @param string $expMsg
     */
    public function test_factory($driverName, $expMsg)
    {
        $dbConfig = self::getDbConfig('SCHEMA_DUMP1');
        $dbConfig['connection']['driver'] = $driverName;

        try {
            $mysql = SchemaDumpFactory::factory($dbConfig);

            $hasThrown = false;
            $this->assertInstanceOf('\Kicaj\SchemaDump\SchemaGetter', $mysql);
        } catch (Exception $e) {
            $hasThrown = true;
            $this->assertContains($expMsg, $e->getMessage());
        }

        if ($expMsg) {
            $this->assertTrue($hasThrown);
        } else {
            $this->assertFalse($hasThrown);
        }
    }

    public function factoryProvider()
    {
        return [
            [DbConnector::DB_DRIVER_MYSQL, ''],
            ['unknown', 'unknown database driver name: unknown'],
        ];
    }

    /**
     * @covers ::factory
     */
    public function test_factoryConnErr()
    {
        SchemaDumpFactory::_resetInstances();

        $dbConfig = self::getDbConfig('SCHEMA_DUMP1');
        $dbConfig['connection']['password'] = 'wrongOne';

        try {
            SchemaDumpFactory::factory($dbConfig, true);
            $thrown = false;
            $errMsg = '';
        } catch (DatabaseException $e) {
            $thrown = true;
            $errMsg = $e->getMessage();
        }

        $this->assertTrue($thrown);
        $this->assertContains('Access denied for user', $errMsg);
    }

    /**
     * @covers ::factory
     */
    public function test_factory_sameInstance()
    {
        $dbConfig = self::getDbConfig('SCHEMA_DUMP1');

        $db1 = SchemaDumpFactory::factory($dbConfig);
        $db2 = SchemaDumpFactory::factory($dbConfig);

        $this->assertSame($db1, $db2);
    }
}