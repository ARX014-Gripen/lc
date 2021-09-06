<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * OrderList Entity
 *
 * @property int $id
 * @property int|null $deliverer_id
 * @property int $orderer_id
 * @property string $item_name
 * @property string $status
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Deliverer $na_nakamura_local_deliverer
 * @property \App\Model\Entity\Orderer $na_nakamura_local_orderer
 */
class OrderList extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'deliverer_id' => true,
        'orderer_id' => true,
        'item_name' => true,
        'delivery_date' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'na_nakamura_local_deliverer' => true,
        'na_nakamura_local_orderer' => true,
    ];
}
