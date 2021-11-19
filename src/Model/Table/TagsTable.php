<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Tags Model
 *
 * @property \App\Model\Table\DelivererTable&\Cake\ORM\Association\BelongsTo $Deliverer
 * @property \App\Model\Table\OrdererTable&\Cake\ORM\Association\BelongsTo $Orderer
 *
 * @method \App\Model\Entity\Tags get($primaryKey, $options = [])
 * @method \App\Model\Entity\Tags newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Tags[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tags|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tags saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Tags patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Tags[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tags findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TagsTable extends Table
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

        $this->setTable('na_nakamura_local_tags');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Items');

        $this->belongsToMany('Items',[
            'foreignKey' => 'tag_id',
            'targetForeignKey' => 'item_id',
            'joinTable' => 'na_nakamura_local_items_to_tags'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 45, '45文字以内にしてください。')
            ->requirePresence('name', 'create')
            ->notEmptyString('name');
        
        return $validator;
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
        return $rules;
    }
}
