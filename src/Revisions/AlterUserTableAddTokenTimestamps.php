<?php

namespace Tnt\Account\Revisions;

use dry\db\Connection;
use Oak\Contracts\Config\RepositoryInterface as Config;
use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Account\Contracts\User\ActivatableInterface;
use Tnt\Account\Contracts\User\ResetableInterface;
use Tnt\Account\Model\User;
use Tnt\Dbi\QueryBuilder;
use Tnt\Dbi\TableBuilder;

/**
 * Records when a reset or activation token was minted, so the token can expire.
 */
class AlterUserTableAddTokenTimestamps implements RevisionInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var Config
     */
    private $config;

    /**
     * AlterUserTableAddTokenTimestamps constructor.
     * @param QueryBuilder $queryBuilder
     * @param Config $config
     */
    public function __construct(QueryBuilder $queryBuilder, Config $config)
    {
        $this->queryBuilder = $queryBuilder;
        $this->config = $config;
    }

    /**
     * Add the token timestamp columns to the user table.
     *
     * @return void
     */
    public function up(): void
    {
        $modelClass = $this->config->get('accounts.model') ?? User::class;

        $columns = $this->columns($modelClass);

        if ($columns === []) {
            return;
        }

        $this->queryBuilder
            ->table('account_user')
            ->alter(function (TableBuilder $table) use ($columns) {
                foreach ($columns as $column) {
                    $table
                        ->addColumn($column, 'int')
                        ->length(11)
                        ->null();
                }
            });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     * Remove the token timestamp columns from the user table.
     *
     * @return void
     */
    public function down(): void
    {
        $modelClass = $this->config->get('accounts.model') ?? User::class;

        $columns = $this->columns($modelClass);

        if ($columns === []) {
            return;
        }

        $this->queryBuilder
            ->table('account_user')
            ->alter(function (TableBuilder $table) use ($columns) {
                foreach ($columns as $column) {
                    $table->dropColumn($column);
                }
            });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     * @return string
     */
    public function describeUp(): string
    {
        return 'Alter account_user table add reset/activation token timestamps';
    }

    /**
     * @return string
     */
    public function describeDown(): string
    {
        return 'Alter account_user table drop reset/activation token timestamps';
    }

    /**
     * The timestamp columns the configured model actually needs.
     *
     * @param class-string $modelClass
     * @return array<int, string>
     */
    private function columns(string $modelClass): array
    {
        $columns = [];

        if (is_a($modelClass, ResetableInterface::class, true)) {
            $columns[] = $modelClass::getResetTokenCreatedField();
        }

        if (is_a($modelClass, ActivatableInterface::class, true)) {
            $columns[] = $modelClass::getTempTokenCreatedField();
        }

        return $columns;
    }
}
