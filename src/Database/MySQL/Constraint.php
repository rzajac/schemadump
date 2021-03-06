<?php declare(strict_types=1);
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

namespace Kicaj\Schema\Database\MySQL;

use Kicaj\Schema\Itf\ConstraintItf;
use Kicaj\Schema\Itf\IndexItf;
use Kicaj\Schema\Itf\TableItf;
use Kicaj\Schema\SchemaEx;
use Kicaj\Tools\Helper\Str;

/**
 * MySQL index constraint.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Constraint implements ConstraintItf
{
    /**
     * The index constraint definition.
     *
     * @var string
     */
    protected $constDef;

    /**
     * The table the index constraint belongs to.
     *
     * @var TableItf
     */
    protected $table;

    /**
     * The index constraint name.
     *
     * @var string
     */
    protected $name;

    /**
     * The index name the constraint is on.
     *
     * @var string
     */
    protected $fKeyName;

    /**
     * The foreign table name.
     *
     * @var string
     */
    protected $fTableName;

    /**
     * The foreign index name.
     *
     * @var string
     */
    protected $fIndexName;

    /**
     * Constructor.
     *
     * @param string   $constDef The index constraint definition.
     * @param TableItf $table    The table index constraint belongs to.
     *
     * @throws SchemaEx
     */
    public function __construct(string $constDef, TableItf $table)
    {
        $this->constDef = $constDef;
        $this->table = $table;

        $this->parseConstraint();
    }

    /**
     * Parse index constraint.
     *
     * @throws SchemaEx
     */
    protected function parseConstraint()
    {
        preg_match('/(?:.*)?CONSTRAINT `(.*?)` FOREIGN KEY \(`(.*?)`\) REFERENCES `(.*?)` \(`(.*?)`\)(?: .*)?/', $this->constDef, $matches);

        if (count($matches) != 5) {
            throw new SchemaEx('Cannot parse index constraint: ' . $this->constDef);
        }

        $this->name = trim($matches[1]);
        $this->fKeyName = trim($matches[2]);
        $this->fTableName = trim($matches[3]);
        $this->fIndexName = trim($matches[4]);
    }

    /**
     * Returns true if line is definition of one of the index constraints.
     *
     * @param string $line The line from CREATE STATEMENT.
     *
     * @return bool
     */
    public static function isConstraintDef(string $line): bool
    {
        if (Str::startsWith($line, 'CONSTRAINT')) {
            return true;
        }

        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return name of the foreign key.
     *
     * @return string
     */
    public function getForeignKeyName(): string
    {
        return $this->fKeyName;
    }

    public function getIndex(): IndexItf
    {
        try {
            $index = $this->table->getIndexByName($this->name);
        } catch (SchemaEx $e) {
            $index = $this->table->getIndexByName($this->fKeyName);
        }

        return $index;
    }

    public function getForeignTableName(): string
    {
        return $this->fTableName;
    }

    public function getForeignIndexName(): string
    {
        return $this->fIndexName;
    }

    public function getTable(): TableItf
    {
        return $this->table;
    }
}
