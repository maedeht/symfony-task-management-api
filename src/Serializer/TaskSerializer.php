<?php

namespace App\Serializer;


use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TaskSerializer
{
    private $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer([new ObjectNormalizer()]);
    }

    public function serialize($data)
    {
        $serialized = $this->serializer->normalize($data, null, [
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            AbstractObjectNormalizer::IGNORED_ATTRIBUTES => [
                'createdAt',
                'updatedAt'
            ]
        ]);

        return $serialized;
    }
}