<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Orderer Entity
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property float $lat
 * @property float $lng
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class Orderer extends Entity
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
        'id' => true,
        'name' => true,
        'address' => true,
        'lat' => true,
        'lng' => true,
        'created' => true,
        'modified' => true,
    ];
}
