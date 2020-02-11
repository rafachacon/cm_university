<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Student
 *
 * @ORM\Table(name="students")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Student
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=150)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Phone", mappedBy="student", cascade={"remove"})
     */
    private $phones;

    public function __construct()
    {
        $this->phones = new ArrayCollection();
    }

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
     * @return Student
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @param mixed $phones
     *
     * @return Student
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;

        return $this;
    }

    public function addPhone(Phone $phone)
    {
        $this->phones[] = $phone;
    }
}
