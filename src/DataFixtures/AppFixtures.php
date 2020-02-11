<?php

namespace App\DataFixtures;

use App\Entity\Phone;
use App\Entity\Student;
use App\Entity\Student2Subject;
use App\Entity\Subject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    const TOTAL_SUBJECTS = 10;

    public function load(ObjectManager $manager)
    {
        $allSubjects = [];

        // Creating subjects
        for ($i = 1; $i <= self::TOTAL_SUBJECTS; $i++) {
            $subject = new Subject();
            $subject->setName('Subject '.$i);

            $manager->persist($subject);

            $allSubjects[] = $subject;
        }

        $manager->flush();

        // Creating students (with just one phone, but they can be more).
        for ($i = 1; $i <= 100; $i++) {
            $faker = Faker\Factory::create();

            $student = new Student();
            $student->setName($faker->name);

            $phone = new Phone();
            $phone->setName('personal phone');
            $phone->setNumber($faker->phoneNumber);
            $phone->setStudent($student);

            $manager->persist($student);
            $manager->persist($phone);

            // And let's say each student has been taking all subjects
            // with a random score from 0 to 10.
            for ($j = 0; $j< self::TOTAL_SUBJECTS; $j++) {
                $score = new Student2Subject();
                $score->setStudent($student);
                $score->setSubject($allSubjects[$j]);
                $score->setScore(random_int(0, 10));

                $manager->persist($score);
            }
        }

        $manager->flush();
    }
}
