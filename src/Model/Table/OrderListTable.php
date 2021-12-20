<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * OrderList Model
 *
 * @property \App\Model\Table\DelivererTable&\Cake\ORM\Association\BelongsTo $Deliverer
 * @property \App\Model\Table\OrdererTable&\Cake\ORM\Association\BelongsTo $Orderer
 *
 * @method \App\Model\Entity\OrderList get($primaryKey, $options = [])
 * @method \App\Model\Entity\OrderList newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\OrderList[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\OrderList|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrderList saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\OrderList patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\OrderList[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\OrderList findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrderListTable extends Table
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

        $this->setTable('na_nakamura_local_order_list');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Deliverer', [
            'foreignKey' => 'deliverer_id',
        ]);

        $this->belongsTo('Orderer', [
            'foreignKey' => 'orderer_id',
        ]);

        $this->belongsTo('Items', [
            'foreignKey' => 'item_id',
        ]);

        $this->belongsTo('Signature', [
            'foreignKey' => 'signature_id',
        ]);

        $this->hasOne('GroupByOrderList', [
            'className' => 'OrderList',
            'foreignKey' => 'id',
            // 'bindingKey' => 'id',
            'joinType' => 'INNER'
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
            ->date('delivery_date', ['ymd', 'mdy'])
            ->requirePresence('delivery_date', 'create')
            ->notEmpty('delivery_date');
        
        $validator
            ->scalar('status')
            ->maxLength('status', 255, '255文字以内にしてください。')
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

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
        $rules->add($rules->existsIn(['deliverer_id'], 'Deliverer'));
        $rules->add($rules->existsIn(['orderer_id'], 'Orderer'));

        return $rules;
    }
}
