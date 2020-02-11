<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Subject
 *
 * @ORM\Table(name="subjects")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Subject
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return Subject
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
