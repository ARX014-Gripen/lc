<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Satisfaction Model
 *
 * @property \App\Model\Table\DelivererTable&\Cake\ORM\Association\BelongsTo $Deliverer
 * @property \App\Model\Table\OrdererTable&\Cake\ORM\Association\BelongsTo $Orderer
 *
 * @method \App\Model\Entity\Satisfaction get($primaryKey, $options = [])
 * @method \App\Model\Entity\Satisfaction newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Satisfaction[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Satisfaction|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Satisfaction saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Satisfaction patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Satisfaction[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Satisfaction findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SatisfactionTable extends Table
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

        $this->setTable('na_nakamura_local_satisfaction');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
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
