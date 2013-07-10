<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property string $text
 * @property string $created
 * @property int $done
 * @property User $user m:hasOne
 * @property EList $list m:hasOne(list_id:list)
 */

class Task extends \LeanMapper\Entity{

}