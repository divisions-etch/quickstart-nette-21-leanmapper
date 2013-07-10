<?php

namespace Model\Entity;

/**
 * @property int $id
 * @property string $title
 * @property Task[] $tasks m:belongsToMany(list_id)
 */

class EList extends \LeanMapper\Entity{

}