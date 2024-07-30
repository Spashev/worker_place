<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;

trait DomainMorphMap
{
    public function getMorphClass(): string
    {
        return array_search($this->getParentClass(), Relation::$morphMap);
    }

    protected function getParentClass(): string
    {
        return (new ReflectionClass($this))->getParentClass()->getName();
    }
}