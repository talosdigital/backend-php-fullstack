<?php

namespace DoctrineMongoODMModule\Hydrator;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Hydrator\HydratorInterface;
use Doctrine\ODM\MongoDB\UnitOfWork;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ODM. DO NOT EDIT THIS FILE.
 */
class UserEntityUserFacebookHydrator implements HydratorInterface
{
    private $dm;
    private $unitOfWork;
    private $class;

    public function __construct(DocumentManager $dm, UnitOfWork $uow, ClassMetadata $class)
    {
        $this->dm = $dm;
        $this->unitOfWork = $uow;
        $this->class = $class;
    }

    public function hydrate($document, $data, array $hints = array())
    {
        $hydratedData = array();

        /** @Field(type="string") */
        if (isset($data['facebook_id'])) {
            $value = $data['facebook_id'];
            $return = (string) $value;
            $this->class->reflFields['facebook_id']->setValue($document, $return);
            $hydratedData['facebook_id'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['username'])) {
            $value = $data['username'];
            $return = (string) $value;
            $this->class->reflFields['username']->setValue($document, $return);
            $hydratedData['username'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['email'])) {
            $value = $data['email'];
            $return = (string) $value;
            $this->class->reflFields['email']->setValue($document, $return);
            $hydratedData['email'] = $return;
        }

        /** @Field(type="string") */
        if (isset($data['picture'])) {
            $value = $data['picture'];
            $return = (string) $value;
            $this->class->reflFields['picture']->setValue($document, $return);
            $hydratedData['picture'] = $return;
        }
        return $hydratedData;
    }
}