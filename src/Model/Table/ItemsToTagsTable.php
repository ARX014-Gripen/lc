<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ItemsToTags Model
 *
 * @property \App\Model\Table\NaNakamuraLocalItemsTable&\Cake\ORM\Association\BelongsTo $NaNakamuraLocalItems
 * @property \App\Model\Table\NaNakamuraLocalTagsTable&\Cake\ORM\Association\BelongsTo $NaNakamuraLocalTags
 *
 * @method \App\Model\Entity\ItemsToTag get($primaryKey, $options = [])
 * @method \App\Model\Entity\ItemsToTag newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ItemsToTag[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ItemsToTag|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ItemsToTag saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ItemsToTag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ItemsToTag[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ItemsToTag findOrCreate($search, callable $callback = null, $options = [])
 */
class ItemsToTagsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('na_nakamura_local_items_to_tags');
        $this->setDisplayField('item_id');
        $this->setPrimaryKey(['item_id', 'tag_id']);

        $this->belongsTo('Items', [
            'foreignKey' => 'item_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Tags', [
            'foreignKey' => 'tag_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['item_id'], 'Items'));
        $rules->add($rules->existsIn(['tag_id'], 'Tags'));

        return $rules;
    }
}
