<?php

namespace Tasklist\Task\Controller;

use DateTimeImmutable;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tasklist\Common\Controller\ApiValidationErrorsControllerTrait;
use Tasklist\Task\Dto\TaskInputDto;
use Tasklist\Task\Entity\Board;
use Tasklist\Task\Entity\Status;
use Tasklist\Task\Entity\Task;
use Tasklist\Task\Repository\StatusRepository;
use Tasklist\Task\Service\TaskCrudManger;
use Tasklist\User\Entity\User;

/**
 * TasksController.
 */
class TasksController extends AbstractFOSRestController
{
    use ApiValidationErrorsControllerTrait;

    /**
     * Token storage.
     *
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Task CRUD manger.
     *
     * @var TaskCrudManger
     */
    private $taskCrudManger;

    /**
     * Validator.
     *
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * TasksController constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param TaskCrudManger $taskCrudManger
     * @param ValidatorInterface $validator
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        TaskCrudManger $taskCrudManger,
        ValidatorInterface $validator
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->taskCrudManger = $taskCrudManger;
        $this->validator = $validator;
    }

    /**
     * Create task.
     *
     * @param Board $board
     * @param TaskInputDto $taskInput
     *
     * @return View
     *
     * @ParamConverter("board")
     * @ParamConverter("taskInput", converter="fos_rest.request_body")
     *
     * @OA\Parameter(
     *     name="board",
     *     in="path",
     *     type="integer",
     *     description="Board id."
     * )
     * @OA\Parameter(
     *     name="taskInput",
     *     in="body",
     *     description="Task details.",
     *     @Model(type=BoardInputDto::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns newly created task.",
     *     @Model(type=Task::class)
     * )
     */
    public function postBoardTaskAction(Board $board, TaskInputDto $taskInput): View
    {
        try {
            /* @var $user User */
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user !== $board->getOwner()) {
                throw new AccessDeniedHttpException('Creating task on other user\'s board is forbidden');
            }

            $validationErrors = $this->validator->validate($taskInput);

            if ($validationErrors->count()) {
                return $this->createValidationErrorsResponse($validationErrors);
            }

            return $this->view(
                $this->taskCrudManger->createFromDto($taskInput, $board)
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Update status for task.
     *
     * @param Board $board
     * @param Task $task
     * @param Status $status
     *
     * @return View
     *
     * @ParamConverter("board")
     * @ParamConverter("task")
     * @ParamConverter("status")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns task with updated status.",
     *     @Model(type=Task::class)
     * )
     */
    public function patchBoardTaskStatusAction(Board $board, Task $task, Status $status): View
    {
        try {
            /* @var $user User */
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user !== $board->getOwner()) {
                throw new AccessDeniedHttpException('Updating task status on other user\'s board is forbidden');
            }

            return $this->view(
                $this->taskCrudManger->updateStatus($task, $status)
            );
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Delete task.
     *
     * @param Board $board
     * @param Task $task
     *
     * @return View
     *
     * @ParamConverter("board")
     * @ParamConverter("task")
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns task with updated status.",
     *     @Model(type=Task::class)
     * )
     */
    public function deleteBoardTaskAction(Board $board, Task $task): View
    {
        try {
            /* @var $user User */
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user !== $board->getOwner()) {
                throw new AccessDeniedHttpException('Removing task on other user\'s board is forbidden');
            }

            $this->taskCrudManger->delete($task);

            return $this->view([], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }
}
