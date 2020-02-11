<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Student2Subject
 *
 * @ORM\Table(name="student2subjects")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Student2Subject
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Student
     * @ORM\ManyToOne(targetEntity="Student", inversedBy="Student2Subject")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $student;

    /**
     * @var Subject
     * @ORM\ManyToOne(targetEntity="Subject", inversedBy="Student2Subject")
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $subject;

    /**
     * This would be normally the total score of the student y this subject, from 0 to 10.
     *
     * @ORM\Column(name="score", type="float", options={"unsigned": true, "default": 0.0})
     */
    private $score;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student
    {
        return $this->student;
    }

    /**
     * @param Student $student
     *
     * @return Student2Subject
     */
    public function setStudent(Student $student)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * @return Subject
     */
    public function getSubject(): Subject
    {
        return $this->subject;
    }

    /**
     * @param Subject $subject
     *
     * @return Student2Subject
     */
    public function setSubject(Subject $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     *
     * @return Student2Subject
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }
}
