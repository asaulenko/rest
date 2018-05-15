<?php

namespace AppBundle\Serializer;

use AppBundle\Annotation\DeserializeEntity;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DoctrineEntityDeserializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var AnnotationReader
     */
    private $reader;
    
    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * DoctrineEntityDeserializationSubscriber constructor.
     * @param AnnotationReader $reader
     * @param Registry $doctrine
     */
    public function __construct(AnnotationReader $reader, Registry $doctrine)
    {
        $this->reader = $reader;
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'format' => 'json',
            ],
            [
                'event' => 'serializer.post_deserialize',
                'method' => 'onPostDeserialize',
                'format' => 'json',
            ],
        ];
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $deserializedType = $event->getType()['name'];
        $data = $event->getData();

        if (!class_exists($deserializedType)) {
            return;
        }

        $class = new \ReflectionClass($deserializedType);

        foreach ($class->getProperties() as $property) {
            if (!isset($data[$property->getName()])) {
                continue;
            }

            /** @var DeserializeEntity $annotation */
            $annotation = $this->reader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );

            if (null === $annotation || !class_exists($annotation->type)) {
                continue;
            }

            $data[$property->getName()] = [
                $annotation->idField => $data[$property->getName()],
            ];
        }

        $event->setData($data);
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
        $deserializedType = $event->getType()['name'];

        if (!class_exists($deserializedType)) {
            return;
        }

        $object = $event->getObject();
        $reflection = new \ReflectionObject($object);

        foreach ($reflection->getProperties() as $property) {
            /** @var DeserializeEntity $annotation */
            $annotation = $this->reader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );

            if (null === $annotation || !class_exists($annotation->type)) {
                continue;
            }

            if (!$reflection->hasMethod($annotation->setter)) {
                throw new \LogicException(
                    sprintf(
                        'Object %s does not have a method %s',
                        $reflection->getName(),
                        $annotation->setter
                    )
                );
            }

            $property->setAccessible(true);
            $deserializedEntity = $property->getValue($object);
            
            if (null === $deserializedEntity) {
                return;
            }
            
            $entityId = $deserializedEntity->{$annotation->idGetter}();

            $entity = $this->doctrine->getRepository($annotation->type)->find($entityId);

            if (null === $entity) {
                throw new NotFoundHttpException(
                    sprintf('Resource %s', $reflection->getShortName())
                );
            }

            $object->{$annotation->setter}($entity);
        }
    }
}
