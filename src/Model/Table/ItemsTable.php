<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Items Model
 *
 * @property \App\Model\Table\DelivererTable&\Cake\ORM\Association\BelongsTo $Deliverer
 * @property \App\Model\Table\OrdererTable&\Cake\ORM\Association\BelongsTo $Orderer
 *
 * @method \App\Model\Entity\Items get($primaryKey, $options = [])
 * @method \App\Model\Entity\Items newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Items[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Items|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Items saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Items patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Items[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Items findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ItemsTable extends Table
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

        $this->setTable('na_nakamura_local_items');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Tags',[
            'foreignKey' => 'item_id',
            'targetForeignKey' => 'tag_id',
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
            ->maxLength('name', 255, '255文字以内にしてください。')
            ->requirePresence('name', 'create')
            ->notEmptyString('name');
        
        $validator
            ->scalar('image')
            ->maxLength('image', 255, '255文字以内にしてください。')
            ->requirePresence('image', 'create')
            ->notEmptyString('image');

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
