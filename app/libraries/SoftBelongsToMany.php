<?php

/**
 * write by EThaiZone <ethaizone@hotmail.com>
 * todo feature - about update data of pivot table
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SoftBelongsToMany extends \Illuminate\Database\Eloquent\Relations\BelongsToMany {

    /**
     * The name of the "deleted at" column.
     *
     * @var string
     */
    const DELETED_AT = 'deleted_at';

    public function __construct(Builder $query, Model $parent, $table, $foreignKey, $otherKey, $relationName = null)
    {
        parent::__construct($query, $parent, $table, $foreignKey, $otherKey, $relationName);

        // where deleted_at null - important!
        $this->query->whereNull($table.'.'.static::DELETED_AT);
    }

    /**
     * Attach a model to the parent.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @param  bool   $touch
     * @return void
     */
    public function attach($id, array $attributes = array(), $touch = true)
    {
        if ($id instanceof Model) $id = $id->getKey();

        $query = $this->newPivotStatement();

        ## before this line code is same
        # after this is new logic

        $id = array_values(array_unique((array) $id));

        // get all pivot records with trashed
        $allRecords = array();
        foreach($this->newPivotQueryWithTrashed()->get() as $tmpRecord)
        {
            $allRecords[$tmpRecord->{$this->otherKey}] = $tmpRecord;
        }

        foreach ($id as $index => $otherKey) {
            // test to get pivot from all record
            $pivotModel = @$allRecords[$otherKey];

            if ($pivotModel)
            {
                // has pivot but we shoud check it has deleted_at or not

                $deleted_at = $pivotModel->{static::DELETED_AT};
                if ($deleted_at)
                {
                    // this pivot is deleted but user want to attach it
                    // so we will recover it
                    $this->newPivotQueryWithTrashed()
                        ->where($this->otherKey, $otherKey)
                        ->update( array(static::DELETED_AT => null) );
                }

                // when pivot record is exists
                // remove it from array
                unset($id[$index]);
            }

        }

        $id = array_values($id);

        if (count($id) > 0)
        {
            $query->insert($this->createAttachRecords($id, $attributes));

            if ($touch) $this->touchIfTouching();
        }


    }

    /**
     * Detach models from the relationship.
     *
     * @param  int|array  $ids
     * @param  bool  $touch
     * @return int
     */
    public function detach($ids = array(), $touch = true)
    {
        if ($ids instanceof Model) $ids = (array) $ids->getKey();

        $query = $this->newPivotQuery();

        $ids = (array) $ids;

        if (count($ids) > 0)
        {
            $query->whereIn($this->otherKey, $ids);
        }

        if ($touch) $this->touchIfTouching();

        ## before this line code is same

        // Once we have all of the conditions set on the statement, we are ready
        // to run the update for set deleted_at on the pivot table.
        $results = $query->update( array(static::DELETED_AT => DB::raw('NOW()')) );

        ## after this line code is same

        return $results;
    }

    /**
     * Get a new plain query builder for the pivot table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function newPivotStatement()
    {
        return parent::newPivotStatement()->whereNull(static::DELETED_AT);
    }

    /**
     * Get a new plain query builder for the pivot table. with trashed.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function newPivotStatementWithTrashed()
    {
        return parent::newPivotStatement();
    }

    /**
     * Create a new query builder for the pivot table. with trashed.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newPivotQueryWithTrashed()
    {
        $query = $this->newPivotStatementWithTrashed();

        return $query->where($this->foreignKey, $this->parent->getKey());
    }

}