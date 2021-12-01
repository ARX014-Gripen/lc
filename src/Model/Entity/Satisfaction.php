<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Satisfaction Entity
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
class Satisfaction extends Entity
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
        'order_id' => true,
        'item_id' => true,
        'level' => true,
        'delivery_datetime' => true,
    ];
}
