<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Orderer Model
 *
 * @method \App\Model\Entity\Orderer get($primaryKey, $options = [])
 * @method \App\Model\Entity\Orderer newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Orderer[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Orderer|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Orderer saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Orderer patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Orderer[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Orderer findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrdererTable extends Table
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

        $this->setTable('na_nakamura_local_orderer');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->scalar('address')
            ->maxLength('address', 255, '255文字以内にしてください。')
            ->requirePresence('address', 'create')
            ->notEmptyString('address');

        $validator
            ->numeric('lat')
            ->requirePresence('lat', 'create')
            ->notEmptyString('lat');

        $validator
            ->numeric('lng')
            ->requirePresence('lng', 'create')
            ->notEmptyString('lng');

        return $validator;
    }
}
