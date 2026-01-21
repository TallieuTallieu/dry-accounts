<?php

namespace Tnt\Account\Revisions;

use dry\db\Connection;
use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Dbi\QueryBuilder;
use Tnt\Dbi\TableBuilder;

class AlterUserTableAddRefreshToken implements RevisionInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * CreateUserTable constructor.
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Add refresh token columns to the user table.
     *
     * @return void
     */
    public function up(): void
    {
        $this->queryBuilder->table('account_user')->alter(function (TableBuilder $table) {

            $table->addColumn('refresh_token', 'varchar')->length(255);
            $table->addColumn('refresh_token_expiry_time', 'int')->length(11);
        });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     * Remove refresh token columns from the user table.
     *
     * @return void
     */
    public function down(): void
    {
        $this->queryBuilder->table('account_user')->alter(function (TableBuilder $table) {

            $table->dropColumn('refresh_token');
            $table->dropColumn('refresh_token_expiry_time');
        });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     * @return string
     */
    public function describeUp(): string
    {
        return 'Alter account_user table add refresh_token';
    }

    /**
     * @return string
     */
    public function describeDown(): string
    {
        return 'Alter account_user table drop refresh_token';
    }
}