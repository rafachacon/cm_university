<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Entity\Student2Subject;
use App\Entity\Subject;
use App\Entity\Student;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Class ApiController
 *
 * @Route("/api")
 */
class ApiController extends FOSRestController
{
    /**
     * @Rest\Get("/v1/subjects.{_format}", name="subjects_list_all", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all subjects of cm_university."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all subjects."
     * )
     *
     *
     * @SWG\Tag(name="Subjects")
     */
    public function getSubjectsAction()
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $data = [];
        $message = '';

        try {
            $code = 201;
            $error = false;

            $data = $em->getRepository('App:Subject')->findBy([], ['id' => 'ASC']);
        } catch (Exception $e) {
            $code = 500;
            $error = true;
            $message = 'An error has occurred: '.$e->getMessage();
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $data,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/v1/subject/{subjectId}.{_format}", name="get_subject", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets subject given by ID."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The subject ID was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The subject ID"
     * )
     *
     *
     * @SWG\Tag(name="Subjects")
     *
     * @param $subjectId integer
     *
     * @return Response
     */
    public function getSubjectAction($subjectId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $data = [];
        $message = "";

        try {
            $code = 200;
            $error = false;

            $data = $em->getRepository("App:Subject")->find($subjectId);

            if ($data == null) {
                throw new Exception('The subject does not exist.');
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the subject - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $data,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/v1/subject.{_format}", name="add_subject", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="Subject was added successfully"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error was occurred trying to add a new subject"
     * )
     *
     * @SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="The subject name",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Subjects")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addSubjectAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $subject = [];
        $message = "";

        try {
            $code = 201;
            $error = false;
            $name = $request->request->get("name", null);

            if ($name != null) {
                $subject = new Subject();
                $subject->setName($name);

                $em->persist($subject);
                $em->flush();

            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to add a new subject - Error: You must provide a name";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to add a new subject - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $subject,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/v1/subject/{subjectId}.{_format}", name="update_subject", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The subject was updated successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to update the subject."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The subject ID"
     * )
     *
     * @SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="The subject name",
     *     schema={}
     * )
     *
     *
     * @SWG\Tag(name="Subjects")
     *
     * @param Request $request
     * @param integer $subjectId
     *
     * @return Response
     */
    public function updateSubjectAction(Request $request, $subjectId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $subject = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $name = $request->request->get("name", null);
            $subject = $em->getRepository("App:Subject")->find($subjectId);

            if (!is_null($name) && !is_null($subject)) {
                $subject->setName($name);

                $em->persist($subject);
                $em->flush();

            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to update the subject - Error: You must to provide a name or the subject does not exist";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to update the subject - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $subject,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Delete("/v1/subject/{subjectId}.{_format}", name="delete_subject", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Subject successfully deleted"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to delete the subject"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The subject ID"
     * )
     *
     * @SWG\Tag(name="Subjects")
     *
     * @param integer $subjectId
     *
     * @return Response
     */
    public function deleteSubjectAction($subjectId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();

        try {
            $code = 200;
            $error = false;
            $subject = $em->getRepository("App:Subject")->find($subjectId);

            if ($subject != null) {
                $em->remove($subject);
                $em->flush();

                $message = "The subject was deleted successfully!";

            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to delete the subject - Error: The subject does not exist";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the subject - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/v1/student/{studentId}.{_format}", name="get_subject", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets student given by ID."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The student ID was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The student ID"
     * )
     *
     *
     * @SWG\Tag(name="Students")
     *
     * @param $studentId integer
     *
     * @return Response
     */
    public function getStudentAction($studentId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $data = [];
        $message = "";

        try {
            $code = 200;
            $error = false;

            $data = $em->getRepository("App:Student")->find($studentId);

            if ($data == null) {
                throw new Exception('The student does not exist.');
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the student - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $data,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/v1/students.{_format}", name="students_list_all", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all students of cm_university."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all students."
     * )
     *
     *
     * @SWG\Tag(name="Students")
     */
    public function getStudentsAction()
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $data = [];
        $message = '';

        try {
            $code = 201;
            $error = false;

            $data = $em->getRepository('App:Student')->findAll();
        } catch (Exception $e) {
            $code = 500;
            $error = true;
            $message = 'An error has occurred: '.$e->getMessage();
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $data,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/v1/student.{_format}", name="add_student", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="Student was added successfully"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error was occurred trying to add a new student"
     * )
     *
     * @SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="The student name",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Students")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addStudentAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $student = [];
        $message = "";

        try {
            $code = 201;
            $error = false;
            $name = $request->request->get("name", null);
            $phones = $request->request->get("phones", null);

            if ($name != null) {
                $student = new Student();
                $student->setName($name);

                $em->persist($student);

                if ($phones != null) {
                    foreach ($phones as $phoneItem) {
                        $phone = new Phone();
                        $phone->setName($phoneItem['name']);
                        $phone->setNumber($phoneItem['number']);
                        $phone->setStudent($student);

                        $em->persist($phone);
                    }
                }

                $em->flush();

            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to add a new student - Error: You must provide a name";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to add a new student - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $student,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/v1/student/{studentId}.{_format}", name="update_student", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The student was updated successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to update the student."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The student ID"
     * )
     *
     * @SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="The student name",
     *     schema={}
     * )
     *
     *
     * @SWG\Tag(name="Students")
     *
     * @param Request $request
     * @param integer $studentId
     *
     * @return Response
     */
    public function updateStudentAction(Request $request, $studentId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $student = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $name = $request->request->get("name", null);
            $student = $em->getRepository("App:Student")->find($studentId);

            if ($name != null) {
                $student->setName($name);

                $em->persist($student);
                $em->flush();

            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to update the student - Error: You must to provide a name or the student does not exist";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to update the student - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $student,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Delete("/v1/student/{studentId}.{_format}", name="delete_student", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Student successfully deleted"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to delete the student"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The student ID"
     * )
     *
     * @SWG\Tag(name="Students")
     *
     * @param integer $studentId
     *
     * @return Response
     */
    public function deleteStudentAction($studentId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();

        try {
            $code = 200;
            $error = false;
            $student = $em->getRepository("App:Student")->find($studentId);

            if ($student != null) {
                $em->remove($student);
                $em->flush();

                $message = "The student was deleted successfully!";

            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to delete the student - Error: The student does not exist";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the student - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/v1/student/{studentId}/phone.{_format}", name="add_phone", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="Phone was added successfully"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error was occurred trying to add a new phone"
     * )
     *
     * @SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="The phone name",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="number",
     *     in="body",
     *     type="string",
     *     description="The phone number",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Students")
     *
     * @param Request $request
     * @param integer $studentId
     *
     * @return Response
     */
    public function addPhoneAction(Request $request, $studentId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $phone = [];
        $message = "";

        try {
            $code = 201;
            $error = false;
            $name = $request->request->get("name", null);
            $number = $request->request->get("number", null);
            $student = $em->getRepository('App:Student')->find($studentId);

            if ($name != null && $number != null && $student != null) {
                $phone = new Phone();
                $phone->setName($name);
                $phone->setNumber($number);
                $phone->setStudent($student);


                $em->persist($phone);
                $em->flush();

            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to add a new phone - Error: You must provide a name and a number";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to add a new phone - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $phone,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/v1/phone/{phoneId}.{_format}", name="update_phone", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The phone was updated successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to update the phone."
     * )
     *
     * @SWG\Parameter(
     *     name="name",
     *     in="path",
     *     type="string",
     *     description="The phone name"
     * )
     *
     * @SWG\Parameter(
     *     name="number",
     *     in="body",
     *     type="string",
     *     description="The phone number",
     *     schema={}
     * )
     *
     *
     * @SWG\Tag(name="Students")
     *
     * @param Request $request
     * @param integer $phoneId
     *
     * @return Response
     */
    public function updatePhoneAction(Request $request, $phoneId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $phone = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $name = $request->request->get("name", null);
            $number = $request->request->get("number", null);
            $phone = $em->getRepository("App:Phone")->find($phoneId);

            if ($name != null && $number != null && $phone != null) {
                $phone->setName($name);
                $phone->setNumber($number);

                $em->persist($phone);
                $em->flush();

            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to update the phone - Error: You must to provide a name or the subject does not exist";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to update the phone - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $phone,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Delete("/v1/phone/{phoneId}.{_format}", name="delete_phone", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Phone successfully deleted"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to delete the phone"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The phone ID"
     * )
     *
     * @SWG\Tag(name="Students")
     *
     * @param integer $phoneId
     *
     * @return Response
     */
    public function deletePhoneAction($phoneId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();

        try {
            $code = 200;
            $error = false;
            $phone = $em->getRepository("App:Phone")->find($phoneId);

            if ($phone != null) {
                $em->remove($phone);
                $em->flush();

                $message = "The phone was deleted successfully!";

            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to delete the phone - Error: The phone does not exist";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the phone - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/v1/score/{studentId}", name="add_score")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Score was added successfully"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error was occurred trying to add a score"
     * )
     *
     *
     * @SWG\Tag(name="Students")
     *
     * @param Request $request
     * @param integer $studentId
     *
     * @return Response
     */
    public function addScoreAtion(Request $request, $studentId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $studentScore = [];
        $message = "";

        try {
            $code = 201;
            $error = false;
            $scores = $request->request->get('scores', null);
            $student = $em->getRepository('App:Student')->find($studentId);

            if ($scores != null && $student != null) {
                foreach ($scores as $scoreItem) {
                    $studentScore = new Student2Subject();
                    $studentScore->setStudent($student);
                    $subject = $em->getRepository('App:Subject')->find($scoreItem['subjectId']);
                    $studentScore->setSubject($subject);
                    $studentScore->setScore($scoreItem['score']);

                    $em->persist($studentScore);
                }

                $em->flush();

            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to add a new score - Error: You must provide a student";
            }

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to add a new score - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $studentScore,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/v1/score/{studentId}.{_format}", name="student_score", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets score for the student given by ID."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The student ID was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The student ID"
     * )
     *
     *
     * @SWG\Tag(name="Students")
     *
     * @param $studentId integer
     *
     * @return Response
     */
    public function getScoreAction($studentId)
    {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $scoreSummary = [];
        $data = [];
        $message = "";
        $avgScore = 0;

        try {
            $code = 200;
            $error = false;

            $score = $em->getRepository("App:Student2Subject")->findBy(
                [
                    "student" => $studentId,
                ]
            );

            if ($score == null) {
                throw new Exception('The student does not exist.');
            }

            $sumScore = 0;
            $total = 0;
            foreach ($score as $scoreItem) {
                $scoreSummary[] = [
                    'subject' => $scoreItem->getSubject()->getName(),
                    'score'   => $scoreItem->getScore(),
                ];

                $total++;
                $sumScore += $scoreItem->getScore();
            }
            if ($total > 0) {
                $avgScore = $sumScore / $total;
            }

            $data = [
                'student' => $score[0]->getStudent()->getName(),
                'score'   => $scoreSummary,
                'average' => $avgScore,
            ];


        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the score - Error: {$ex->getMessage()}";
        }

        $response = [
            'code'  => $code,
            'error' => $error,
            'data'  => $error ? $message : $data,
        ];

        return new Response($serializer->serialize($response, "json"));
    }
}
