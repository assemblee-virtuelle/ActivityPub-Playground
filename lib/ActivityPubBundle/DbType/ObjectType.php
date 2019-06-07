<?php

namespace AV\ActivityPubBundle\DbType;

class ObjectType extends EnumType
{
    protected $name = 'object_type';

    public const NOTE = 'Note';

    public const PLACE = 'Place';
}