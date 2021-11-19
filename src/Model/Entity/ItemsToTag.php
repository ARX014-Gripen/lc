<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ItemsToTag Entity
 *
 * @property int $item_id
 * @property int $tag_id
 *
 * @property \App\Model\Entity\NaNakamuraLocalItem $na_nakamura_local_item
 * @property \App\Model\Entity\NaNakamuraLocalTag $na_nakamura_local_tag
 */
class ItemsToTag extends Entity
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
        'items_id' => true,
        'tag_id' => true,
        'na_nakamura_local_item' => true,
        'na_nakamura_local_tag' => true,
    ];
}
