<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;

final class PrefixNamingStrategy extends UnderscoreNamingStrategy
{
    public function classToTableName(string $className): string
    {
        return 'jt_' . parent::classToTableName($className);
    }

    public function joinTableName(string $sourceEntity, string $targetEntity, string $propertyName): string
    {
        return 'jt_' . parent::classToTableName($sourceEntity) . '_' . parent::classToTableName($targetEntity);
    }

    public function joinKeyColumnName(string $entityName, string|null $referencedColumnName): string
    {
        return parent::classToTableName($entityName) . '_' . ($referencedColumnName ?: $this->referenceColumnName());
    }
}
