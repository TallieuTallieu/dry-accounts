<?php

namespace Tnt\Account\Revisions;

use dry\db\Connection;
use Oak\Contracts\Migration\RevisionInterface;
use Oak\Contracts\Config\RepositoryInterface as Config;
use Tnt\Account\Contracts\User\AuthenticatableInterface;
use Tnt\Account\Contracts\User\ActivatableInterface;
use Tnt\Account\Contracts\User\ResetableInterface;
use Tnt\Account\Model\User;
use Tnt\Dbi\QueryBuilder;
use Tnt\Dbi\TableBuilder;

class CreateUserTable implements RevisionInterface
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
     * CreateUserTable constructor.
     * @param QueryBuilder $queryBuilder
     * @param Config $config
     */
    public function __construct(QueryBuilder $queryBuilder, Config $config)
    {
        $this->queryBuilder = $queryBuilder;
        $this->config = $config;
    }

    /**
     * Create the user table with dynamic fields based on configured model interfaces.
     */
    public function up()
    {
        $modelClass = $this->config->get('accounts.model', User::class);

        $this->queryBuilder
            ->table('account_user')
            ->create(function (TableBuilder $table) use ($modelClass) {
                // Base columns that every user needs
                $table->addColumn('id', 'int')->length(11)->primaryKey();
                $table->addColumn('created', 'int')->length(11);
                $table->addColumn('updated', 'int')->length(11);

                // Check if model implements AuthenticatableInterface
                if (
                    $this->implementsInterface(
                        $modelClass,
                        AuthenticatableInterface::class
                    )
                ) {
                    $table
                        ->addColumn(
                            $modelClass::getAuthIdentifierField(),
                            'varchar'
                        )
                        ->length(255);
                    $table
                        ->addColumn($modelClass::getPasswordField(), 'varchar')
                        ->length(255);
                    $table->addUnique('email');
                }

                // Check if model implements ActivatableInterface
                if (
                    $this->implementsInterface(
                        $modelClass,
                        ActivatableInterface::class
                    )
                ) {
                    $table
                        ->addColumn($modelClass::getIsActivatedField(), 'tinyint')
                        ->length(1)
                        ->default(0);
                    $table
                        ->addColumn($modelClass::getTempTokenField(), 'varchar')
                        ->length(255)
                        ->null();
                }

                // Check if model implements ResetableInterface
                if (
                    $this->implementsInterface(
                        $modelClass,
                        ResetableInterface::class
                    )
                ) {
                    $table
                        ->addColumn($modelClass::getResetTokenField(), 'varchar')
                        ->length(255)
                        ->null();
                }
            });

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     *
     */
    public function down()
    {
        $this->queryBuilder->table('account_user')->drop();

        $this->queryBuilder->build();

        Connection::get()->query($this->queryBuilder->getQuery());
    }

    /**
     * @return string
     */
    public function describeUp(): string
    {
        return 'Create account_user table';
    }

    /**
     * @return string
     */
    public function describeDown(): string
    {
        return 'Drop account_user table';
    }

    /**
     * Check if a class implements a specific interface.
     *
     * @param string $className The class name to check
     * @param string $interfaceName The interface name to check for
     * @return bool True if the class implements the interface, false otherwise
     */
    private function implementsInterface(
        string $className,
        string $interfaceName
    ): bool {
        if (!class_exists($className)) {
            return false;
        }

        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->implementsInterface($interfaceName);
    }
}
